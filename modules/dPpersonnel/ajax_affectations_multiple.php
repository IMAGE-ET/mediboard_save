<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

global $can;
$can->needsAdmin();

$_multiple = array();
$object = mbGetObjectFromGet(null, null, "object_guid");

$affectation = new CAffectationPersonnel();
$affectation->setObject($object);
$affectation->personnel_id = CValue::get("personnel_id");
$affectation->loadRefPersonnel();

$_multiple["object"]       = $affectation->_ref_object;
$_multiple["personnel"]    = $affectation->_ref_personnel;
$_multiple["affectations"] = $affectation->loadMatchingList();
$_multiple["affect_count"] = count($_multiple["affectations"]);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("_multiple", $_multiple);

$smarty->display("inc_affectations_multiple.tpl");
