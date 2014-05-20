<?php

/**
 * S�lecteur de mod�le
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$praticien_id = CValue::getOrSession("praticien_id");

// Chargement de l'objet
$object = new $object_class;
/** @var $object CMbObject */
$object->load($object_id);

// Chargement du praticien concern� et des praticiens disponibles
$praticien = CMediusers::get($praticien_id);
$praticien->canDo();
$praticiens = $praticien->loadPraticiens(PERM_EDIT);

// Chargement des objets relatifs a l'objet charg�
$templateClasses = $object->getTemplateClasses();

// Chargement des modeles de consultations du praticien
$modelesCompat = array();
$modelesNonCompat = array();

// Chargement des modeles pour chaque classe, pour les praticiens et leur fonction
foreach ($templateClasses as $class => $id) {
  $modeles = CCompteRendu::loadAllModelesFor($praticien->_id, 'prat', $class, 'body');
  if ($id) {
    $modelesCompat[$class] = $modeles;
    continue;
  }
  $modelesNonCompat[$class] = $modeles;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"       , $praticien);
$smarty->assign("praticiens"      , $praticiens);
$smarty->assign("target_id"       , $object->_id);
$smarty->assign("target_class"    , $object->_class);

$smarty->assign("modelesCompat"   , $modelesCompat);
$smarty->assign("modelesNonCompat", $modelesNonCompat);
$smarty->assign("modelesId"       , $templateClasses);

$smarty->display("modele_selector.tpl");
