<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CSociete', 'societe_id');
$do->createMsg = 'Soci�t� cr��e';
$do->modifyMsg = 'Soci�t� modifi�e';
$do->deleteMsg = 'Soci�t� supprim�e';
$do->doIt();

?>