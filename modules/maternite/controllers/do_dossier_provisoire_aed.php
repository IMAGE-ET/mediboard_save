<?php

/**
 * Cr�ation d'un dossier provisoire
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$sejour_maman_id = CValue::post("sejour_maman_id");
$operation_id    = CValue::post("operation_id");
$callback        = CValue::post("callback");
$prenom          = CValue::post("prenom");
$nom             = CValue::post("nom");
$praticien_id    = CValue::post('praticien_id');

$sejour = new CSejour();
$sejour->load($sejour_maman_id);
 
$parturiente = $sejour->loadRefPatient();
$grossesse   = $sejour->loadRefGrossesse();
$curr_affect = $sejour->loadRefCurrAffectation();

/**
 * Fonction utilitaire pour la sauvegarde rapide d'un object avec g�n�ration du message
 *
 * @param CMbObject $object Objet � enregister
 *
 * @return void
 */
function storeObject($object) {
  $title = $object->_id ? "-msg-modify" : "-msg-create";
  if ($msg = $object->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    echo CAppUI::getMsg();
    CApp::rip();
  }
  
  CAppUI::setMsg(CAppUI::tr(get_class($object) . $title), UI_MSG_OK);
}

$terme_prevu = $grossesse->terme_prevu;

// Cr�ation de la naissance provisoire:
//   1. Cr�er le nouveau patient (b�b�)
//   2. Cr�er le s�jour du patient
//   3. Cr�er la naissance

$patient = new CPatient();
$patient->nom       = $nom ? $nom : $parturiente->nom;
$patient->prenom    = $prenom ? $prenom : "provi";
$patient->civilite  = "enf";
$patient->naissance = $terme_prevu;
storeObject($patient);

if (!$prenom) {
  $patient->prenom = $patient->_id;
  $patient->store();
}

$sejour_enfant = new CSejour();
$sejour_enfant->patient_id    = $patient->_id;
$sejour_enfant->entree_prevue = CMbDT::dateTime();
$sejour_enfant->sortie_prevue = max($sejour_enfant->entree_prevue, $sejour->sortie);
$sejour_enfant->praticien_id  = $praticien_id ? $praticien_id : $sejour->praticien_id;
$sejour_enfant->group_id      = $sejour->group_id;
$sejour_enfant->_naissance    = true;
storeObject($sejour_enfant);

$naissance = new CNaissance();
$naissance->grossesse_id     = $grossesse->_id;
$naissance->sejour_maman_id  = $sejour->_id;
$naissance->sejour_enfant_id = $sejour_enfant->_id;
$naissance->operation_id     = $operation_id;
$naissance->num_naissance    = CAppUI::conf("maternite CNaissance num_naissance") + $naissance->countList();
storeObject($naissance);

echo CAppUI::getMsg();

if ($callback) {
  CAppUI::callbackAjax($callback);
}

CApp::rip();
