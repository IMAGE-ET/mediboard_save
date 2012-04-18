<?php

/**
 * aphmOdonto
 *  
 * @category aphmOdonto
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

ignore_user_abort(true);

$printer_id   = CValue::get("printer_id");
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);

$patient = new CPatient;

$fields = array();

$object->completeLabelFields($fields);

// Chargement des mod�les d'�tiquettes
$modele_etiquette = new CModeleEtiquette;

$where = array();

$where['object_class'] = " = '$object_class'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

if (count($modeles_etiquettes = $modele_etiquette->loadList($where))) {
  // TODO: faire une modale pour proposer les mod�les d'�tiquettes
  $first_modele = reset($modeles_etiquettes);
  $first_modele->completeLabelFields($fields);
  $first_modele->replaceFields($fields);
  $first_modele->printEtiquettes($printer_id);
}
else {
  CAppUI::stepAjax("Aucun mod�le d'�tiquette configur� pour l'objet " . CAppUI::tr($object_class));
}
?>