<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");
$do->createMsg = "Favori cr��";
$do->modifyMsg = "Favori modifi�";
$do->deleteMsg = "Favori supprim�";
$do->redirect = null;
$do->doIt();

?>