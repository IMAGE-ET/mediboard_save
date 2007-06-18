<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// Object binding
$obj = new CPlageconsult();
if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

$del         = dPgetParam( $_POST, "del", 0 );
$repeat      = dPgetParam( $_POST, "_repeat", 0 );
$type_repeat = dPgetParam( $_POST, "_type_repeat", 1 );
   
if ($del) {
  $obj->load();

  while ($repeat-- > 0) {
    if (!$obj->_id) {
      $AppUI->setMsg("Plage non trouv�e", UI_MSG_ERROR);
    }
    else {
      if ($msg = $obj->delete()) {
        $AppUI->setMsg("Plage non supprim�e", UI_MSG_ERROR);
        $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        $AppUI->setMsg("Plage supprim�e", UI_MSG_OK);
      }
    }
    $obj->becomeNext();
  }
  
  mbSetValueToSession("plageconsult_id");

} else {
  while ($repeat-- > 0) {    
    if ($obj->_id) {
      if ($msg = $obj->store()) {
        $AppUI->setMsg("Plage non mise � jour", UI_MSG_ERROR);
        $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        $AppUI->setMsg("Plage mise � jour", UI_MSG_OK);
      }      
    } 
    else {
      if ($msg = $obj->store()) {
        $AppUI->setMsg("Plage non cr�e", UI_MSG_ERROR);
        $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        $AppUI->setMsg("Plage cr��e", UI_MSG_OK);
      }
    }

    
    for ($i=1; $i <= $type_repeat; $i++) {
      $obj->becomeNext();
    }
  }

}

$AppUI->redirect("m=$m");
?>