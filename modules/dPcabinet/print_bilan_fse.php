<?php /* $Id: print_compta.php 2321 2007-07-19 08:14:45Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 2321 $
* @author Thomas Despoix 
*/

global $can;

$can->needsEdit();

$filter = new CLmFSE();
$filter->_date_min = mbGetValueFromGet("_date_min", mbDate());
$filter->_date_max = mbGetValueFromGet("_date_max", mbDate("+1 day"));
$filter->S_FSE_CPS = mbGetValueFromGet("S_FSE_CPS", -1);
$filter->S_FSE_ETAT = mbGetValueFromGet("S_FSE_ETAT");

// Chargement des FSE
$fse = new CLmFSE();
$where = array();
$where["S_FSE_DATE_FSE"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

if ($filter->S_FSE_ETAT) {
  $where["S_FSE_ETAT"] = "= '$filter->S_FSE_ETAT'";
}

$fses = $fse->LoadList($where);

// Tri et calcul des cumuls
$base = array(
  "count" => 0,
  "S_FSE_TOTAL_FACTURE" => 0.0,
);
$cumuls = array();
$total = $base;

foreach ($fses as $fse) {
  $days[$fse->S_FSE_DATE_FSE][$fse->_id] = $fse;
  
  $cumul =& $cumuls[$fse->S_FSE_DATE_FSE];
  if (!$cumul) {
    $cumul = $base;
  }
  
  if ($fse->_annulee) {
    continue;
  }
  
  $cumul["count"]++;
  $cumul["S_FSE_TOTAL_FACTURE"] += $fse->S_FSE_TOTAL_FACTURE;
  $total["count"]++;
  $total["S_FSE_TOTAL_FACTURE"] += $fse->S_FSE_TOTAL_FACTURE;
}

// Chargement du praticien 
$prat = new CMediusers();
$prat->loadFromIdCPS($filter->S_FSE_CPS);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("filter", $filter);
$smarty->assign("total", $total);
$smarty->assign("days", $days);
$smarty->assign("cumuls", $cumuls);
$smarty->assign("fses", $fses);
$smarty->assign("prat", $prat);
$smarty->display("print_bilan_fse.tpl");

?>