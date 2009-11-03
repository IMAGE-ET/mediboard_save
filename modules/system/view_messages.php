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

// R�cup�ration du message � ajouter/�diter
$message = new CMessage;
$message->deb = mbDateTime();
$message->load(CValue::getOrSession("message_id"));
$message->loadRefs();

// R�cup�ration de la liste des messages
$filter = new CMessage;
$filter->_status = CValue::getOrSession("_status");
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