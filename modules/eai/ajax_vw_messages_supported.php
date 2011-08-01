<?php 
/**
 * Messages supported
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid     = CValue::getOrSession("actor_guid");
$exchange_class = CValue::getOrSession("exchange_class");

$data_format = new $exchange_class;
$messages = $data_format->getMessagesSupported($actor_guid);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("messages"  , $messages);
$smarty->assign("actor_guid", $actor_guid);
$smarty->display("inc_messages_supported.tpl");

?>