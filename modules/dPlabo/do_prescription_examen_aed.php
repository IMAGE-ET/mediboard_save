<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLaboExamen", "prescription_labo_examen_id");
$do->createMsg = "Analyse ajout�e";
$do->modifyMsg = "Analyse modifi�e";
$do->deleteMsg = "Analyse enlev�e";
$do->doIt();

?>