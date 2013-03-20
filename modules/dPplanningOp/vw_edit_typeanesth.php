<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can;

$can->needsAdmin();

$show_inactive = CValue::getOrSession("inactive", 0);


// Liste des Type d'anesth�sie
$type_anesth = new CTypeAnesth;
$where = array(
  "actif" =>  ($show_inactive) ? " IN ('0','1')" : " = '1' "
);
$types_anesth = $type_anesth->loadList($where, "name");
foreach ($types_anesth as &$_type_anesth) {
  $_type_anesth->countOperations();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("types_anesth", $types_anesth);
$smarty->assign("show_inactive", $show_inactive);
$smarty->display("vw_edit_typeanesth.tpl");