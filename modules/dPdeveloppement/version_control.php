<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien M�nager
*/

CCanDo::checkRead();

if (!extension_loaded("svn")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "extension-failed-needed", "svn");
  return;
}

CAppUI::stepMessage(UI_MSG_OK, "extension-msg-ok", "svn");


// Cr�ation du template
$smarty = new CSmartyDP();
// $smarty->assign("files", $files);
// $smarty->display("sniff_code.tpl");

?>