<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/

global $can, $m, $g;

$can->needsRead();

$blood_salvage_id = mbGetValueFromGetOrSession("blood_salvage_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$timing  = mbGetValueFromGetOrSession("timing");
$modif_operation    = $date>=mbDate();


$blood_salvage = new CBloodSalvage();
if($blood_salvage_id){
  $blood_salvage->load($blood_salvage_id);
  $blood_salvage->loadRefsFwd();
  $blood_salvage->loadRefPlageOp();

  $timing["_recuperation_end"]         = array();
  $timing["_transfusion_start"]        = array();
  $timing["_transfusion_end"]          = array();
  foreach($timing as $key => $value) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $blood_salvage->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $blood_salvage->$key);
    }
  }
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("blood_salvage"  , $blood_salvage  );
$smarty->assign("date"           , $date           );
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("timing", $timing);



$smarty->display("inc_vw_bs_sspi_timing.tpl");

?>