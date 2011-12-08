<?php /* $Id: do_cut_affectation_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::post("affectation_id");
$_date_cut      = CValue::post("_date_cut");
$lit_id         = CValue::post("lit_id");

$affectation = new CAffectation;
$affectation->load($affectation_id);

if ($_date_cut < $affectation->entree || $_date_cut > $affectation->sortie) {
  CAppUI::setMsg("Date de scindage hors des bornes de l'affectation", UI_MSG_ERROR);
  CApp::rip();
}

$affectation_cut = new CAffectation;
$affectation_cut->entree = $_date_cut;
$affectation_cut->lit_id = $affectation->lit_id;
$affectation_cut->sejour_id = $affectation->sejour_id;
$affectation_cut->sortie = $affectation->sortie;

if ($lit_id) {
  $affectation_cut->lit_id = $lit_id;
}

$affectation->sortie = $_date_cut;

if ($msg = $affectation->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

if ($msg = $affectation_cut->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

echo CAppUI::getMsg();
CApp::rip();

?>