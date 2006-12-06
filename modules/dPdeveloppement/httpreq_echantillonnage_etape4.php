<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $m;

$_nb_services = mbGetValueFromGet("_nb_services", null);

$list_5 = mbArrayCreateRange(1,5, true);
$list_14 = mbArrayCreateRange(1,14, true);
$list_20 = mbArrayCreateRange(1,20, true);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("_nb_services" , $_nb_services);
$smarty->assign("list_5"       , $list_5);
$smarty->assign("list_14"      , $list_14);
$smarty->assign("list_20"      , $list_20);

$smarty->display("inc_echantillonnage_etape4.tpl");
?>