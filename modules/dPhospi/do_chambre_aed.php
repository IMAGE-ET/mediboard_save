<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CChambre", "chambre_id");
$do->createMsg = "Chambre cr��e";
$do->modifyMsg = "Chambre modifi�e";
$do->deleteMsg = "Chambre supprim�e";
$do->doIt();
?>