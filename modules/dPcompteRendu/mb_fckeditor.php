<?php /* $Id: $ */
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author S�bastien Fillonneau
*/

$templateManager = unserialize($_SESSION["dPcompteRendu"]["templateManager"]);

// Cr�ation du template
$smarty = new CSmartyDP("modules/dPcompteRendu");
$smarty->assign("templateManager", $templateManager);
$smarty->assign("nodebug", true);
$smarty->display("mb_fckeditor.tpl");
?>