<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

function viewMsg($msg, $action, $txt = ""){
  global $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    CAppUI::setMsg("$action: $msg", UI_MSG_ERROR );
    CAppUI::redirect("m=$m&tab=$tab");
    return;
  }
  CAppUI::setMsg("$action $txt", UI_MSG_OK );
}

// R�cup�ration du rpu
$rpu_id = CValue::post("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);

$sejour = $rpu->loadRefSejour();;

// Creation d'un s�jour reliquat
if (!CAppUI::conf("dPurgences create_sejour_hospit")) {
  // Clonage
  $sejour_rpu = new CSejour;
  foreach ($sejour->getProperties() as $name => $value) {
    $sejour_rpu->$name = $value;
  }
  
  // Enregistrement
  $sejour_rpu->_id = null;
  $msg = $sejour_rpu->store();
  viewMsg($msg, "S�jour reliquat enregistr�");
  
  // Transfert du RPU sur l'ancien s�jour
  $rpu->sejour_id = $sejour_rpu->_id;
} 

// Modification du RPU
$rpu->mutation_sejour_id = $sejour->_id;
$rpu->sortie_autorisee = "1";
$rpu->gemsa = "4";
$msg = $rpu->store();
viewMsg($msg, "CRPU-title-close");

// Passage en s�jour d'hospitalisation
$sejour->type = "comp";
$sejour->_en_mutation = $sejour_rpu->_id;
$msg = $sejour->store();
viewMsg($msg, "CSejour-title-modify");

CAppUI::redirect("m=dPplanningOp&tab=vw_edit_sejour&sejour_id=$sejour->_id");

?>