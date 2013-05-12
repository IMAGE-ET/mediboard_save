<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

if (null == $sip_barcode = CValue::get("sip_barcode")) {
  return;
}

$values = array();
if (!preg_match("/SID([\d]+)/i", $sip_barcode, $values)) {
  CAppUI::stepAjax("Le num�ro saisi '%s' ne correspond pas � un idenfitiant de s�jour", UI_MSG_WARNING, $sip_barcode);
  return;
}

$sejour = new CSejour;
$sejour->load($values[1]);
if (!$sejour->_id) {
  CAppUI::stepAjax("Le s�jour dont l'idenfitiant est '%s' n'existe pas", UI_MSG_WARNING, $sejour->_id);
  return;
}

$sejour->loadRefRPU();
if ($sejour->type != "urg" && !$sejour->_ref_rpu->_id) {
  CAppUI::stepAjax("Le s�jour trouv� '%s' n'est pas un s�jour d'urgences", UI_MSG_WARNING, $sejour->_view);
  return;
}

CAppUI::redirect("m=dPurgences&tab=vw_aed_rpu&sejour_id=$sejour->_id");
