<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Chargement du user_courant
$user = CMediusers::get();
$is_praticien = $user->isPraticien();

$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");
$praticien_id = CValue::get("praticien_id", $user->user_id);
$suffixe = CValue::get("suffixe");

$prescription = new CPrescription();

if (!$object_id || !$object_class){
	return; 
}

$prescriptions = array();


// Chargement de l'objet de la prescription
$object = new $object_class;
$object->load($object_id);

$totals_by_chapitre = array();
if ($object->_id){
	// Chargement des prescriptions
  $prescriptions = $object->loadBackRefs("prescriptions");
  if($prescriptions){
	  foreach ($prescriptions as &$_prescription){
	  	// Chargement du nombre d'elements pour chaque prescription
	  	$_prescription->loadRefPraticien();
	  	$_prescription->countLinesMedsElements();
	  	
	  	foreach ($_prescription->_counts_by_chapitre as $chapitre => $count) {
	  	  @$totals_by_chapitre[$chapitre]+= $count;
	  	}
	  }
  }
}




$type ="";
if($object_class == "CSejour"){
	$type = "pre_admission";
}
if($object_class == "CConsultation"){
	$type = "externe";
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("is_praticien"       , $is_praticien);
$smarty->assign("today"              , mbDate());
$smarty->assign("totals_by_chapitre" , $totals_by_chapitre);
$smarty->assign("object_id"          , $object_id);
$smarty->assign("object_class"       , $object_class);
$smarty->assign("suffixe"            , $suffixe);
$smarty->assign("praticien_id"       , $praticien_id);
$smarty->assign("prescriptions"      , $prescriptions);
$smarty->assign("type", $type);
$smarty->display("inc_widget_prescription.tpl");

?>