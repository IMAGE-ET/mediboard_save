<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage pharmacie
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CDispensiation', 'dispensiation_id');
$do->createMsg = 'Dispensiation cr��e';
$do->modifyMsg = 'Dispensiation modifi�e';
$do->deleteMsg = 'Dispensiation supprim�e';
$do->doIt();

?>