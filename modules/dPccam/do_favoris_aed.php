<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");

// Am�lioration des textes
if ($favori_user = mbGetValueFromPost("favoris_user")) {
	$user = new CMediusers;
	$user->load($favori_user);
	$for = " pour $user->_view";
	$do->createMsg .= $for;
	$do->modifyMsg .= $for;
	$do->deleteMsg .= $for;
}

$do->redirect = null;
$do->doIt();

?>