<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien M�nager
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');

$destockages = array();
$lines = array();

// Chargement de toutes les administrations dans la periode donnee
$administration = new CAdministration();
$where = array();
$where["dateTime"] = "BETWEEN '$date_min' AND '$date_max'"; 
$where["object_class"] = " = 'CPrescriptionLineMedicament'";
$administrations = $administration->loadList($where);

// Calcul des quantit�s administr�es
foreach($administrations as $_administration){
  $_administration->loadTargetObject();	
	$line =& $_administration->_ref_object;
  $code_cip = $line->code_cip;
	if(!isset($destockages[$code_cip]["quantite"])) {
		$destockages[$code_cip]["quantite"] = 0;
	}
  $destockages[$code_cip]["quantite"] += $_administration->quantite;
  // Tableaux de cache de medicaments
  if(!isset($medicaments[$code_cip])){
  	$line->_ref_produit->loadConditionnement();
    $medicaments[$code_cip] =& $line->_ref_produit;
  }
}

// Calcul du nombre de boites correspondant aux administrations
foreach($destockages as $code_cip => $_destockage){
  $medicament = $medicaments[$code_cip];
  $presentation = $destockages[$code_cip]["quantite"]/$medicament->nb_unite_presentation/$medicament->nb_presentation;
  if (!isset($destockages[$code_cip]["nb_produit"])){
    $destockages[$code_cip]["nb_produit"] = 0;
  }
  $destockages[$code_cip]["nb_produit"] = $presentation; 
  
  if(strstr($destockages[$code_cip]["nb_produit"], '.')){
    $destockages[$code_cip]["nb_produit"] = ceil($destockages[$code_cip]["nb_produit"]);
  }
  
  $destockages[$code_cip]["stock"] = CProductStockService::getFromCode($code_cip, $service_id);
  if ($destockages[$code_cip]["stock"]) {
    $destockages[$code_cip]["stock"]->quantity -= $destockages[$code_cip]["nb_produit"];
    
    $stock = $destockages[$code_cip]["stock"];
    $log = new CUserLog();
    $where = array();
    $order = "date DESC";
    $where["object_id"] = " = '$stock->_id'";
    $where["object_class"] = " = '$stock->_class_name'";
    $where["date"] = " BETWEEN '$date_min' AND '$date_max'";
    $where["fields"] = " LIKE '%quantity%'";
    
    $destockages[$code_cip]["stock"]->_ref_logs = $log->loadList($where, $order);
    foreach ($destockages[$code_cip]["stock"]->_ref_logs as $log) {
      $log->loadRefsFwd();
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('destockages', $destockages);
$smarty->display('inc_destockages_service_list.tpl');

?>