<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id;

$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));
$periods         = array("day", "week", "month","weekly");
$chir_id         = CValue::get("chir_id");
$function_id     = $chir_id ? null : CValue::get("function_id");
$date            = CValue::get("date", mbDate());
$plageconsult_id = CValue::get("plageconsult_id");

// Liste des praticiens
$user = new CMediusers();
$listPraticiens = $user->loadPraticiens();

// R�cup�ration des consultations de la plage s�l�ctionn�e
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date = $plage->date;
}

// R�cup�ration de la periode pr�c�dente et suivante
$unit = $period;
if($period == "weekly") {
  $unit = "week";
}

$ndate = mbDate("+1 $unit", $date);
$pdate = mbDate("-1 $unit", $date);

if($period == "weekly") {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_weekly");
} else {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_classic");
}

?>