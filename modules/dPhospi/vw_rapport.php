<?php  /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date");

$med = new CMediusers();
$listPrat = $med->loadPraticiens();

$dateEntree = mbDateTime("23:59:00", $date);
$dateSortie = mbDateTime("00:01:00", $date);

$hierEntree = mbDate("- 1 day", $dateEntree);
$hierEntree = mbDateTime("23:59:00", $hierEntree);

// Chargement des services
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ);

// Calcul du nombre d'affectations a la date $date
$affectation = new CAffectation();
$whereAffect["entree"] = "<= '$dateEntree'";
$whereAffect["sortie"] = ">= '$dateSortie'";
$whereAffect["sejour_id"] = "!= '0'";


// Inialisation des tableaux de stockage
$total_service = array();
foreach($services as $key=>$_service){
	$total_service[$_service->nom] = 0;
}
$total_prat = array();
foreach($listPrat as $key=>$prat){
	$totalPrat[$prat->_view]["hospi"] = 0;
	$totalPrat[$prat->_view]["ambu"] = 0;
	$totalPrat[$prat->_view]["total"] = 0;
}

$list_affectations = $affectation->loadList($whereAffect);

foreach($list_affectations as $key=>$_affectation){
   // Chargement des r�f�rences n�cessaire pour parcourir les affectations
   $_affectation->loadRefLit();
   $_affectation->_ref_lit->loadRefChambre();
   $_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
   $_affectation->loadRefSejour();
   $_affectation->_ref_sejour->loadRefPraticien();
   
   // Stockage des informations li�es aux services
   foreach($services as $key=>$_service){
	 if($_service->_id == $_affectation->_ref_lit->_ref_chambre->_ref_service->_id){
	   $total_service[$_service->nom]++;	
	 }
   }
   
   // Stockage des informations li�es au praticiens
   foreach($listPrat as $key=>$_prat){
   	 // Cas d'un sejour de type Ambulatoire
     if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id && $_affectation->_ref_sejour->type == "ambu"){
	   $totalPrat[$_prat->_view]["ambu"]++;	
	 } 
	 // Autres cas
   	 if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id && $_affectation->_ref_sejour->type == "comp"){
	   $totalPrat[$_prat->_view]["hospi"]++;
	 }
	 // Total des hospitalisations (Ambu + autres)
	 if($_prat->_id == $_affectation->_ref_sejour->_ref_praticien->_id){
	   $totalPrat[$_prat->_view]["total"] = $totalPrat[$_prat->_view]["ambu"] + $totalPrat[$_prat->_view]["hospi"]; 	
	 }
   } 
}

// present de la veille
$affectationVeille = new CAffectation();
$whereVeille["entree"] = "<= '$hierEntree'";
$whereVeille["sortie"] = ">= '$dateSortie'";
$whereVeille["sejour_id"] = "!= '0'";
$listPresentVeille = $affectationVeille->loadList($whereVeille);

// entree du jour
$date_debut = mbDateTime("00:01:00",$date);
$date_fin = mbDateTime("23:59:00",$date);
$affectationEntreeJour = new CAffectation();
$whereEntree["entree"] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereEntree["sejour_id"] = "!= '0'";
$listEntreeJour = $affectationEntreeJour->loadList($whereEntree);

// sorties du jour
$affectationSortieJour = new CAffectation();
$whereSortie["sortie"] = "BETWEEN '$date_debut' AND '$date_fin'";
$whereSortie["sejour_id"] = "!= '0'";
$listSortieJour = $affectationSortieJour->loadList($whereSortie);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date",$date);
$smarty->assign("services", $services);
$smarty->assign("list_affectations",$list_affectations);
$smarty->assign("total_service", $total_service);
$smarty->assign("listPresentVeille", $listPresentVeille);
$smarty->assign("listSortieJour",$listSortieJour);
$smarty->assign("listEntreeJour",$listEntreeJour);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("totalPrat",$totalPrat);
$smarty->display("vw_rapport.tpl");

?>