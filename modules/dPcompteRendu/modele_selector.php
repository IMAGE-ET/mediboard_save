<?php /* */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
* @author Fabien Menager
**/

// Chargement de l'objet
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$object = new $object_class;
$object->load($object_id);

// Chargement du praticien concern� et des praticiens disponibles
$praticien = CMediusers::get(CValue::getOrSession("praticien_id"));
$praticien->canDo();
$praticiens =$praticien->loadPraticiens(PERM_EDIT);

// Chargement des objets relatifs a l'objet charg�
$templateClasses = $object->getTemplateClasses();

// Chargement des modeles de consultations du praticien
$order = "nom";

$wherePrat = array();
$whereFunc = array();

$modelesCompat = array();
$modelesNonCompat = array();

// Chargement des modeles pour chaque classe, pour les praticiens et leur fonction
foreach($templateClasses as $class => $id) {
  $modeles = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $class, null, 0);
  if ($id) {
    $modelesCompat[$class] = $modeles;
  } 
  else {
    $modelesNonCompat[$class] = $modeles;
  }
}


  // Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticien", $praticien);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("target_id", $object->_id);
$smarty->assign("target_class", $object->_class);

$smarty->assign("modelesCompat", $modelesCompat);
$smarty->assign("modelesNonCompat", $modelesNonCompat);
$smarty->assign("modelesId", $templateClasses);

$smarty->display("modele_selector.tpl");

?>