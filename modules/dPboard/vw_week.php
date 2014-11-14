<?php

/**
 *  $Id$
 *  
 * @package    Mediboard
 * @subpackage dPboard
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CAppUI::requireModuleFile("dPboard", "inc_board");

$date  = CValue::getOrSession("date", CMbDT::date());
$prec  = CMbDT::date("-1 week", $date);
$suiv  = CMbDT::date("+1 week", $date);
$today = CMbDT::date();

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("date", $date);
$debut = CMbDT::date("-1 week", $debut);
$debut = CMbDT::date("next monday", $debut);
$fin   = CMbDT::date("next sunday", $debut);

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Praticien selectionn�
$chirSel = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);
$prat = new CMediusers();
$prat->load($chirSel);
$function_prat = $prat->loadRefFunction();

$user = new CMediusers();

$nbjours = 7;
$listPlageConsult = new CPlageconsult();
$listPlageOp = new CPlageOp();

$where = array();
$where["date"] = "= '$fin'";
$where["chir_id"] = " = '$chirSel'";

$operation = new COperation();
$operation->chir_id = $chirSel;
$operation->date = $fin;

// find for day number
if (!$listPlageConsult->countList($where) && !$listPlageOp->countList($where) && !$operation->countMatchingList()) {
  $nbjours--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = CMbDT::date("-1 day", $fin);
  $where["date"] = "= '$dateArr'";
  $operation->date = $dateArr;
  if (!$listPlageConsult->countList($where) && !$listPlageOp->countList($where) && !$operation->countMatchingList()) {
    $nbjours--;
  }
}

// Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, $nbjours, false, null, null, true);

if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}
$planning->guid     = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");

$hours = CPlageconsult::$hours;

$plageConsult     = new CPlageconsult();
$plageOp          = new CPlageOp();
$plagesConsult    = array();
$plagesOp         = array();

$hours_stop       = CPlageconsult::$hours_stop;
$hours_start      = CPlageconsult::$hours_start;

$where            = array();
$where["chir_id"] = "= '$chirSel'";

$last_day = CMbDT::date("+7 day", $debut);

//plages consult
$plagesConsult = $plageConsult->loadForDays($chirSel, $debut, $last_day);
$color = "#BFB";
foreach ($plagesConsult as $_consult) {
  $_consult->loadFillRate();
  $_consult->countPatients();
  $_consult->loadRefChir();
  $class = null;
  if ($_consult->pour_tiers) {
    $class = "pour_tiers";
  }
  if (CModule::getActive("3333tel")) {
    C3333TelTools::checkPlagesConsult($_consult, $_consult->_ref_chir->function_id);
  }
  ajoutEvent($planning, $_consult, $_consult->date, $_consult->libelle, $color, "consultation", $class);
}

// plages op
$plagesOp = $plageOp->loadForDays($chirSel, $debut, CMbDT::date("+7 day", $debut));
foreach ($plagesOp as $_op) {
  $_op->loadRefSalle();
  $_op->multicountOperations();
  $color = "#BCE";

  //to check if group is present group
  $g = CGroups::loadCurrent();
  $_op->loadRefSalle();
  $_op->_ref_salle->loadRefBloc();
  if ($_op->_ref_salle->_ref_bloc->group_id != $g->_id) {
    $color = "#748dee";
  }

  ajoutEvent($planning, $_op, $_op->date, $_op->_ref_salle->nom, $color, "operation");
}

// plages conge
$conge = new CPlageConge();
$where_conge = array();
$where_conge["date_debut"] = " <= '$fin' ";
$where_conge["date_fin"] = " >= '$debut' ";
$where_conge["user_id"] = " = '$chirSel' ";
/** @var CPlageConge[] $conges */
$conges = $conge->loadList($where_conge);
foreach ($conges as $_conge) {
  $_dates = array();
  $_dates[] = $_conge->date_debut;
  $_date = $_conge->date_debut;
  if ($_conge->date_debut != $_conge->date_fin) {
    while($_date != $_conge->date_fin) {
      $_date = CMbDT::date("+1 DAY", $_date);
      $_dates[] = $_date;
    }
  }
  $libelle = '<h3 style="text-align: center">
    CONGES</h3>
    <p style="text-align: center">'.CMbString::htmlEntities($_conge->libelle).'</p>';
  foreach ($_dates as $_date) {
    $event = new CPlanningEvent($_conge->_guid.$_date, $_date, 1430 , $libelle, "#ddd", true, "hatching", null, false );
    $planning->addEvent($event);
  }
}

//Operation hors plage
$operation = new COperation();
$where = array();
for ($i = 0; $i < 7; $i++) {
  $date             = CMbDT::date("+$i day", $debut);
  $where["date"]    = "= '$date'";
  $where["annulee"] = " = '0'";
  $where["plageop_id"] = " IS NULL";
  $where[] = "chir_id = '$chirSel' OR anesth_id = '$chirSel'";
  $nb_hors_plages = $operation->countList($where);
  if ($nb_hors_plages) {
    $onclick = "viewList('$date', null, 'CPlageOp')";
    $planning->addDayLabel($date, "$nb_hors_plages intervention(s) hors-plage", null, "#ffd700", $onclick);
  }
}

/**
 * Ajout d'un �v�nement � un planning
 *
 * @param CPlanningWeek &$planning planning concern�
 * @param CPlageHoraire $_plage    plage � afficher
 * @param string        $date      date de l'�v�nement
 * @param string        $libelle   libell� de l'�v�nement
 * @param string        $color     couleur de l'�v�nement
 * @param string        $type      type de l'�v�nement
 * @param string|null   $class     css class to apply
 *
 * @return void
 */
function ajoutEvent(&$planning, $_plage, $date, $libelle, $color, $type, $class= null) {
  $debute = "$date $_plage->debut";
  $event = new CPlanningEvent(
    $_plage->_guid, $debute, CMbDT::minutesRelative($_plage->debut, $_plage->fin),
    $libelle, $color, true, $class, null
  );
  $event->resizable = true;
  
  //Param�tres de la plage de consultation
  $event->type = $type;
  $pct = $_plage->_fill_rate;
  if ($pct > "100") {
    $pct = "100";
  }
  if ($pct == "") {
    $pct = 0;
  }

  $event->plage["id"]  = $_plage->_id;
  $event->plage["pct"] = $pct;
  if ($type == "consultation") {
    $event->plage["locked"]       = $_plage->locked;
    $event->plage["_affected"]    = $_plage->_affected;
    $event->plage["_nb_patients"] = $_plage->_nb_patients;
    $event->plage["_total"]       = $_plage->_total;
  }
  else {
    $event->plage["locked"]         = 0;
    $event->plage["_count_operations"] = $_plage->_count_operations;
  }
  $event->plage["list_class"] = $_plage->_guid;
  $event->plage["add_class"]  = $date;
  $event->plage["list_title"] = $date;
  $event->plage["add_title"]  = $type;
  
  //Ajout de l'�v�nement au planning 
  $planning->addEvent($event);
}

$planning->rearrange(true);

// Variables de templates
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("chirSel",  $chirSel);
$smarty->assign("planning",  $planning);

$smarty->display("vw_week.tpl");
