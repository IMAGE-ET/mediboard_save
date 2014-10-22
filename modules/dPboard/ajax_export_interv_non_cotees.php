<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Board
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$chirSel   = CValue::getOrSession("praticien_id", null);
$all_prats = CValue::get("all_prats", 0);
$fin       = CMbDT::date("+1 day", CValue::get("fin"));
$debut     = CValue::get("debut");

$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chirSel = $mediuser->_id;
}

$user = new CMediusers();
$user->load($chirSel);

$ljoin = array();
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where = array();
$where["operations.date"] = "BETWEEN '$debut' AND '$fin'";
$where["operations.annulee"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens(PERM_READ);
  $in_prats = CSQLDataSource::prepareIn(array_keys($prats));
  $where["operations.chir_id"] = "$in_prats";
  $where[] = "operations.anesth_id IS NULL OR operations.anesth_id $in_prats";
}
else {
  if ($user->isAnesth()) {
    $where[] = "operations.chir_id = '$user->_id' OR
      operations.anesth_id = '$user->_id' OR
      (operations.anesth_id IS NULL && plagesop.anesth_id = '$user->_id')";
  }
  else {
    $where["operations.chir_id"] = "= '$user->_id'";
  }
}

/** @var COperation[] $interventions */
$operation = new COperation();
$interventions = $operation->loadList($where, null, null, null, $ljoin);

CStoredObject::massLoadFwdRef($interventions, "plageop_id");

$where = array();
if (!$all_prats) {
  //$where["executant_id"] = "= '$user->_id'";
  $where["code_activite"] = $user->_is_anesth ? "= '4'" : "!= '4'";
}

CStoredObject::massCountBackRefs($interventions, "actes_ccam", $where);

