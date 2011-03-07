<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date = CValue::getOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $can->edit || $date >= mbDate();

// Selection des plages op�ratoires de la journ�e
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

// Listes des op�rations
$listEntree = new COperation;
$where = array();
$where[] = "`plageop_id` ".CSQLDataSource::prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_bloc"] = "IS NULL";
$order = "time_operation";
$listEntree = $listEntree->loadList($where,$order);
foreach($listEntree as $key => $value) {
	$oper =& $listEntree[$key];
  $oper->loadRefsFwd();
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listEntree" , $listEntree);
$smarty->assign("date"       , $date);
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("vw_brancardage.tpl");
?>