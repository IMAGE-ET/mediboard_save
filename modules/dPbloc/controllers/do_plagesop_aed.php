<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Object binding
$obj = new CPlageOp();
$obj->bind($_POST);

$del    = CValue::post("del"    , 0);
$repeat = CValue::post("_repeat", 0);


// si l'id de l'objet est nul => creation
// si l'objet a un id, alors, modification


$body_msg = null;
$header   = array();
$msgNo    = null;

if ($del) {
  // Supression des plages
  $obj->load();
  while ($repeat > 0) {
    if (!$obj->_id) {
      CAppUI::setMsg("Plage non trouv�e", UI_MSG_ERROR);
    }
    else {
      if ($msg = $obj->delete()) {
        CAppUI::setMsg("Plage non supprim�e", UI_MSG_ERROR);
        CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        CAppUI::setMsg("Plage supprim�e", UI_MSG_OK);
      }
    }
    $repeat -= $obj->becomeNext();
  }
  $_SESSION["dPbloc"]["id"] = null;
  
} else {
  //Modification des plages
  if ($obj->_id != 0) {
    while ($repeat > 0) {   
      if ($obj->_id) {
        if ($msg = $obj->store()) {
          CAppUI::setMsg("Plage non mise � jour", UI_MSG_ERROR);
          CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
        } 
        else {
          CAppUI::setMsg("Plage mise � jour", UI_MSG_OK);
        }      
      } 
      $repeat -= $obj->becomeNext();
    }
  }
  // Cr�ation des plages
  else {
    while ($repeat > 0) {   
      if ($msg = $obj->store()) {
        CAppUI::setMsg("Plage non cr��e", UI_MSG_ERROR);
        CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        CAppUI::setMsg("Plage cr��e", UI_MSG_OK);
      }
      $repeat -= $obj->becomeNext();
    }
  }
}

if ($otherm = CValue::post("otherm", 0)) {
  $m = $otherm;
}

CAppUI::redirect("m=$m");

?>