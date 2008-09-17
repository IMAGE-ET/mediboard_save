<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

$can->needsRead();

// R�cup�ration du message � ajouter/�diter
$message = new CMessage;
$message->deb = mbDateTime();
$message->load(mbGetValueFromGetOrSession("message_id"));
$message->loadRefs();

// R�cup�ration de la liste des messages
$filter_status = mbGetValueFromGetOrSession("filter_status");
$messages = new CMessage;
$messages = $messages->loadPublications($filter_status);
foreach ($messages as &$curr_message) {
	$curr_message->loadRefs();
}
$modules = CModule::$installed;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("message"      , $message      );
$smarty->assign("messages"     , $messages     );
$smarty->assign("modules"      , $modules      );
$smarty->assign("mp_status"    , CMessage::$status);
$smarty->assign("filter_status", $filter_status);

$smarty->display("view_messages.tpl");

?>