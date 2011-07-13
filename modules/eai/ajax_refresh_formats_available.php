<?php 
/**
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid = CValue::getOrSession("actor_guid");

$formats_xml  = $formats_tabular  = array();
$messages_xml = $messages_tabular = array();

$actor = CMbObject::loadFromGuid($actor_guid);
// Exp�diteur d'int�gration
if ($actor instanceof CInteropSender) {
  $formats_xml = CExchangeDataFormat::getAll("CEchangeXML");
  foreach ($formats_xml as &$_format_xml) {
    $_format_xml = new $_format_xml;

    $temp = $_format_xml->getMessagesSupported($actor_guid, false, null, true);
    $messages_xml = array_merge($messages_xml, $temp);
  }
  
  $formats_tabular = CExchangeDataFormat::getAll("CExchangeTabular");
  foreach ($formats_tabular as &$_format_tabular) {
    $_format_tabular = new $_format_tabular;
    
    $temp = $_format_tabular->getMessagesSupported($actor_guid, false, null, true);
    $messages_tabular = array_merge($messages_tabular, $temp);
  }
}
// Destinataire d'int�gration 
else if($actor instanceof CInteropReceiver) {
  $actor->makeBackSpec("echanges");
  $data_format = new $actor->_backSpecs["echanges"]->class;
  
  if ($data_format instanceof CExchangeTabular) {
    $formats_tabular [] = $data_format;
    $temp = $data_format->getMessagesSupported($actor_guid, false, null, true);
    $messages_tabular = array_merge($messages_tabular, $temp);
  }
  
  if ($data_format instanceof CEchangeXML) {
    $formats_xml [] = $data_format;
    $temp = $data_format->getMessagesSupported($actor_guid, false, null, true);
    $messages_xml = array_merge($messages_xml, $temp);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("actor_guid"      , $actor_guid);
$smarty->assign("formats_xml"     , $formats_xml);
$smarty->assign("messages_xml"    , $messages_xml);
$smarty->assign("formats_tabular" , $formats_tabular);
$smarty->assign("messages_tabular", $messages_tabular);
$smarty->display("inc_formats_available.tpl");

?>