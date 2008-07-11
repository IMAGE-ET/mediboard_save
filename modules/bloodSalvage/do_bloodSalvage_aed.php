<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision:  $
* @author Alexandre Germonneau
*/


global $AppUI, $can, $m;
/*
 * R�cup�ration de l'heure courante pour les timers de l'op�ration de Cell Saver.
 */
$listTimes = array("_recuperation_start", "_recuperation_end", "_transfusion_start", "_transfusion_end");
foreach($listTimes as $curr_item) {
  if(isset($_POST[$curr_item])) {
    if($_POST[$curr_item] == "current") {
      $_POST[$curr_item] = mbTime();
    }
  }
}
$do = new CDoObjectAddEdit('CBloodSalvage', 'blood_salvage_id');


$do->modifyMsg = "RSPO modifi�";
$do->createMsg = "RSPO cr��";
$do->doIt();

?>