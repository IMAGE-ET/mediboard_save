<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$patient_id      = CValue::get("patient_id", null);
$nom             = CValue::get("nom"       , null);
$nom_jeune_fille = CValue::get("nom_jeune_fille", null);
$prenom          = CValue::get("prenom"    , null);
$prenom_2        = CValue::get("prenom_2"  , null);
$prenom_3        = CValue::get("prenom_3"  , null);
$prenom_4        = CValue::get("prenom_4"  , null);
$naissance       = CValue::get("naissance" , "0000-00-00");

$textDifferent = null;
if ($patient_id) {
  $oldPat = new CPatient();
  $oldPat->load($patient_id);
  if (!$oldPat->checkSimilar($nom, $prenom)) {
    $textDifferent = "Le nom et/ou le pr�nom sont tr�s diff�rents de" .
        "\n\t$oldPat->_view" .
        "\nVoulez-vous tout de m�me sauvegarder ?";
  }
}

$patientMatch = new CPatient();
$patientMatch->patient_id = $patient_id;
if (CAppUI::conf('dPpatients CPatient function_distinct')) {
  $function_id = CMediusers::get()->function_id;
  $patientMatch->function_id = $function_id;
}
$patientMatch->nom        = $nom;
$patientMatch->nom_jeune_fille = $nom_jeune_fille;
$patientMatch->prenom     = $prenom; 
$patientMatch->prenom_2   = $prenom_2; 
$patientMatch->prenom_3   = $prenom_3;
$patientMatch->prenom_4   = $prenom_4;  
$patientMatch->naissance  = $naissance;

$textMatching = $textSiblings = '';
if (CAppUI::conf('dPpatients CPatient identitovigilence') == "doublons" ) {
  if ($patientMatch->loadMatchingPatient(true) > 0) {
    $textMatching = "Doublons d�tect�s.";
    $textMatching .= "\nVous ne pouvez pas sauvegarder le patient.";
  }

  if (!$textMatching) {
    $textSiblings = patientGetSiblings($patientMatch);
  }
}
else {
  $textSiblings = patientGetSiblings($patientMatch);
}

/**
 * Informations sur les possibilit� de doublons d'un patient
 *
 * @param CPatient $patientMatch Patient � v�rifier
 *
 * @return null|string
 */
function patientGetSiblings($patientMatch) {
  $siblings = $patientMatch->getSiblings();

  $textSiblings = null;

  if (count($siblings) != 0) {
    $textSiblings = "Risque de doublons :";
    foreach ($siblings as $value) {
      $textSiblings .= "\n\t - $value->nom $value->prenom ";
      if ($value->nom_jeune_fille) {
        $textSiblings .= "($value->nom_jeune_fille)";
      }

      $textSiblings .= " n�(e) le ". CMbDT::dateToLocale($value->naissance) .
      "\n\t\thabitant ". strtr($value->adresse, "\n", "-") .
      "- $value->cp $value->ville";
    }
  }

  return $textSiblings;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("textSiblings", $textSiblings);
$smarty->assign("textMatching", $textMatching );

$smarty->display("httpreq_get_siblings.tpl");
