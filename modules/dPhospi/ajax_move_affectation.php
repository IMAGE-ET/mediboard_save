<?php /* $Id: ajax_move_affectation.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");
$sejour_id      = CValue::get("sejour_id");

$affectation = new CAffectation;
if ($affectation_id) {
  $affectation->load($affectation_id);
  
  // On d�place l'affectation parente si n�cessaire
  if (null != $affectation_id = $affectation->parent_affectation_id) {
    $affectation = new CAffectation;
    $affectation->load($affectation_id);
  }
}
else {
  $affectation->sejour_id = $sejour_id;
  $sejour = new CSejour;
  $sejour->load($sejour_id);
  $affectation->entree = $sejour->entree;
  $affectation->sortie = $sejour->sortie;
}

$affectation->lit_id = $lit_id;

if ($msg = $affectation->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

echo CAppUI::getMsg();
?>