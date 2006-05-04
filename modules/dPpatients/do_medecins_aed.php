<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('dPpatients', 'medecin') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CMedecin", "medecin_id");
$do->createMsg = "Medecin cr��";
$do->modifyMsg = "Medecin modifi�";
$do->deleteMsg = "Medecin supprim�";
$do->doIt();

?>