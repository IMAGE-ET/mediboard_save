<?php /** $Id: ajax_vw_content_plan_soins_service.php 6159 2009-04-23 08:54:24Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$categories_id = CValue::getOrSession("categories_id");
$date          = CValue::getOrSession("date");
$date_max      = CMbDT::date("+ 1 DAY", $date);
$service_id    = CValue::getOrSession("service_id", "none");
$nb_decalage   = CValue::get("nb_decalage");
$mode_dossier  = CValue::get("mode_dossier", "administration");
$premedication = CValue::get("premedication");
$real_time     = CValue::getOrSession("real_time", 0);

$composition_dossier = array();
$bornes_composition_dossier = array();
$count_composition_dossier = array();

$configs = CConfigService::getAllFor($service_id);

// Si la date actuelle est inf�rieure a l'heure affich�e sur le plan de soins, on affiche le plan de soins de la veille
$datetime_limit = CMbDT::dateTime($configs["Poste 1"].":00:00");
if ($date == CMbDT::date() && CMbDT::dateTime() < $datetime_limit) {
  $date = CMbDT::date("- 1 DAY", $date);
}

if (!$nb_decalage) {
  $nb_decalage = $configs["Nombre postes avant"];
}

$planif_manuelle = CAppUI::conf("dPprescription CPrescription planif_manuelle", CGroups::loadCurrent()->_guid);

$tabHours = CAdministration::getTimingPlanSoins($date, $configs);
foreach ($tabHours as $_key_date => $_period_date) {
  foreach ($_period_date as $_key_periode => $_period_dates) {
    $count_composition_dossier[$_key_date][$_key_periode] = $planif_manuelle ? 3 : 2;
    $first_date = reset(array_keys($_period_dates));
    $first_time = reset(reset($_period_dates));
    $last_date = end(array_keys($_period_dates));
    $last_time = end(end($_period_dates));

    $composition_dossier[] = "$_key_date-$_key_periode";

    $bornes_composition_dossier["$_key_date-$_key_periode"]["min"] = "$first_date $first_time:00:00";
    $bornes_composition_dossier["$_key_date-$_key_periode"]["max"] = "$last_date $last_time:00:00";

    foreach ($_period_dates as $_key_real_date => $_period_hours) {
      $count_composition_dossier[$_key_date][$_key_periode] += count($_period_hours);
      $_dates[$_key_real_date] = $_key_real_date;
    }
  }
}

// Chargement des lignes de prescription
$line = new CPrescriptionLineElement();

$ljoin = array();
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id AND prescription.type = 'sejour'";
$ljoin["sejour"] = "sejour.sejour_id = prescription.object_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";

$where = array();
$where["element_prescription_id"] =  CSQLDataSource::prepareIn($categories_id);
if ($real_time) {
  $time = CMbDT::time();
  $where[] = "'$date $time' <= affectation.sortie && '$date $time' >= affectation.entree";
}
else {
  $where[] = "'$date' <= affectation.sortie && '$date_max' >= affectation.entree";
}
$where["affectation.service_id"] = " = '$service_id'";
$where["inscription"] = " = '0'";
$where["active"] = " = '1'";

if ($premedication) {
  $where["premedication"] = " = '1'";
}
$lines = $line->loadList($where, null, null, null, $ljoin);

// Chargement du patient pour chaque sejour
$sejours       = CMbArray::pluck($lines, "_ref_prescription", "_ref_object");
$patients      = CMbObject::massLoadFwdRef($sejours, "patient_id");

$prescriptions = array();

foreach ($lines as $_line_element) {
  if (!in_array($_line_element->prescription_id, $prescriptions)) {
    $prescriptions[$_line_element->prescription_id] = $_line_element->_ref_prescription;
  }
  $prescription =& $prescriptions[$_line_element->prescription_id];
  $prescription->_ref_object->_ref_curr_affectation = $prescription->_ref_object->getCurrAffectation($date . " 00:00:00");
  $prescription->_ref_object->_ref_curr_affectation->loadView();
  $prescription->_ref_object->_ref_curr_affectation->loadRefLit()->loadCompleteView();
  $prescription->loadRefPatient();
  $_line_element->loadRefsPrises();
  $_line_element->loadRefLogSignee();
  $_line_element->loadRefOperation();
  $_line_element->_ref_praticien->loadRefFunction();
  $category = $_line_element->_ref_element_prescription->_ref_category_prescription;
  $name_chap = $category->chapitre;
  $name_cat = $category->_id;

  if ($_line_element->_ref_element_prescription->rdv) {
    $_line_element->loadRefTask();
  }

  // Chargement des planificatoins systemes  
  $_line_element->calculPlanifSysteme();

  // Chargement des administrations et des transmissions
  $_line_element->calculPrises($prescription, $_dates, $name_chap, $name_cat, 1, $planif_manuelle);
  $_line_element->calculAdministrations($_dates);

  foreach ($_dates as $_date) {
    // Pre-remplissage des prises prevues dans le dossier de soin
    if (($_date >= $_line_element->debut && $_date <= CMbDT::date($_line_element->_fin_reelle))) {
      // Si aucune prise  
      if ((count($_line_element->_ref_prises) < 1) && (!isset($prescription->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))) {
        $prescription->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
      }
    }
  }

  // Suppression des prises prevues replanifi�es
  $_line_element->removePrisesPlanif();
}

$prescriptions_order = array();
$nb_lines_element = 0;
foreach ($prescriptions as $key => $_prescription) {
  if ($_prescription->_ref_lines_elt_for_plan) {
    foreach ($_prescription->_ref_lines_elt_for_plan as $_elements_by_chap) {
      foreach ($_elements_by_chap as $elements_cat) {
        foreach ($elements_cat as $_element) {
          $nb_lines_element += count($_element);
        }
      }
    }
  }

  $bed = $_prescription->_ref_object->_ref_curr_affectation->_ref_lit;
  $bedroom_name = $bed->_ref_chambre->nom . $bed->nom . $_prescription->_ref_object->_ref_curr_affectation->_id;
  $prescriptions_order[$bedroom_name] = $key;
}

// Tri par num�ro de chambre
ksort($prescriptions_order);
$prescriptions = CMbArray::ksortByArray($prescriptions, $prescriptions_order);

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("patients", $patients);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("categories", $categories);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("composition_dossier", $composition_dossier);
$smarty->assign("bornes_composition_dossier", $bornes_composition_dossier);
$smarty->assign("count_composition_dossier", $count_composition_dossier);
$smarty->assign("operations", array());
$smarty->assign("nb_decalage", $nb_decalage);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("nb_lines_element", $nb_lines_element);
$smarty->assign("now", CMbDT::dateTime());
$smarty->assign("date", $date);
$smarty->assign("move_dossier_soin", false);
$smarty->assign("configs", $configs);
$smarty->assign("params", CConstantesMedicales::$list_constantes);
$smarty->assign("manual_planif", $planif_manuelle);

$smarty->display('inc_vw_content_plan_soins_service.tpl');