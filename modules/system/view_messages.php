<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsRead();

$message_id = CValue::getOrSession("message_id");
$_status = CValue::getOrSession("_status");

// R�cup�ration du message � ajouter/�diter
$message = new CMessage;
$message->deb = mbDateTime();
$message->load($message_id);
$message->loadRefs();

// R�cup�ration de la liste des messages
$filter = new CMessage;
$filter->_status = $_status;
$messages = $filter->loadPublications($filter->_status);
foreach ($messages as $_message) {
	$_message->loadRefs();
}

$groups = CMediusers::loadEtablissements(PERM_EDIT);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("message",  $message);
$smarty->assign("messages", $messages);
$smarty->assign("groups",   $groups);
$smarty->assign("filter",   $filter);
$smarty->display("view_messages.tpl");
