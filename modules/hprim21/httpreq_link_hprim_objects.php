<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

$date_limite = mbDate("- 1 month");
$qte_limite  = 1000;

$g = CGroups::loadCurrent()->_id;
$tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp");
$tag_ipp = str_replace('$g', $g, $tag_ipp);
$tag_sejour = CAppUI::conf("dPplanningOp CSejour tag_dossier");
$tag_sejour = str_replace('$g', $g, $tag_sejour);

// Gestion des m�decins
$hprimMedecin = new CHprim21Medecin();
$where = array();
$where["user_id"] = "IS NULL";
$listHprimMedecins = $hprimMedecin->loadList($where);
$total = count($listHprimMedecins);

// Liaison � un m�decin existant
$nouv = 0;
foreach($listHprimMedecins as $curr_med) {
  $medecin = new CMediusers();
  $ljoin = array();
  $ljoin["users"] = "users.user_id = users_mediboard.user_id";
  $where = array();
  $where["users_mediboard.adeli"] = "= '$curr_med->external_id'";
  $where["users.user_last_name"] = $medecin->_spec->ds->prepare("= %", $curr_med->nom);
  $medecin->loadObject($where, null, null, $ljoin);
  if($medecin->_id) {
    $curr_med->user_id = $medecin->_id;
    $curr_med->store();
    $nouv++;
  }
}

mbTrace($total, "M�decins utilis�s");
mbTrace($nouv , "M�decins rapproch�s");
mbTrace($nouv*100/($total), "% de rapprochement de m�decins");

// Gestion des patients
$hprimPatient = new CHprim21Patient();
$where = array();
$where["date_derniere_modif"] = ">= '$date_limite'";
$where["patient_id"] = "IS NULL";
$order = "date_derniere_modif DESC";
$listHprimPatients = $hprimPatient->loadList($where, $order, $qte_limite);
$total = count($listHprimPatients);

// Liaison � un patient existant
$nouv = 0;
$anc  = 0;
$err  = 0;
foreach($listHprimPatients as $curr_pat) {
  // Recherche si la liaison a d�j� �t� faite
  $idSante400 = new CIdSante400();
  $idSante400->object_class = "CPatient";
  $idSante400->tag = $tag_ipp;
  $idSante400->id400 = "$curr_pat->external_id";
  $idSante400->loadMatchingObject("last_update DESC");
  if($idSante400->_id) {
    $curr_pat->patient_id = $idSante400->object_id;
    $curr_pat->store();
    $anc++;
    continue;
  }
  // Sinon rattachement � un patient existant
  $patient = new CPatient();
  $patient->nom = $curr_pat->nom;
  $patient->prenom = $curr_pat->prenom;
  $patient->naissance = $curr_pat->naissance;
  $patient->loadMatchingPatient();
  if($patient->_id) {
    $idSante400->object_id   = $patient->_id;
    $idSante400->last_update = mbDateTime();
    $idSante400->store();
    $curr_pat->patient_id = $patient->_id;
    $curr_pat->store();
    $nouv++;
  }
}

mbTrace($total, "Patient utilis�s");
mbTrace($anc  , "Patient anciennement rapproch�s");
mbTrace($nouv , "Nouveaux patients rapproch�s");
mbTrace($nouv*100/($total-$anc), "% de rapprochement de patients");
//mbTrace($err, "Erreurs parmis les nouveaux rapproch�s");
//mbTrace($err*100/$total, "% d'erreurs rapprochement");

// Gestion des s�jours
$hprimSejour = new CHprim21Sejour();
$where = array();
$where["date_mouvement"] = ">= '$date_limite'";
$where["sejour_id"] = "IS NULL";
$order = "date_mouvement DESC";
$listHprimSejours = $hprimSejour->loadList($where, $order, $qte_limite);
$total = count($listHprimSejours);

// Liaison � un sejour existant
$nouv    = 0;
$anc     = 0;
$nopat   = 0;
$moresej = 0;
$err     = 0;
foreach($listHprimSejours as $curr_sej) {
  // V�rification que le patient correspondant est bien li�
  $hprimPatient = new CHprim21Patient();
  $hprimPatient->load($curr_sej->hprim21_patient_id);
  if(!$hprimPatient->patient_id) {
    $nopat++;
    continue;
  }
  // Recherche si la liaison a d�j� �t� faite
  $idSante400 = new CIdSante400();
  $idSante400->object_class = "CSejour";
  $idSante400->tag = $tag_sejour;
  $idSante400->id400 = "$curr_sej->external_id";
  $idSante400->loadMatchingObject("last_update DESC");
  if($idSante400->_id) {
    $curr_sej->sejour_id = $idSante400->object_id;
    $curr_sej->store();
    $anc++;
    continue;
  }
  // Sinon rattachement � un sejour existant
  $sejour = new CSejour();
  $where = array();
  $where["patient_id"]    = "= '$hprimPatient->patient_id'";
  $where["entree_prevue"] = "<= '".mbDate("+2 day", $curr_sej->date_mouvement)."'";
  $where["entree_prevue"] = ">= '".mbDate("-2 day", $curr_sej->date_mouvement)."'";
  $where["annule"]        = "= '0'";
  $listSej = $sejour->loadList($where);
  if(count($listSej) > 1) {
    $moresej++;
    continue;
  }
  if(!count($listSej)) {
    continue;
  }
  $sejour = reset($listSej);
  if($sejour->_id) {
    $idSante400->object_id   = $sejour->_id;
    $idSante400->last_update = mbDateTime();
    $idSante400->store();
    $curr_sej->sejour_id = $sejour->_id;
    $curr_sej->store();
    $nouv++;
  }
}

mbTrace($total  , "Sejours utilis�s");
mbTrace($nopat  , "Sejours sans patient rapproch�");
mbTrace($anc    , "Sejours anciennement rapproch�s");
mbTrace($moresej, "Sejours multiples trouv�s");
mbTrace($nouv   , "Nouveaux s�jours rapproch�s");
mbTrace($nouv*100/($total-$nopat-$anc-$moresej), "% de rapprochement de s�jours");

?>