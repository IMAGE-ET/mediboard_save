<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$object_guid = CValue::get("object_guid");
$object = CMbObject::loadFromGuid($object_guid);

$object->loadRefsFraisDivers();

$frais_divers = new CFraisDivers();
$frais_divers->setObject($object);
$frais_divers->quantite = 1;
$frais_divers->coefficient = 1;
$frais_divers->num_facture = 1;
$frais_divers->loadListExecutants();
$frais_divers->loadExecution();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("object"      , $object);
$smarty->assign("frais_divers", $frais_divers);

$smarty->display("inc_form_add_frais_divers.tpl");
