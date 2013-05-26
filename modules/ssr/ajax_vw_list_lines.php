<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Initialisation de la variable permettant de ne pas passer par les alertes manuelles
if (CModule::getActive("dPprescription")) {
  CPrescriptionLine::$contexte_recent_modif = 'ssr';
}

$prescription_id = CValue::get("prescription_id");
$category_id = CValue::get("category_id");
$full_line_id = CValue::get("full_line_id");

$lines = array();

$line = new CPrescriptionLineElement();
$order = "debut ASC";
$ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
$where["prescription_id"] = " = '$prescription_id'";
$where["element_prescription.category_prescription_id"] = " = '$category_id'";
$_lines[$category_id] = $line->loadList($where, $order, null, null, $ljoin);

foreach ($_lines[$category_id] as $_line) {
  $lines[$category_id][$_line->element_prescription_id][] = $_line;	
}

$current_user = CMediusers::get();
$can_edit_prescription = $current_user->isPraticien() || $current_user->isAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("full_line_id", $full_line_id);
$smarty->assign("lines", $lines);
$smarty->assign("category_id", $category_id);
$smarty->assign("nodebug", true);
$smarty->assign("can_edit_prescription", $can_edit_prescription);
$smarty->display("inc_list_lines.tpl");
