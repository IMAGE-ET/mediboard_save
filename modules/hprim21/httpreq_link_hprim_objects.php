<?php

/**
 * Liaison des object Hprim21 aux objets m�tiers
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

CApp::setTimeLimit(240);
CApp::setMemoryLimit("512M");

$date_limite = CMbDT::date("- 1 month");
$qte_limite  = 1000;

$tag_ipp    = CPatient::getTagIPP();
$tag_sejour = CSejour::getTagNDA();

// Gestion des m�decins
$hprimMedecin = new CHprim21Medecin();
$where = array();
$where["user_id"] = "IS NULL";
/** @var CHprim21Medecin[] $listHprimMedecins */
$listHprimMedecins = $hprimMedecin->loadList($where);
$total = count($listHprimMedecins);

// Liaison � un m�decin existant
$nouv = 0;

CHprim21Object::massLoadExchanges($listHprimMedecins);
foreach ($listHprimMedecins as $_medecin) {
  $_medecin->loadRefExchange();

  $mediuser = CMediusers::loadFromAdeli($_medecin->external_id);
  if ($mediuser->_id) {
    $_medecin->user_id = $mediuser->_id;
    $_medecin->store();
    $nouv++;
  }

  $echg_hprim = $_medecin->_ref_echange_hprim21;
  if (!$echg_hprim->object_id) {
    $echg_hprim->object_class = "CMediusers";
    $echg_hprim->object_id    = $mediuser->_id;
    $echg_hprim->store();
  }
}

CAppUI::stepAjax("M�decins utilis�s : '$total'");
CAppUI::stepAjax("M�decins rapproch�s : '$nouv'");
if ($total > 0) {
  CAppUI::stepAjax($nouv * 100/ ($total).", %% de rapprochement de m�decins");
}

// Gestion des patients
$hprimPatient                 = new CHprim21Patient();
$where                        = array();
$where["date_derniere_modif"] = ">= '$date_limite'";
$where["patient_id"]          = "IS NULL";
$order                        = "date_derniere_modif DESC";
/** @var CHprim21Patient[] $listHprimPatients */
$listHprimPatients = $hprimPatient->loadList($where, $order, $qte_limite);
$total = count($listHprimPatients);

// Liaison � un patient existant
$nouv = $anc = 0;
CHprim21Object::massLoadExchanges($listHprimPatients);
foreach ($listHprimPatients as $_patient) {
  $_patient->loadRefExchange();
  $echg_hprim = $_patient->_ref_echange_hprim21;

  // Recherche si la liaison a d�j� �t� faite
  $IPP = CIdSante400::getMatch("CPatient", $tag_ipp, $_patient->external_id);
  if ($IPP->_id) {
    $anc++;
    
    continue;
  }
  else {
    // Sinon rattachement � un patient existant
    $patient = new CPatient();
    $patient->nom       = $_patient->nom;
    $patient->prenom    = $_patient->prenom;
    $patient->naissance = $_patient->naissance;
    $return = $patient->loadMatchingPatient();

    if ($return == 1) {
      $IPP->object_id   = $patient->_id;
      $IPP->store();

      $nouv++;
    }
  }

  $_patient->patient_id = $IPP->object_id;
  $_patient->store();

  if (!$echg_hprim->object_id) {
    $echg_hprim->object_class = $IPP->object_class;
    $echg_hprim->object_id    = $IPP->object_id;
    $echg_hprim->id_permanent = $IPP->id400;
    $echg_hprim->store();
  }
}

CAppUI::stepAjax("Patient utilis�s : '$total'");
CAppUI::stepAjax("Patient anciennement rapproch�s : '$anc'");
CAppUI::stepAjax("Nouveaux patients rapproch�s : '$nouv'");

if (($total - $anc) > 0) {
  CAppUI::stepAjax($nouv * 100 / ($total - $anc)."%% de rapprochement de patients", UI_MSG_OK);
}

// Gestion des s�jours
$hprimSejour = new CHprim21Sejour();
$where = array();
$where["date_mouvement"] = ">= '$date_limite'";
$where["sejour_id"] = "IS NULL";
$order = "date_mouvement DESC";
/** @var CHprim21Sejour[] $listHprimSejours */
$listHprimSejours = $hprimSejour->loadList($where, $order, $qte_limite);
$total = count($listHprimSejours);

// Liaison � un sejour existant
$nouv = $anc = $nopat = $moresej = $err = 0;
CHprim21Object::massLoadExchanges($listHprimSejours);
foreach ($listHprimSejours as $_sejour) {
  $echg_hprim = $_sejour->loadRefExchange();

  // V�rification que le patient correspondant est bien li�
  $hprimPatient = new CHprim21Patient();
  $hprimPatient->load($_sejour->hprim21_patient_id);
  if (!$hprimPatient->patient_id) {
    $nopat++;
    continue;
  }
  // Recherche si la liaison a d�j� �t� faite

  $nda = CIdSante400::getMatch("CSejour", $tag_sejour, $_sejour->external_id);
  if ($nda->_id) {
    $_sejour->sejour_id = $nda->object_id;
    $_sejour->store();
    
    $echg_hprim->object_class = $nda->object_class;
    $echg_hprim->object_id    = $nda->object_id;
    $echg_hprim->id_permanent = $nda->id400;
    $echg_hprim->store();
    
    $anc++;
    continue;
  }

  // Sinon rattachement � un sejour existant
  $sejour = new CSejour();
  $where = array();
  $where["patient_id"] = "= '$hprimPatient->patient_id'";
  $date_min            = CMbDT::date("-2 day", $_sejour->date_mouvement);
  $date_max            = CMbDT::date("+2 day", $_sejour->date_mouvement);
  $where["entree"]     = "BETWEEN '$date_min' AND '$date_max'";
  $where["type"]       = "!= 'consult'";
  $where["annule"]     = "= '0'";

  $listSej = $sejour->loadList($where);

  if (count($listSej) > 1) {
    $moresej++;
    continue;
  }
  if (!count($listSej)) {
    continue;
  }
  $sejour = reset($listSej);
  if ($sejour->_id) {
    $nda->object_id = $sejour->_id;
    $nda->store();

    $_sejour->sejour_id = $sejour->_id;
    $_sejour->store();

    $echg_hprim->object_class = $nda->object_class;
    $echg_hprim->object_id    = $nda->object_id;
    $echg_hprim->id_permanent = $nda->id400;
    $echg_hprim->store();
    
    $nouv++;
  }
}

CAppUI::stepAjax("S�jours utilis�s : '$total'");
CAppUI::stepAjax("S�jours sans patient rapproch�s : '$nopat'");
CAppUI::stepAjax("S�jours anciennement rapproch�s : '$anc'");
CAppUI::stepAjax("S�jours multiples trouv�s : '$moresej'");
CAppUI::stepAjax("Nouveaux s�jours rapproch�s : '$nouv'");

if (($total-$nopat-$anc-$moresej) > 0) {
  CAppUI::stepAjax($nouv * 100 / ($total - $nopat - $anc - $moresej)." %% de rapprochement de s�jours");
}