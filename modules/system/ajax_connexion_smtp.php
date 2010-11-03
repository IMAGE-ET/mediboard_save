<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'�change sp�cifi�", UI_MSG_ERROR);
}
if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("Aucun type de test sp�cifi�", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

$exchange_source->init();

if($type_action == "connexion") {
	try {
		$exchange_source->_mail->SmtpConnect();
		CAppUI::stepAjax("Connect� au serveur $exchange_source->host sur le port $exchange_source->port");
  } catch(phpmailerException $e) {
		CAppUI::stepAjax($e->errorMessage(), UI_MSG_WARNING);
	} catch(Exception $e) {
		CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
	}
} elseif($type_action == "envoi") {
  try {
    $exchange_source->setRecipient($exchange_source->email, $exchange_source->email);
  	$exchange_source->setSubject("Test d'envoi de mail par Mediboard");
  	$exchange_source->setBody("Ceci est un mail de test envoy� par Mediboard afin de v�rifier le fonctionnement de votre serveur SMTP");
    $exchange_source->addAttachment("./images/pictures/logo.png");
  	$exchange_source->send();
    CAppUI::stepAjax("Message envoy� au serveur $exchange_source->host sur le port $exchange_source->port");
  } catch(phpmailerException $e) {
    CAppUI::stepAjax($e->errorMessage(), UI_MSG_WARNING);
  } catch(Exception $e) {
    CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING);
  }
} else {
	CAppUI::stepAjax("Type de test non support� : $type_action", UI_MSG_ERROR);
}
