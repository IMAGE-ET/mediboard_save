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
$filter = new CMessage;
$filter->_status = mbGetValueFromGetOrSession("_status");
$messages = $filter->loadPublications($filter->_status);
foreach ($messages as $_message) {
	$_message->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("message" , $message      );
$smarty->assign("messages", $messages     );
//$smarty->assign("modules" , CModule::$installed);
$smarty->assign("filter"  , $filter);

$smarty->display("view_messages.tpl");

?>