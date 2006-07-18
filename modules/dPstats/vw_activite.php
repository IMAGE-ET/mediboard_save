<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate());
$finact        = mbGetValueFromGetOrSession("finact", mbDate());

// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("debutact", $debutact);
$smarty->assign("finact", $finact);
$smarty->display("vw_activite.tpl");

?>