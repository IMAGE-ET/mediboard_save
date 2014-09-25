<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Get concrete class
$doc_class = CValue::get("doc_class", "CFile");
if (!is_subclass_of($doc_class, "CDocumentItem")) {
  trigger_error("Wrong '$doc_class' won't inerit from CDocumentItem", E_USER_ERROR);
  return;
}


// Users
$user_ids = null;
if (CValue::get("owner_guid")) {
  $owner = mbGetObjectFromGet(null, null, "owner_guid");
  $user_ids = array();
  if ($owner instanceof CGroups) {
    foreach ($owner->loadBackRefs("functions") as $_function) {
      $user_ids = array_merge($user_ids, $_function->loadBackIds("users"));
    }
  }

  if ($owner instanceof CFunctions) {
    $user_ids = $owner->loadBackIds("users");
  }

  if ($owner instanceof CUser || $owner instanceof CMediusers) {
    $user_ids = array($owner->_id);
  }
}

// Query prepare
/** @var CDocumentItem $doc */
$doc = new $doc_class;
$user_details = $doc->getUsersStatsDetails($user_ids);

// Reorder and make totals
$details = array();
$class_totals = array();
$category_totals = array();
$big_totals = array(
  "count"  => 0,
  "weight" => 0,
);

foreach ($user_details as $_detail) {
  $docs_count   = $_detail["docs_count"];
  $docs_weight  = $_detail["docs_weight"];
  $object_class = $_detail["object_class"];
  $category_id  = $_detail["category_id"];
  
  // Reorder
  $details[$category_id][$object_class] = array(
    "count"  => $docs_count,
    "weight" => $docs_weight,
  );
  
  // Totals
  $report = error_reporting(0);
  $class_totals[$object_class]["count" ] += $docs_count;
  $class_totals[$object_class]["weight"] += $docs_weight;
  $category_totals[$category_id]["count" ] += $docs_count;
  $category_totals[$category_id]["weight"] += $docs_weight;
  $big_totals["count" ] += $docs_count;
  $big_totals["weight"] += $docs_weight;
  error_reporting($report);
}

// All categories
$category = new CFilesCategory();
$categories = $category->loadAll(array_keys($category_totals));

// All classes
$classes = array_keys($class_totals);

$periodical_details = $doc->getPeriodicalStatsDetails($user_ids);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("details", $details);
$smarty->assign("periodical_details", $periodical_details);
$smarty->assign("class_totals", $class_totals);
$smarty->assign("category_totals", $category_totals);
$smarty->assign("big_totals", $big_totals);
$smarty->assign("categories", $categories);
$smarty->assign("classes", $classes);
$smarty->display("stats_details.tpl");
