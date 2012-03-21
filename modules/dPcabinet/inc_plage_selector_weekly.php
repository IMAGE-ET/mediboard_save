<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author SARL Openxtrem
*/

CCanDo::checkRead();

global $period, $periods, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id;

$prat = new CMediusers;
$prat->load($chir_id);

//Planning au format  CPlanningWeek
$today = mbDate();
$debut = $date;
$debut = mbDate("-1 week", $debut);
$debut = mbDate("next monday", $debut);
$fin   = mbDate("next sunday", $debut);

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, 7, false, "auto", false, false);
$planning->title    = $prat->_view;
$planning->guid     = $prat->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");

$plage = new CPlageConsult();

$where = array();
$where["chir_id"] = " = '$chir_id'";
for ($i = 0; $i < 7; $i++) {
  $jour = mbDate("+$i day", $debut);
  $where["date"] = "= '$jour'";
  foreach($plage->loadList($where) as $_plage){
    $_plage->loadRefsConsultations(false);
    foreach($_plage->_ref_consultations as $_consult) {
      $debute = "$jour $_consult->heure";
      if($_consult->patient_id) {
        $_consult->loadRefPatient();
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->_ref_patient->_view, "#000", true, null, null);
      } else {
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, "[PAUSE]", "#aa0", true, null, null);
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_consult->_id;
      //Ajout de l'�v�nement au planning 
      $planning->addEvent($event);
    }
    $utilisation = $_plage->getUtilisation();
    foreach($utilisation as $_timing => $_nb) {
      if(!$_nb) {
        $debute = "$jour $_timing";
        $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", "#383", true, null, null);
        $event->type        = "rdvfree";
        $event->plage["id"] = $_plage->_id;
        //Ajout de l'�v�nement au planning 
        $planning->addEvent($event);
      }
    }
  }    
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("periods"        , $periods);
$smarty->assign("date"           , $date);
$smarty->assign("refDate"        , $debut);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("planning"       , $planning);
$smarty->assign("bank_holidays"  , mbBankHolidays($today));

$smarty->display("inc_plage_selector_weekly.tpl");
