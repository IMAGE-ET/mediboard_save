<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductCategory', 'category_id');
$do->createMsg = 'Cat�gorie cr��e';
$do->modifyMsg = 'Cat�gorie modifi�e';
$do->deleteMsg = 'Cat�gorie supprim�e';
$do->doIt();

?>