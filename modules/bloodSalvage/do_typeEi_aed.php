<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/

$do = new CDoObjectAddEdit('CTypeEi', 'type_ei_id');

$do->modifyMsg = "Mod�le de fiche modifi�";
$do->createMsg = "Mod�le de fiche cr��";
$do->doIt();

?>