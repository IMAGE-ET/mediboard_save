<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Poiron Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CModePaiement", "mode_paiement_id");
$do->createMsg = "Mode de paiement cr��e";
$do->modifyMsg = "Mode de paiement modifi�e";
$do->deleteMsg = "Mode de paiement supprim�e";
$do->doIt();

?>