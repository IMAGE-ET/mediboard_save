<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CFavoricim10", "favoris_id");
$do->createMsg = "Favori cr��";
$do->modifyMsg = "Favori modifi�";
$do->deleteMsg = "Favori supprim�";
$do->redirect = null;
$do->doIt();

?>