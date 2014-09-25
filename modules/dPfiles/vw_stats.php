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

// Get Concrete class
$doc_class = CValue::get("doc_class", "CFile");
if (!is_subclass_of($doc_class, "CDocumentItem")) {
  trigger_error("Wrong '$doc_class' won't inerit from CDocumentItem", E_USER_ERROR);
  return;
}

$func = new CFunctions();

/** @var CDocumentItem $doc */
$doc = new $doc_class;
$users_stats = $doc->getUsersStats();
$funcs_stats = array();
$groups_stats = array();

$total = array(
  "docs_weight" => 0,
  "docs_count" => 0,
);

// Stat per user
foreach ($users_stats as &$_stat_user) {
  $total["docs_weight"] += $_stat_user["docs_weight"];
  $total["docs_count"]  += $_stat_user["docs_count"];

  $_stat_user["_docs_average_weight"] = $_stat_user["docs_weight"] / $_stat_user["docs_count"];

  // Make it mediusers uninstalled compliant
  if (CModule::getActive("mediusers")) {
    // Get the owner
    /** @var CMediusers $user */
    $user = new CMediusers();
    $user = $user->getCached($_stat_user["owner_id"]);
    $_stat_user["_ref_owner"] = $user;

    // Initilize function data
    $function = $user->loadRefFunction();
    if (!isset($funcs_stats[$function->_id])) {
      $funcs_stats[$function->_id] = array(
        "docs_weight" => 0,
        "docs_count"  => 0,
        "_ref_owner"  => $function,
      );
    }
    
    // Cummulate data per function
    $stat_func =& $funcs_stats[$function->_id];
    $stat_func["docs_weight"] += $_stat_user["docs_weight"];
    $stat_func["docs_count" ] += $_stat_user["docs_count" ];

    // Initilize group data
    $group = $function->loadRefGroup();
    if (!isset($groups_stats[$group->_id])) {
      $groups_stats[$group->_id] = array(
        "docs_weight" => 0,
        "docs_count"  => 0,
        "_ref_owner"  => $group,
      );
    }

    // Cummulate data per group
    $stat_group =& $groups_stats[$group->_id];
    $stat_group["docs_weight"] += $_stat_user["docs_weight"];
    $stat_group["docs_count" ] += $_stat_user["docs_count" ];
  }
}

// Get user data percentages
foreach ($users_stats as &$_stat_user) {
  $_stat_user["_docs_weight_percent"] = $_stat_user["docs_weight"] / $total["docs_weight"];
  $_stat_user["_docs_count_percent" ] = $_stat_user["docs_count" ] / $total["docs_count" ];
}

// Get function data percentages
foreach ($funcs_stats as $function_id => &$_stat_func) {
  $_stat_func["_docs_weight_percent"] = $_stat_func["docs_weight"] / $total["docs_weight"];
  $_stat_func["_docs_count_percent" ] = $_stat_func["docs_count" ] / $total["docs_count" ];
  $_stat_func["_docs_average_weight"] = $_stat_func["docs_weight"] / $_stat_func["docs_count"];
}

// Get function data percentages
foreach ($groups_stats as $group_id => &$_stat_group) {
  $_stat_group["_docs_weight_percent"] = $_stat_group["docs_weight"] / $total["docs_weight"];
  $_stat_group["_docs_count_percent" ] = $_stat_group["docs_count" ] / $total["docs_count" ];
  $_stat_group["_docs_average_weight"] = $_stat_group["docs_weight"] / $_stat_group["docs_count"];
}

$total["_docs_average_weight"] = $total["docs_count"] ? ($total["docs_weight"] / $total["docs_count"]) : 0;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("doc_class", $doc_class);
$smarty->assign("users_stats", $users_stats);
$smarty->assign("funcs_stats", $funcs_stats);
$smarty->assign("groups_stats", $groups_stats);
$smarty->assign("total", $total);
$smarty->display("vw_stats.tpl");
