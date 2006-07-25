<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPccam", "dPccam"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");
$do->createMsg = "Favori cr��";
$do->modifyMsg = "Favori modifi�";
$do->deleteMsg = "Favori supprim�";
$do->redirect = null;
$do->doIt();

?>