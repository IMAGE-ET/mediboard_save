<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if ($reloadAlltarifs = CValue::post("reloadAlltarifs")) {
  $tarif = new CTarif();
  $tarifs = $tarif->loadList();
  foreach ($tarifs as $_tarif) {
    /* @var CTarif $_tarif*/
    $_tarif->_update_montants = 1;
    $_tarif->updateMontants();
    if ($msg = $_tarif->store()) {
      CAppUI::setMsg($_tarif->_id.$msg, UI_MSG_ERROR);
    }
  }
  CAppUI::setMsg("Tarifs mis � jour", UI_MSG_OK);
  echo CAppUI::getMsg();
}
else {
  $do = new CDoObjectAddEdit("CTarif", "tarif_id");

  // redirection vers la comptabilite dans le cas de la creation d'un nouveau tarif dans la consult
  if (isset($_POST["_tab"])) {
    $do->redirect = "m=dPcabinet&tab=".$_POST["_tab"];
  }
  $do->doIt();
}