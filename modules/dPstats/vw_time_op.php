<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

if(function_exists("date_default_timezone_set")) {
  date_default_timezone_set("UTC");
}

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$typeVue    = mbGetValueFromGetOrSession("typeVue", 0);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

if($typeVue){
  // Stat des temps de pr�paration
  include("inc_vw_timeop_prepa.php");
}else{
  // Stat des temps op�ratoires
  include("inc_vw_timeop_op.php");
}


// Cr�ation du template
$smarty = new CSmartyDP(1);

if(!$typeVue) {
  $smarty->assign("prat_id"  , $prat_id  );
  $smarty->assign("codeCCAM" , $codeCCAM );
  $smarty->assign("listPrats", $listPrats);
}

$smarty->assign("listTemps", $listTemps);
$smarty->assign("total", $total);
$smarty->assign("typeVue", $typeVue);

$smarty->display("vw_time_op.tpl");

?>