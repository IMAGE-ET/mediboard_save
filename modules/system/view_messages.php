<?php /* $Id: view_messages.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: 1.2 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("system", "message"));

global $mp_status;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// R�cup�ration du message � ajouter/�diter
$message = new CMessage;
$message->load(mbGetValueFromGetOrSession("message_id"));
$message->loadRefs();

// R�cup�ration de la liste des messages
$filter_status = mbGetValueFromGetOrSession("filter_status");
$messages = new CMessage;
$messages = $messages->loadPublications($filter_status);

// Cr�ation du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('message', $message);
$smarty->assign('messages', $messages);
$smarty->assign('mp_status', $mp_status);
$smarty->assign('filter_status', $filter_status);

$smarty->display('view_messages.tpl');

?>