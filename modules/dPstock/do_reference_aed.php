<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductReference', 'reference_id');
$do->createMsg = 'R�f�rence cr��e';
$do->modifyMsg = 'R�f�rence modifi�e';
$do->deleteMsg = 'R�f�rence supprim�e';
$do->doIt();

?>