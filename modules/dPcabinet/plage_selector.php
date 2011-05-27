<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$ds = CSQLDataSource::get("std");

// Initialisation des variables
$plageconsult_id = CValue::get("plageconsult_id");
$hour            = CValue::get("hour");
$chir_id         = CValue::get("chir_id");
$function_id     = $chir_id ? null : CValue::get("function_id");
$date            = CValue::get("date", mbDate());
$hide_finished   = CValue::get("hide_finished", true);
$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));

// R�cup�ration des consultations de la plage s�l�ctionn�e
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date = $plage->date;
}

// R�cup�ration des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();

// Praticiens s�lectionn�s
$listPrat = new CMediusers;
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $listPrat->loadPraticiens(PERM_EDIT, $function_id, null, true);
} else {
  $listPrat = $listPrat->loadProfessionnelDeSante(PERM_EDIT, $function_id, null, true);
}

$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);

// Filtres
if ($hour) {
  $where["debut"] = "<= '$hour:00'";
  $where["fin"] = "> '$hour:00'";
}

if ($hide_finished) {
  $where[] = $ds->prepare("`date` >= %", mbDate());
}

// Filtre de la p�riode
$periods = array("day", "week", "month");
switch ($period) {
  case "day":
    $minDate = mbDate(null, $date);
    $maxDate = mbDate(null, $date);
    break;

  case "week":
    $minDate = mbDate("last sunday", $date);
    $maxDate = mbDate("next saturday", $date);
    break;

  case "month":
    $minDate = mbTransformTime(null, $date, "%Y-%m-01");
    $maxDate = mbTransformTime("+1 month", $minDate, "%Y-%m-01");
    break;

  default:
    trigger_error("P�riode '$period' inconnue");
    break;
}

$relDate = $date;
if ($period == 'month') {
	$relDate = $minDate;
}

$ndate = mbDate("+1 $period", $relDate);
$pdate = mbDate("-1 $period", $relDate);

$where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
$where[] = "libelle != 'automatique' OR libelle IS NULL";

$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);

if (!array_key_exists($plageconsult_id, $listPlage)) {
  $plage->_id = $plageconsult_id = null;
}
$currPlage = new CPlageconsult();
foreach ($listPlage as $keyPlage => &$currPlage) {
  if (!$plageconsult_id && $date == $currPlage->date) {
    $plageconsult_id = $currPlage->plageconsult_id;
  }

  $currPlage->_ref_chir =& $listPrat[$currPlage->chir_id];
  $currPlage->loadFillRate();
  $currPlage->loadCategorieFill();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("periods"        , $periods);
$smarty->assign("hour"           , $hour);
$smarty->assign("hours"          , CPlageconsult::$hours);
$smarty->assign("hide_finished"  , $hide_finished);
$smarty->assign("date"           , $date);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlage"      , $listPlage);
$smarty->assign("online"         , true);

$smarty->display("plage_selector.tpl");

?>