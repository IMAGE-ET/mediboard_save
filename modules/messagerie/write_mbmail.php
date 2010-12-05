<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision$
* @author Thomas Despoix
*/
 
global $AppUI, $can;
$can->needsRead();

$mbmail = new CMbMail();
$mbmail->from    = $AppUI->user_id;
$mbmail->to      = CValue::get("to");
$mbmail->subject = CValue::get("subject");
$mbmail->load(CValue::getOrSession("mbmail_id"));
$mbmail->loadRefsFwd();

// V�rifiction de la premi�re lecture par le destinataire
if ($mbmail->to == $AppUI->user_id && $mbmail->date_sent && ! $mbmail->date_read) {
  $mbmail->date_read = mbDateTime();
  $mbmail->store();
}

$functions = CMediusers::loadFonctions();
foreach($functions as &$curr_func) {
  $curr_func->loadRefsUsers();
}

if ($mbmail->to) {
  $user_to = new CMediusers();
  $user_to->load($mbmail->to); 
  $user_to->loadRefFunction();
  if(!isset($functions[$user_to->_ref_function->_id])) {
    $functions[$user_to->_ref_function->_id] = $user_to->_ref_function;
  }
  if(!isset($functions[$user_to->_ref_function->_id]->_ref_users[$user_to->_id])) {
    $functions[$user_to->_ref_function->_id]->_ref_users[$user_to->_id] = $user_to;
  }
}

// Initialisation de FCKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "ckeditor";
$templateManager->simplifyMode = true;

if ($mbmail->date_sent) {
  $templateManager->printMode = true;
}

$templateManager->initHTMLArea();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mbmail", $mbmail);
$smarty->assign("functions" , $functions);

$smarty->display("write_mbmail.tpl");

?>