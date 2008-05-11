<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

$obj = new CPack();
$obj->bind($_POST);

// detect if a delete operation has to be processed
$del = dPgetParam( $_POST, 'del', 0 );
if ($del) {
	// check canDelete
	if ($msg = $obj->canDeleteEx()) {	
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}

	// delete object
	if ($msg = $obj->delete()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
    mbSetValueToSession("pack_id");
		$AppUI->setMsg( "Pack supprim�e", UI_MSG_ALERT);
		$AppUI->redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['pack_id'];
		$AppUI->setMsg( $isNotNew ? 'Pack mis � jour' : 'Pack ajout�', UI_MSG_OK);
	}
	$AppUI->redirect();
}
?>