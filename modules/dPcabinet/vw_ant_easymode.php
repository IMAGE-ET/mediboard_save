<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

// @todo � transf�rer dans  dPpatient
// En l'�tat on ne peut pas v�rifier les droits sur dPcabinet
// CCanDo::checkRead();

$patient_id = CValue::get("patient_id");
$consult_id = CValue::get("consult_id");

// On charge le praticien
$user = CAppUI::$user;
$user->loadRefs();
$canUser = $user->canDo();

$consult = new CConsultation;
if ($consult_id) {
  $consult->load($consult_id);
  $consult->loadRefsFwd();
}

// Chargement des aides � la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($user->_id);

$aides_antecedent = $antecedent->_aides_all_depends["rques"] ? $antecedent->_aides_all_depends["rques"] : array();

// On charge le patient pour connaitre ses ant�cedents et traitements actuels
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->loadRefsAntecedents();
$dossier_medical->loadRefsTraitements();

$applied_antecedents = array();

foreach ($dossier_medical->_ref_antecedents_by_type as $list) {
  foreach ($list as $a) {
    if (!isset($applied_antecedents[$a->type]))   $applied_antecedents[$a->type] = array();

    $applied_antecedents[$a->type][] = $a->rques;
  }
}

$order_mode_grille = CAppUI::pref("order_mode_grille");

$fill_pref = $order_mode_grille != "";

$order_decode = array();

if ($fill_pref) {
  $order_decode = get_object_vars(json_decode($order_mode_grille));
  $keys = array_keys($order_decode);
  
  foreach ($keys as $_key => $_value) {
    if ($_value == "_empty_") {
      $keys[$_key] = "";
    }
  }
  $keys = array_flip($keys);
  
  $antecedent->_count_rques_aides = array_replace_recursive($keys, $antecedent->_count_rques_aides);
}

foreach ($aides_antecedent as $_depend_1 => $_aides_by_depend_1) {
  if ($fill_pref) {
    $key = $_depend_1 == "" ? "_empty_" : $_depend_1;
    if (isset($order_decode[$key])) {
      $keys = explode("|", $order_decode[$key]);
      $keys = array_flip($keys);
      $aides_antecedent[$_depend_1] = array_replace_recursive($keys, $_aides_by_depend_1);
      $_aides_by_depend_1 = $aides_antecedent[$_depend_1];
    }
  }
  foreach ($_aides_by_depend_1 as $_depend_2 => $_aides_by_depend_2) {
    if (!is_array($_aides_by_depend_2)) {
      continue;
    }

    foreach ($_aides_by_depend_2 as $_aide) {
      if (isset($applied_antecedents[$_depend_1])) {
        foreach ($applied_antecedents[$_depend_1] as $_atcd) {
          if ($_atcd == $_aide->text || strpos($_atcd, $_aide->text) === 0) {
            $_aide->_applied = true;
          }
        }
      }
    }
  }
}

$applied_traitements = array();
foreach ($dossier_medical->_ref_traitements as $a) {
  $applied_traitements[$a->traitement] = true;
}

$traitement = new CTraitement();
$traitement->loadAides($user->_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("aides_antecedent", $aides_antecedent);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);
$smarty->assign("applied_antecedents", $applied_antecedents);
$smarty->assign("applied_traitements", $applied_traitements);
$smarty->assign("patient", $patient);
$smarty->assign("consult", $consult);
$smarty->assign("user_id", $user->_id);
$smarty->assign("order_mode_grille", $order_mode_grille);

$smarty->display("vw_ant_easymode.tpl");
