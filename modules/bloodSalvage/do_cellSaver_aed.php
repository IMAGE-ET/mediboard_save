<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/

$do = new CDoObjectAddEdit('CCellSaver', 'cell_saver_id');

$do->modifyMsg = "Cell Saver modifi�";
$do->createMsg = "Cell Saver cr��";
$do->doIt();

?>