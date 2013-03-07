<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsaleOp
* @version $Revision$
* @author Alexis Granger
*/


global $a;

$praticien_id = CValue::getOrSession("praticien_id");
$date = CValue::getOrSession("date", CMbDT::date());
$dialog = CValue::get("dialog");
$tabOperations = array();
$tabOp = array();

$actes_ccam = array();
// Creation de la liste des praticiens
$praticiens = array();
// Tableau qui stocke le nombre d'acte non sign� par praticien
$nonSigne = array();
$operations = array();

// Si mode dialog, on efface les variables de tri
if($dialog){
  $praticien_id = "";
  $salle_id = "";
}

$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");

$praticien = new CMediusers();
$listPraticien = $praticien->loadPraticiens();

// Signature des actes en definissant un objet
if($dialog){
  // Recuperation de l'operation
	// Chargement de l'objet
	$object = new $object_class;
	$object->load($object_id);
	$object->loadView();
	$operations[$object->_id] = $object;
} else {
	
	// On parcourt les actes ccam
  $acte_ccam = new CActeCCAM();
  $where = array();
  
  if($praticien_id){
	  $where["executant_id"] = " = '$praticien_id'";
  }
  $where["execution"] = "LIKE '$date%'";
  $where["object_class"] = " = 'COperation'";
	$actes = $acte_ccam->loadList($where);
	
	foreach($actes as $key => $_acte){
		// Si l'operation n'est pas deja stock�e, on la charge et on la stocke
		if (!array_key_exists($_acte->object_id, $operations)){
		  $_acte->loadRefObject();
		  $operations[$_acte->object_id] = $_acte->_ref_object;
	  }
	  // Sinon, on stocke directement l'acte dans l'operation
	  $operations[$_acte->object_id]->_ref_actes_ccam[$_acte->_id] = $_acte;
	  $operations[$_acte->object_id]->loadRefsFwd();
	}
}


// Parcours du tableau d'operations, et stockage dans un tableau de salle
foreach($operations as $key => $op){
	// Classement des actes par executant
	foreach($op->_ref_actes_ccam as $key => $acte_ccam){
	  // Mise a jour de la liste des praticiens
	  if(!array_key_exists($acte_ccam->executant_id, $praticiens)){
	    $praticien = new CMediusers();
	    $praticien->load($acte_ccam->executant_id);
	    $praticien->loadRefFunction();
	    $praticiens[$acte_ccam->executant_id] = $praticien;
	    // initialisation du tableau d'actes non sign�s
	    $nonSigne[$acte_ccam->executant_id] = 0;
	  }  
	  // Chargement de l'executant de l'acte CCAM
	  $acte_ccam->loadRefExecutant();
	  // Chargement du tarif
	  $acte_ccam->getTarif();
	  
	  @$tabOperations[$op->_id][$acte_ccam->executant_id][$acte_ccam->_id] = $acte_ccam;
	  
	  if(!$acte_ccam->signe){
	    @$nonSigne[$op->_id][$acte_ccam->executant_id]++;
	  }
	}
}

ksort($tabOperations);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("tabOp", $tabOp);
$smarty->assign("date", $date);
$smarty->assign("nonSigne", $nonSigne);
$smarty->assign("redirectUrl", $a);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("dialog", $dialog);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("operations", $operations);
$smarty->assign("listPraticien", $listPraticien);
$smarty->assign("tabOperations", $tabOperations);
if($dialog){
  $smarty->assign("object", $object);
}
$smarty->assign("actes_ccam", $actes_ccam);

$smarty->display("vw_signature_actes.tpl");
?>