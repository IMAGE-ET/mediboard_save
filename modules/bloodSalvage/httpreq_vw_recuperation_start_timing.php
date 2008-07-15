<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodSalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$blood_salvage_id = mbGetValueFromPostOrSession("blood_salvage_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$modif_operation    = $date>=mbDate();

$blood_salvage = new CBloodSalvage();
if($blood_salvage_id){
  $blood_salvage->load($blood_salvage_id);
  $blood_salvage->loadRefs();
  
  $timing["_recuperation_start"]       = array();
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


$smarty->display("inc_vw_recuperation_start_timing.tpl");

?>