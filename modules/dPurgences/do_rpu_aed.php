<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

//Date courante dans la sortie
if(isset($_POST["sortie"])){
  if($_POST["sortie"] == "current"){
    $_POST["sortie"] = mbDateTime();
  }
}

$do = new CDoObjectAddEdit("CRPU", "rpu_id");
$do->createMsg = "Urgence cr��e";
$do->modifyMsg = "Urgence modifi�e";
$do->deleteMsg = "Urgence supprim�e";
$do->doIt();

?>