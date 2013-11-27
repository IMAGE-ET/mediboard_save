<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

//CCanDo::checkRead();

$choix = CValue::get("choix", "mois");
$filter = new CPlageConge();
$filter->user_id    = CValue::get("user_id", CAppUI::$user->_id);
$filter->date_debut = CValue::get("date_debut", CMbDT::date());

// Tableau des jours f�ri�s sur 2 ans, car
// en mode semaine : 31 d�cembre - 1 janvier
$bank_holidays = array_merge(CMbDate::getHolidays($filter->date_debut),
  CMbDate::getHolidays(CMbDT::transform("+1 YEAR", $filter->date_debut, "%Y-%m-%d")));

$mediuser  = new CMediusers();
$mediusers = $mediuser->loadListFromType();

if (!$filter->date_debut) {
  $filter->date_debut = Date("Y-m-d");
}

// Si la date rentr�e par l'utilisateur est un lundi,
// on calcule le dimanche d'avant et on rajoute un jour. 
$tab_start = array();
if ($choix == "semaine") {
  $last_sunday = CMbDT::transform('last sunday', $filter->date_debut, '%Y-%m-%d');
  $last_monday = CMbDT::transform('+1 day', $last_sunday, '%Y-%m-%d');
  $debut_periode = $last_monday;
  
  $fin_periode = CMbDT::transform('+6 day', $debut_periode, '%Y-%m-%d');
}
elseif ($choix == "annee") {
  list($year,$m,$j) = explode("-", $filter->date_debut);
  $debut_periode = "$year-01-01";
  $fin_periode = "$year-12-31";
  $j=1;
  for ($i=1;$i<13;$i++) {
    if (!date("w", mktime(0, 0, 0, $i, 1, $year))) {
      $tab_start[$j] = 7;
    }
    else {
      $tab_start[$j]= date("w", mktime(0, 0, 0, $i, 1, $year));
    }
    $j++;
    $tab_start[$j]= date("t", mktime(0, 0, 0, $i, 1, $year));
    $j++;
  }
}
else {
  list($a,$m,$j) = explode("-", $filter->date_debut);
  $debut_periode  = "$a-$m-01";
  $fin_periode  = CMbDT::transform('+1 month', $debut_periode, '%Y-%m-%d');
  $fin_periode  = CMbDT::transform('-1 day', $fin_periode, '%Y-%m-%d');
}

$tableau_periode = array();

for ($i = 0 ; $i < CMbDT::daysRelative($debut_periode, $fin_periode) + 1; $i ++) {
  $tableau_periode[$i] = CMbDT::transform('+'.$i.'day', $debut_periode, '%Y-%m-%d');
}


$where = array();
$where[] = "((date_debut >= '$debut_periode' AND date_debut <= '$fin_periode'" .
         ")OR (date_fin >= '$debut_periode' AND date_fin <= '$fin_periode')".
         "OR (date_debut <='$debut_periode' AND date_fin >= '$fin_periode'))";
$where["user_id"] = CSQLDataSource::prepareIn(array_keys($mediusers), $filter->user_id);

$plageconge = new CPlageConge();
$plagesconge = array();
$orderby="user_id";
/** @var CPlageConge[] $plagesconge */
$plagesconge = $plageconge->loadList($where, $orderby);
$tabUser_plage = array();
$tabUser_plage_indices = array();

foreach ($plagesconge as $_plage) {
  $_plage->loadRefUser();
  $_plage->_ref_user->loadRefFunction();
  $_plage->_deb   = CMbDT::daysRelative($debut_periode, $_plage->date_debut);
  $_plage->_fin   = CMbDT::daysRelative($_plage->date_debut, $_plage->date_fin)+1;
  $_plage->_duree = CMbDT::daysRelative($_plage->date_debut, $_plage->date_fin)+1;
}

$smarty = new CSmartyDP();

$smarty->assign("debut_periode"  , $debut_periode);
$smarty->assign("filter"         , $filter);
$smarty->assign("plagesconge"    , $plagesconge);
$smarty->assign("choix"          , $choix);
$smarty->assign("mediusers"      , $mediusers);
$smarty->assign("tableau_periode", $tableau_periode);
$smarty->assign("tab_start"      , $tab_start);
$smarty->assign("bank_holidays"  , $bank_holidays);

if (($choix == "semaine" || $choix == "mois")) {
  $smarty->display("inc_planning.tpl");
}
else {
  $smarty->display("inc_planning_annee.tpl");
}