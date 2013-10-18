<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$_date_min  = CValue::getOrSession("_date_min");
$_date_max  = CValue::getOrSession("_date_max");
$_prat_id   = CValue::getOrSession("chir");
$typeVue    = CValue::getOrSession("typeVue");

$nbActes = array();
$montantSejour = array();
$tabSejours = array();
$totalActes = 0;
$montantTotalActes = 0;

$praticien = new CMediusers();
$praticien->load($_prat_id);

$date_min = CMbDT::date("-1 day", $_date_min);
$date_max = CMbDT::date("+1 day", $_date_max);

$ljoin = array();
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["acte_ccam"] = "consultation.consultation_id = acte_ccam.object_id AND acte_ccam.object_class = 'CConsultation'";
$where = array();
$where[] = "acte_ccam.execution BETWEEN '$date_min' AND '$date_max'";
$where[] = "acte_ccam.executant_id = '$_prat_id'";
$sejour = new CSejour();
$sejours = $sejour->loadList($where, null, null, "sejour_id", $ljoin);

$ljoin = array();
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["acte_ngap"] = "consultation.consultation_id = acte_ngap.object_id AND acte_ngap.object_class = 'CConsultation'";
$where2 = array();
$where2[] = "acte_ngap.execution BETWEEN '$date_min' AND '$date_max'";
$where2[] = "acte_ngap.executant_id = '$_prat_id'";
$sejour = new CSejour();
$sejours_ngap = $sejour->loadList($where2, null, null, "sejour_id", $ljoin);
foreach ($sejours_ngap as $key => $_sejour) {
  if (!isset($sejours[$key])) {
    $sejours[$key] = $_sejour;
  }
}

$ljoin = array();
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
$ljoin["acte_ccam"] = "operations.operation_id = acte_ccam.object_id AND acte_ccam.object_class = 'COperation'";
$where["operations.annulee"] = " = '0'";
$sejours_consult = $sejour->loadList($where, null, null, "sejour_id", $ljoin);
foreach ($sejours_consult as $key => $_sejour) {
  if (!isset($sejours[$key])) {
    $sejours[$key] = $_sejour;
  }
}

$ljoin = array();
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
$ljoin["acte_ngap"] = "operations.operation_id = acte_ngap.object_id AND acte_ngap.object_class = 'COperation'";
$where2["operations.annulee"] = " = '0'";
$sejours_consult_ngap = $sejour->loadList($where2, null, null, "sejour_id", $ljoin);
foreach ($sejours_consult_ngap as $key => $_sejour) {
  if (!isset($sejours[$key])) {
    $sejours[$key] = $_sejour;
  }
}

foreach ($sejours as $key => $sejour) {
  /* @var CSejour $sejour*/
  $sejour->loadRefPatient();
  $sejour->loadRefsOperations();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsActes();
  $sejour->loadRefsFactureEtablissement();
  foreach ($sejour->_ref_operations as $keyop => $op) {
    $op->loadRefsActes();
    if (!count($op->_ref_actes)) {
      unset($sejour->_ref_operations[$keyop]);
    }
  }
  foreach ($sejour->_ref_consultations as $keyop => $consult) {
    $consult->loadRefsActes();
    if (!count($consult->_ref_actes)) {
      unset($sejour->_ref_consultations[$keyop]);
    }
  }
  if (!count($sejour->_ref_actes) && !count($sejour->_ref_operations) && !count($sejour->_ref_consultations)) {
    unset($sejours[$key]);
  }
  else {
    $sejour->loadRefPatient();
    $tabSejours[CMbDT::date($sejour->sortie)][$sejour->_id] = $sejour;
    $nbActes[$sejour->_id] = 0;
    $montantSejour[$sejour->_id] = 0;
    // Calcul du nombre d'actes par sejour
    if ($sejour->_ref_actes) {
      if (count($sejour->_ref_actes)) {
        foreach ($sejour->_ref_actes as $acte) {
          if ($acte->executant_id == $_prat_id) {
            $nbActes[$sejour->_id]++;
            $montantSejour[$sejour->_id] += $acte->_montant_facture;
          }
        }
      }
    }
    if ($sejour->_ref_operations) {
      foreach ($sejour->_ref_operations as $operation) {
        if (count($operation->_ref_actes)) {
          $operation->loadRefPlageOp();
          foreach ($operation->_ref_actes as $acte) {
            if ($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
            }
          }
        }
      }
    }
    if ($sejour->_ref_consultations) {
      foreach ($sejour->_ref_consultations as $consult) {
        if (count($consult->_ref_actes)) {
          foreach ($consult->_ref_actes as $acte) {
            if ($acte->executant_id == $_prat_id) {
              $nbActes[$sejour->_id]++;
              $montantSejour[$sejour->_id] += $acte->_montant_facture;
            }
          }
        }
      }
    }
    $totalActes        += $nbActes[$sejour->_id];
    $montantTotalActes += $montantSejour[$sejour->_id];
  }
}

// Tri par date du tableau de sejours
ksort($tabSejours);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("montantTotalActes", $montantTotalActes);
$smarty->assign("totalActes"       , $totalActes);
$smarty->assign("nbActes"          , $nbActes);
$smarty->assign("sejours"          , $tabSejours);
$smarty->assign("montantSejour"    , $montantSejour);
$smarty->assign("praticien"        , $praticien);
$smarty->assign("_date_min"        , $_date_min);
$smarty->assign("_date_max"        , $_date_max);
$smarty->assign("typeVue"          , $typeVue);

if (CAppUI::conf("ref_pays") == 1) {
  $smarty->display("vw_actes_realises.tpl");
}
else {
  $smarty->display("vw_actes_realises2.tpl");
}
