<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('dPpatients', 'antecedent') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CAntecedent", "antecedent_id");
$do->createMsg = "Antecedent cr��";
$do->modifyMsg = "Antecedent modifi�";
$do->deleteMsg = "Antecedent supprim�";
$do->doIt();

?>