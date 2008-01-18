<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Chargement du praticien selectionne
$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$praticienSel = new CMediusers();
$praticienSel->load($praticien_id);

// Recuperation de l'id de la consultation du passage en urgence
$consult_urgence_id = mbGetValueFromGet("consult_urgence_id");

$consult = new CConsultation();
$chir = new CMediusers;
$pat = new CPatient;
$plageConsult = new CPlageconsult();

//Chargement des aides
$consult->loadAides($AppUI->user_id);

// L'utilisateur est-il praticien?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

// V�rification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$consultation_id = mbGetValueFromGetOrSession("consultation_id");
$plageconsult_id = mbGetValueFromGet("plageconsult_id", null);

$listModelePrat = array();
$listModeleFunc = array();

if(!$consultation_id) {
  // A t'on fourni une plage de consultation
  if($plageconsult_id){
    $plageConsult->load($plageconsult_id);    
  } else {
    // A t'on fourni l'id du praticien
    if($chir_id = mbGetValueFromGetOrSession("chir_id")) {
      $chir->load($chir_id);
    }

    // A t'on fourni l'id du patient
    if($pat_id = mbGetValueFromGet("pat_id")) {
      $pat->load($pat_id);
    }
  }
} else {
  $consult->load($consultation_id);
  $canConsult = $consult->canDo();
  
  $canConsult->needsRead("consultation_id");
  
  $consult->loadRefs();
  $consult->_ref_plageconsult->loadRefs();  

  $chir =& $consult->_ref_plageconsult->_ref_chir;
  $pat  =& $consult->_ref_patient;
  
	// Mod�les de l'utilisateur
	$order = "nom";
	if ($chir->user_id) {
	  $where = array();
	  $where["object_class"] = "= 'CConsultation'";
	  $where["chir_id"] = "= '$chir->user_id'";
	  $listModelePrat = CCompteRendu::loadModeleByCat(null, $where, $order, true);
	}
	
	// Mod�les de la fonction
	if ($chir->user_id) {
	  $where = array();
	  $where["object_class"] = "= 'CConsultation'";
	  $where["function_id"] = "= '$chir->function_id'";
	  $listModeleFunc = CCompteRendu::loadModeleByCat(null, $where, $order, true);
	}
}

$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$praticienSel->function_id'";
$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie,$orderCategorie);


// Creation du tableau de categories simplifi� pour le traitement en JSON
$listCat = array();
foreach($categories as $key => $cat){
  $listCat[$cat->_id] = $cat->nom_icone;
}


// Ajout du motif de la consultation pass� en parametre
if(!$consult->_id && $consult_urgence_id){
  // Chargement de la consultation de passage aux urgences
  $consultUrgence = new CConsultation();
  $consultUrgence->load($consult_urgence_id);
  $consultUrgence->loadRefSejour();
  $consultUrgence->_ref_sejour->loadRefRPU();
  $consult->motif = "Reconvocation suite � un passage aux urgences, {$consultUrgence->_ref_sejour->_ref_rpu->motif}";
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listCat"           , $listCat           );
$smarty->assign("categories"        , $categories        );
$smarty->assign("plageConsult"     	, $plageConsult      );
$smarty->assign("consult"           , $consult           );
$smarty->assign("chir"              , $chir              );
$smarty->assign("pat"               , $pat               );
$smarty->assign("listPraticiens"    , $listPraticiens    );
$smarty->assign("praticien_id"      , $praticien_id      );
$smarty->assign("listModelePrat"		, $listModelePrat    );
$smarty->assign("listModeleFunc"		, $listModeleFunc    );

$smarty->display("addedit_planning.tpl");

?>