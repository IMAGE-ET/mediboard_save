<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$patient_id = mbGetValueFromGet("patient_id", null);
$nom        = mbGetValueFromGet("nom"       , null);
$prenom     = mbGetValueFromGet("prenom"    , null);
$naissance  = mbGetValueFromGet("naissance" , "0000-00-00");

$textDifferent = null;
if($patient_id) {
  $oldPat = new CPatient();
  $oldPat->load($patient_id);
  if(!$oldPat->checkSimilar($nom, $prenom)) {
    $textDifferent = "Le nom et/ou le pr�nom sont tr�s diff�rents de" .
        "\n\t$oldPat->_view" .
        "\nVoulez-vous tout de m�me sauvegarder ?";
  }
}

$patientMatch = new CPatient();
$patientMatch->patient_id = $patient_id;
$patientMatch->nom        = $nom;
$patientMatch->prenom     = $prenom; 
$patientMatch->naissance  = $naissance;

$textMatching = null;

if (CAppUI::conf('dPpatients CPatient identitovigilence') == "doublons" ) {
	$ds = $this->_spec->ds;
	if ($patient_id) {
		$where["patient_id"] = " != '$patient_id'";
	}
  $where["nom"]        = $ds->prepare("LIKE %", preg_replace("/\W/", "%", $nom));
  $where["prenom"]     = $ds->prepare("LIKE %", preg_replace("/\W/", "%", $prenom));
  $where["naissance"]  = $ds->prepare("= %", $naissance);
       
  if($patientMatch->countList($where) > 0) {
	  $textMatching = "Doublons d�tect�s.";
	  $textMatching .= "\nVous ne pouvez pas sauvegarder le patient.";
	}
} else {
	$siblings = $patientMatch->getSiblings();
	
	if(count($siblings) != 0) {
	  $textMatching = "Risque de doublons :";
	  foreach($siblings as $key => $value) {
	    $textMatching .= "\n\t $value->nom $value->prenom" .
	      " n�(e) le ". mbDateToLocale($value->naissance) .
	      "\n\t\thabitant ". strtr($value->adresse, "\n", "-") .
	      "- $value->cp $value->ville";
	  }
	  $textMatching .= "\nVoulez-vous tout de m�me sauvegarder ?";
	}
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("textDifferent", $textDifferent);
$smarty->assign("textMatching" , $textMatching );

$smarty->display("httpreq_get_siblings.tpl");
?>