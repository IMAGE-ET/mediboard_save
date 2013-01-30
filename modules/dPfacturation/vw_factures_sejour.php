<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();

$date_min           = CValue::getOrSession("_date_min", mbDate());
$date_max           = CValue::getOrSession("_date_max", mbDate());
$etat               = CValue::getOrSession("etat", "ouvert");
$etat_cloture       = CValue::getOrSession("etat_cloture", 1);
$etat_ouvert        = CValue::getOrSession("etat_ouvert", 1);
$facture_id         = CValue::getOrSession("facture_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", "-1");

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT);

//Tri des factures ayant un chir dans une de ses consultations
$factures= array();
$facture = new CFactureEtablissement();

$where = array();
$where["ouverture"] = "BETWEEN '$date_min' AND '$date_max'";

if ($etat_cloture && !$etat_ouvert) {
  $where["cloture"] = "BETWEEN '$date_min' AND '$date_max'";
}
elseif ($etat_cloture && $etat_ouvert) {
   $where[] = "cloture BETWEEN '$date_min' AND '$date_max' OR cloture IS NULL";
}
elseif (!$etat_cloture && $etat_ouvert) {
  $where["cloture"] = "IS NULL";
}

if ($chirSel) {
  $where["praticien_id"] =" = '$chirSel' ";
  if ($patient_id) {
    $where["patient_id"] =" = '$patient_id' ";
  }
  $factures = $facture->loadList($where, "cloture DESC", 100);
}
else {
  $where["patient_id"] = "= '$patient_id'";  
  $factures = $facture->loadList($where , "ouverture ASC", 50);
}

foreach ($factures as $_facture) {
  $_facture->loadRefPatient();
}

if ($no_finish_reglement) {
  foreach ($factures as $key => $_facture) {
    $_facture->loadRefsReglements();
    if ($_facture->_du_restant_patient != 0 ) {
      unset($factures[$key]);
    }
  }
}

$assurances_patient = array();
if ($facture_id) {
  $facture->load($facture_id);  
  $facture->loadRefs();
  $facture->_ref_patient->loadRefsCorrespondantsPatient();
}

$reglement = new CReglement();

$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

$filter = new CSejour();
$filter->_date_min = $date_min;
$filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("listChirs"     , $listChir);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("patient"       , $patient);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);
$smarty->assign("etat_ouvert"   , $etat_ouvert);
$smarty->assign("etat_cloture"  , $etat_cloture);
$smarty->assign("date"          , mbDate());
$smarty->assign("filter"        , $filter);
$smarty->assign("no_finish_reglement"      ,$no_finish_reglement);

$smarty->display("vw_factures.tpl");
