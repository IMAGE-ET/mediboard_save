<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$mean_fields = array(
  "age",
  "wash_volume",
  "saved_volume",
  "transfused_volume",
  "hgb_pocket",
  "hgb_patient",
);

$possible_filters = array_merge(
  array('chir_id', 'anesth_id', 'codes_ccam', 'code_asa', 'cell_saver_id'),
  $mean_fields
);

$filters          = CValue::getOrSession('filters', array());
$months_count     = CValue::getOrSession('months_count', 12);
$months_relative  = CValue::getOrSession('months_relative', 0);
$comparison       = CValue::getOrSession('comparison', $possible_filters);
$comparison_left  = CValue::getOrSession('comparison_left');
$comparison_right = CValue::getOrSession('comparison_right');
$mode             = CValue::get('mode');

foreach ($possible_filters as $n) {
  if (!isset($filters[$n])) $filters[$n] = null;
}

$cell_saver = new CCellSaver;
$cell_savers = $cell_saver->loadList(null, "marque, modele");

$user = new CMediusers();
$user->load(CAppUI::$instance->user_id);

$mediuser = new CMediusers();
$fields = array(
  "anesth_id"  => $mediuser->loadListFromType(array('Anesth�siste')),
  "chir_id"    => $mediuser->loadListFromType(array('Chirurgien'), ($user->isAnesth() ? null : PERM_READ)),
  "codes_asa"  => range(1, 5),
  "cell_saver_id" => $cell_savers,
);

$smarty = new CSmartyDP();

// Filter
$smarty->assign('filters',         $filters);
$smarty->assign('months_relative', $months_relative);
$smarty->assign('months_count',    $months_count);
$smarty->assign('mode',            $mode);

// Lists
$smarty->assign('fields', $fields);
$smarty->assign('mean_fields', $mean_fields);

$smarty->display('vw_stats.tpl');