foreach ($interventions as $key => $_interv) {
  $_plage = $_interv->loadRefPlageOp();
  $_interv->loadExtCodesCCAM();

  $codes_ccam = $_interv->_ext_codes_ccam;

  // Nombre d'acte cot�s par le praticien et r�initialisation du count pour le cache
  $nb_actes_ccam = $_interv->_count["actes_ccam"];
  $_interv->_count["actes_ccam"] = null;

  // Aucun acte pr�vu ou cot�
  if (!count($codes_ccam) && !$_interv->_count_actes) {
    continue;
  }

  // Actes pr�vus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");

  $nbCodes = 0;
  foreach ($activites as $_activite) {
    if ($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach ($_activite as $_key_activite => $_type_activite) {
      if ($user->_is_anesth && $_key_activite == 4) {
        $nbCodes++;
      }
      if (!$user->_is_anesth && $_key_activite != 4) {
        $nbCodes++;
      }
    }
  }

  // Si tout est cot�, on n'affiche pas l'op�ration
  if ($nb_actes_ccam >= $nbCodes) {
    unset($interventions[$key]);
    continue;
  }

  $_interv->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
}

/** @var CSejour[] $sejours */
$sejours = CStoredObject::massLoadFwdRef($interventions, "sejour_id");
CStoredObject::massLoadFwdRef($sejours, "patient_id");

CStoredObject::massLoadBackRefs($interventions, "actes_ccam");

foreach ($interventions as $_interv) {
  $_interv->loadRefSejour();
  $_interv->loadRefChir()->loadRefFunction();
  $_interv->loadRefAnesth()->loadRefFunction();
  $_interv->loadRefPatient();

  // Liste des actes CCAM cot�s
  foreach ($_interv->loadRefsActesCCAM() as $_acte) {
    $_acte->loadRefExecutant();
  }
}

$interventions = CModelObject::naturalSort($interventions, array("_datetime"));

$ljoin = array();
$ljoin["sejour"] = "consultation.sejour_id = sejour.sejour_id";
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

$where = array();
$where["sejour.entree"] = " BETWEEN '$debut' AND '$fin'";
$where["sejour.annule"] = "= '0'";
$where["consultation.annule"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens(PERM_READ);
  $where["plageconsult.chir_id"]   = CSQLDataSource::prepareIn(array_keys($prats));
}
else {
  $where["plageconsult.chir_id"] = "= '$user->_id'";
}

/* @var CConsultation[] $consultations*/
$consultation = new CConsultation();
$consultations = $consultation->loadList($where, null, null, null, $ljoin);

/** @var CPlageConsult[] $plages */
$plages = CStoredObject::massLoadFwdRef($consultations, "plageconsult_id");
CStoredObject::massLoadFwdRef($consultations, "sejour_id");
CStoredObject::massLoadFwdRef($consultations, "patient_id");
// Pr�-chargement des users
$where = array("user_id" => CSQLDataSource::prepareIn(CMbArray::pluck($plages, "chir_id")));
$user->loadList($where);
/** @var CMediusers[] $chirs */
$chirs = CStoredObject::massLoadFwdRef($plages, "chir_id");
CStoredObject::massLoadFwdRef($chirs, "function_id");
CStoredObject::massLoadBackRefs($consultations, "actes_ccam");

foreach ($consultations as $key => $_consult) {
  // On ignore les consultation ayant des actes NGAP
  if ($_consult->countBackRefs("actes_ngap")) {
    unset($consultations[$key]);
    continue;
  }

  // Chargemement des codes CCAM
  $_consult->loadExtCodesCCAM();
  $codes_ccam = $_consult->_ext_codes_ccam;

  // Nombre d'acte cot�s par le praticien et r�initialisation du count pour le cache
  $nb_actes_ccam = count($_consult->loadRefsActesCCAM());

  // Aucun acte pr�vu ou cot�
  if (!count($codes_ccam) && !$_consult->_count_actes) {
    $_consult->loadRefSejour();
    $_consult->loadRefPraticien()->loadRefFunction();
    $_consult->loadRefPatient();
    continue;
  }

  // Actes pr�vus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");
  $nbCodes = 0;
  foreach ($activites as $_activite) {
    if ($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach ($_activite as $_key_activite => $_type_activite) {
      if ($user->_is_anesth && $_key_activite == 4) {
        $nbCodes++;
      }
      if (!$user->_is_anesth && $_key_activite != 4) {
        $nbCodes++;
      }
    }
  }

  // Si tout est cot�, on n'affiche pas l'op�ration
  if ($nb_actes_ccam >= $nbCodes) {
    unset($consultations[$key]);
    continue;
  }

  $_consult->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
  $_consult->loadRefsFwd();
  $_consult->loadRefSejour();
  $_consult->loadRefPraticien()->loadRefFunction();

  // Liste des actes CCAM cot�es
  foreach ($_consult->loadRefsActesCCAM() as $_acte) {
    $_acte->loadRefExecutant();
  }
}

$consultations = CModelObject::naturalSort($consultations, array("_date"));

$csv = new CCSVFile();

$line = array(
  "Praticiens",
  "Patient",
  "Ev�nement",
  "Actes Non cot�s",
  "Codes pr�vus",
  "Actes cot�s"
);
if (!$all_prats) {
  unset($line[0]);
}
$csv->writeLine($line);

foreach ($interventions as $_interv) {
  $line = array();
  if ($all_prats) {
    $chir = $_interv->_ref_chir->_view;
    if ($_interv->_ref_anesth->_id) {
      $chir .= "\n" . $_interv->_ref_anesth->_view;
    }
    $line[] = $chir;
  }
  $line[] = $_interv->_ref_patient->_view;

  $interv = $_interv->_view;
  if ($_interv->_ref_sejour->libelle) {
    $interv .= "\n".$_interv->_ref_sejour->libelle;
  }
  if ($_interv->libelle) {
    $interv .= "\n".$_interv->libelle;
  }
  $line[] = $interv;

  $line[] = (!$_interv->_count_actes && !$_interv->_ext_codes_ccam) ? "Aucun pr�vu" : $_interv->_actes_non_cotes."acte(s)";

  $actes = "";
  foreach ($_interv->_ext_codes_ccam as $code) {
    $actes .= $actes == "" ? "" : "\n";
    $actes .= "$code->code";
  }
  $line[] = $actes;

  $actes_cotes = "";
  foreach ($_interv->_ref_actes_ccam as $_acte) {
    $code .= $actes_cotes == "" ? "" : "\n";
    $code .= $_acte->code_acte."-".$_acte->code_activite."-".$_acte->code_phase;
    if ($_acte->modificateurs) {
      $code .= " MD:".$_acte->modificateurs;
    }
    if ($_acte->montant_depassement) {
      $code .= " DH:".$_acte->montant_depassement;
    }
    $actes_cotes .= "$code";
  }
  $line[] = $actes_cotes;

  $csv->writeLine($line);
}

foreach ($consultations as $_consult) {
  $line = array();

  if ($all_prats) {
    $line[] = $_consult->_ref_chir->_view;
  }

  $line[] = $_consult->_ref_patient->_view;

  $view = "Consultation le ".CMbDT::format($_consult->_datetime, "%d/%m/%Y");
  if ($_consult->_ref_sejour && $_consult->_ref_sejour->libelle) {
    $view .= $_consult->_ref_sejour->libelle;
  }
  $line[] = $view;

  $line[] = (!$_consult->_count_actes && !$_consult->_ext_codes_ccam) ? "Aucun pr�vu" : $_consult->_actes_non_cotes."acte(s)";

  $actes = "";
  foreach ($_consult->_ext_codes_ccam as $code) {
    $actes .= $actes == "" ? "" : "\n";
    $actes .= "$code->code";
  }
  $line[] = $actes;

  $actes_cotes = "";
  foreach ($_consult->_ref_actes_ccam as $_acte) {
    $code .= $actes_cotes == "" ? "" : "\n";
    $code .= $_acte->code_acte."-".$_acte->code_activite."-".$_acte->code_phase;
    if ($_acte->modificateurs) {
      $code .= " MD:".$_acte->modificateurs;
    }
    if ($_acte->montant_depassement) {
      $code .= " DH:".$_acte->montant_depassement;
    }
    $actes_cotes .= "$code";
  }
  $line[] = $actes_cotes;

  $csv->writeLine($line);
}

$csv->stream("export-intervention_non_cotes-".$debut."-".$fin);