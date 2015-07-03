<?php
/**
 * View exchanges
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$echange_soap_id = CValue::get("echange_soap_id");

// Chargement de l'échange SOAP demandé
$echange_soap = new CEchangeSOAP();
$echange_soap->load($echange_soap_id);

if ($echange_soap->_id) {
  $echange_soap->loadRefs();

  $echange_soap->input  = unserialize($echange_soap->input);
  if ($echange_soap->soapfault != 1) {
    $echange_soap->output = unserialize($echange_soap->output);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("echange_soap", $echange_soap);
$smarty->display("inc_echange_soap_details.tpl");
