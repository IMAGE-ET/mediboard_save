<?php
/**
 * Test connexion
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'�change sp�cifi�", UI_MSG_ERROR);
}
/** @var CSourceSOAP $exchange_source */
$exchange_source = CExchangeSource::get($exchange_source_name, "soap", true, null, false);

if (!$exchange_source) {
  CAppUI::stepAjax("Aucune source d'�change disponible pour ce nom : '$exchange_source_name'", UI_MSG_ERROR);
}

if (!$exchange_source->host) {
  CAppUI::stepAjax("Aucun h�te pour la source d'�change : '$exchange_source_name'", UI_MSG_ERROR);
}

$options = array(
  "encoding" => $exchange_source->encoding
);

$soap_client = new CSOAPClient($exchange_source->type_soap);
$soap_client->make(
  $exchange_source->host, $exchange_source->user, $exchange_source->getPassword(), $exchange_source->type_echange, $options,
  null, null, $exchange_source->local_cert, $exchange_source->passphrase, $exchange_source->safe_mode, $exchange_source->verify_peer,
  $exchange_source->cafile, $exchange_source->wsdl_external
);

if (!$soap_client || $soap_client->client->soap_client_error) {
  CAppUI::stepAjax("Impossible de joindre la source de donn�e : '$exchange_source_name'", UI_MSG_ERROR);
}
else {
  CAppUI::stepAjax("Connect� � la source '$exchange_source_name'");
}

try {
  $soap_client->client->checkServiceAvailability();
}
catch (CMbException $e) {
  $e->stepAjax();
}