<?php /* $Id: view_identifiants.php 6141 2009-04-21 14:19:23Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 6141 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$trigger_classes = CMouvFactory::getClasses();

// Selected mark
$mark = new CTriggerMark();
$mark->load(CValue::getOrSession("mark_id"));

// filtered marks
$filter = new CTriggerMark();
$where = array();
if ($filter->trigger_class = CValue::getOrSession("trigger_class")) {
  $where["trigger_class"] = "= '$filter->trigger_class'"; 
}

if ($filter->trigger_number = CValue::getOrSession("trigger_number")) {
  $where["trigger_number"] = "= '$filter->trigger_number'"; 
}

if ($filter->mark = CValue::getOrSession("mark")) {
  $where["mark"] = "= '$filter->mark'"; 
}

if ("" !== $filter->done = CValue::getOrSession("done", "")) {
  $where["done"] = "= '$filter->done'"; 
}

$count = $filter->countList($where);
$marks = $filter->loadList($where, "trigger_number DESC", 100);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("trigger_classes", $trigger_classes);
$smarty->assign("mark", $mark);
$smarty->assign("filter", $filter);
$smarty->assign("marks", $marks);
$smarty->assign("count", $count);
$smarty->display("view_marks.tpl");

?>
