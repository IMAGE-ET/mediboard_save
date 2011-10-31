<?php 
/**
 * Reprocessing exchange
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

// Chargement de l'�change demand�
$object = new CMbObject();
$exchange = $object->loadFromGuid($exchange_guid);

$sender = new $exchange->sender_class;
$sender->load($exchange->sender_id);

if (!$ack_data = CEAIDispatcher::dispatch($exchange->_message, $sender, $exchange->_id)) {
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' ne peut retrait�", UI_MSG_ERROR);
}

if ($exchange instanceof CEchangeHprim) {
  $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($sender->_data_format->_family_message);
  $ack->loadXML($ack_data);
  $doc_valid = $ack->schemaValidate();
  if ($doc_valid) {
    $exchange->statut_acquittement = $ack->getStatutAcquittement();
  }
  $exchange->date_echange        = mbDateTime();
  $exchange->acquittement_valide = $doc_valid ? 1 : 0;
  $exchange->_acquittement = $ack_data;
  $exchange->store();
  
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a �t� retrait�");
}

if ($exchange instanceof CExchangeIHE) {
  $ack = new CHL7v2Acknowledgment($sender->_data_format->_family_message);
  $ack_message = $ack->validate($ack_data);
  $exchange->date_echange        = mbDateTime(); 
  $exchange->statut_acquittement = $ack->getStatutAcknowledgment(); 
  $exchange->acquittement_valide = $ack_message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
  $exchange->_acquittement       = $ack_data;
  $exchange->store();
  
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a �t� retrait�");
}

?>