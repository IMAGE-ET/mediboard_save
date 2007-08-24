<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

global $can, $m, $AppUI, $dPconfig;

$can->needsAdmin();

$pratId    = mbGetValueFromGetOrSession("object_id");
$pratId400 = mbGetValueFromGetOrSession("id400");
$date = mbDateTime();

//Cr�ation d'un nouvel id400 pour le laboratoire
$newId400 = new CIdSante400();

$prat = new CMediusers();
$listPrat = $prat->loadPraticiens();

$config = $dPconfig[$m]["CCatalogueLabo"];
$remote_name = $config["remote_name"];

$id400 = new CIdSante400();
$id400->object_class = "CMediusers";
$id400->tag = $remote_name;
$order = "last_update DESC";

$list_idSante400 = $id400->loadMatchingList($order);

foreach ($list_idSante400 as $curr_idSante400) {
  $curr_idSante400->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listPrat", $listPrat);
$smarty->assign("date", $date);
$smarty->assign("remote_name", $remote_name);
$smarty->assign("newId400", $newId400);
$smarty->assign("list_idSante400",$list_idSante400);

$smarty->display("configure.tpl");

?>