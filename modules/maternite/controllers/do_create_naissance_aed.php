<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$naissance_id = CValue::post("naissance_id");
$operation_id = CValue::post("operation_id");
$patient_id   = CValue::post("patient_id");
$constantes_id = CValue::post("constantes_medicales_id");
$sexe         = CValue::post("sexe");
$heure        = CValue::post("heure");
$rang         = CValue::post("rang");
$naissance    = CValue::post("naissance");
$nom          = CValue::post("nom");
$prenom       = CValue::post("prenom");
$poids        = CValue::post("poids");
$taille       = CValue::post("taille");
$perimetre_cranien = CValue::post("perimetre_cranien");

$operation = new COperation;
$operation->load($operation_id);

$parturiente = $operation->loadRefPatient();
$sejour      = $operation->loadRefSejour();
$grossesse   = $sejour->loadRefGrossesse();
$curr_affect = $sejour->loadRefCurrAffectation();

$datetime = mbDateTime();
$date     = mbDate($datetime);

function storeObject($object) {
  $title = $object->_id ? "-msg-modify" : "-msg-create";
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
  }
  
  CAppUI::setMsg(CAppUI::tr(get_class($object) . $title), UI_MSG_OK);
}

// Cinq �tapes pour la cr�ation de la naissance :
//   1. Cr�er le nouveau patient (b�b�)
//   2. Cr�er le relev� de constantes
//   3. Cr�er le s�jour du patient
//   4. Cr�er l'affectation du s�jour
//   5. Cr�er la naissance

if (!$naissance_id) {
  // Etape 1 (patient)
  $patient = new CPatient;
  
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->sexe = $sexe;
  $patient->naissance = $date;
  storeObject($patient);
  
  // Etape 2 (constantes)
  $constantes = new CConstantesMedicales;
  $constantes->patient_id = $patient->_id;
  $constantes->datetime = "now";
  $constantes->poids = $poids;
  $constantes->taille = $taille;
  $constantes->perimetre_cranien = $perimetre_cranien;
  storeObject($constantes);
  
  // Etape 3 (s�jour)
  $sejour_enfant = new CSejour;
  $sejour_enfant->entree_reelle = $datetime;
  $sejour_enfant->sortie_prevue = mbDateTime("+1 month", $datetime);
  $sejour_enfant->patient_id = $patient->_id;
  $sejour_enfant->praticien_id = $sejour->praticien_id;
  $sejour_enfant->group_id = $sejour->group_id;
  storeObject($sejour_enfant);
  
  // Etape 4 (affectation)
  $affectation = new CAffectation;
  $affectation->entree = $sejour_enfant->entree_reelle;
  $affectation->sortie = $sejour_enfant->sortie_prevue;
  $affectation->lit_id = $curr_affect->lit_id;
  $affectation->sejour_id = $sejour_enfant->_id;
  $affectation->parent_affectation_id = $curr_affect->_id;
  storeObject($affectation);
  
  // Etape 5 (naissance)
  $naissance = new CNaissance;
  $naissance->sejour_enfant_id = $sejour_enfant->_id;
  $naissance->operation_id = $operation_id;
  $naissance->grossesse_id = $grossesse->_id;
  $naissance->rang         = $rang;
  $naissance->heure        = $heure;
  storeObject($naissance);
}
// Modification d'une naissance
else {
  $naissance = new CNaissance;
  $naissance->load($naissance_id);
  $naissance->rang = $rang;
  $naissance->heure = $heure;
  storeObject($naissance);
  
  $patient = new CPatient;
  $patient->load($patient_id);
  $patient->nom = $nom;
  $patient->prenom = $prenom;
  $patient->nom = $nom;
  storeObject($patient);
  
  $constantes = new CConstantesMedicales;
  $constantes->load($constantes_id);
  $constantes->poids = $poids;
  $constantes->taille = $taille;
  $constantes->perimetre_cranien = $perimetre_cranien;
  storeObject($constantes);
}
echo CAppUI::getMsg();

CApp::rip();

?>