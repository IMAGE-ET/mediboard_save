<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage pharmacie
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CDelivrance', 'delivrance_id');
$do->createMsg = 'Delivrance cr��e';
$do->modifyMsg = 'Delivrance modifi�e';
$do->deleteMsg = 'Delivrance supprim�e';
$do->doIt();

?>