<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: 
 *  @author Alexis Granger
 */

global $AppUI;

// lignes pour rentrer l'heure courante du serveur dans certains champs
/*
$listTimes = array("_debut", "_fin");
foreach($listTimes as $curr_item) {
  if(isset($_POST[$curr_item])) {
    if($_POST[$curr_item] == "current") {
      $_POST[$curr_item] = mbTime();
    }
  }
}*/

$do = new CDoObjectAddEdit("CAffectationPersonnel", "affect_id");
$do->createMsg = "Affectation cr��e";
$do->modifyMsg = "Affectation modifi�e";
$do->deleteMsg = "Affectation supprim�e";
$do->doIt();

?>