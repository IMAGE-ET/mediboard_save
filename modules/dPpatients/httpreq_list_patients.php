<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain OLLIVIER
*/

/// @todo: Ce fichier ressemble beaucoup � vw_idx_patient.php, il faudrait factoriser

global $can, $g;
$can->needsRead();

$board = CValue::get("board", 0);

$patient_id = CValue::getOrSession("patient_id");

// Patients
$patient_nom       = CValue::getOrSession("nom"       , "");
$patient_prenom    = CValue::getOrSession("prenom"    , "");

$patient_ville     = CValue::getOrSession("ville"     , "");
$patient_cp        = CValue::getOrSession("cp"        , "");
$patient_day       = CValue::getOrSession("Date_Day"  , "");
$patient_month     = CValue::getOrSession("Date_Month", "");
$patient_year      = CValue::getOrSession("Date_Year" , "");
$patient_ipp       = CValue::get("patient_ipp");
$patient_naissance = null;
$useVitale         = CValue::get("useVitale");

$patVitale = new CPatient;

$patient = new CPatient;
if ($new = CValue::get("new")) {
  $patient->load(null);
  CValue::setSession("patient_id", null);
  CValue::setSession("selClass", null);
  CValue::setSession("selKey", null);
} else {
  $patient->load($patient_id);
}

// Champs vitale
if ($useVitale) {
  $patVitale->getPropertiesFromVitale();
  $patVitale->updateFormFields();
  $patient_nom    = $patVitale->nom;
  $patient_prenom = $patVitale->prenom;
  CValue::setSession("nom", $patVitale->nom);
  CValue::setSession("prenom", $patVitale->prenom);
  $patVitale->loadFromIdVitale();
}

// Recherhche par IPP
if($patient_ipp && !$useVitale && CModule::getInstalled("dPsante400")){
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;
  
  $patient = new CPatient;
  $patient->_IPP = $patient_ipp;
  $patient->loadFromIPP();
  if ($patient->_id) {
    CValue::setSession("patient_id", $patient->_id);
    $patients[$patient->_id] = $patient; 
  }
} 

// Recherche par trait standard
else {
	$where        = array();
	$whereSoundex = array();
	$soundexObj   = new soundex2();
  // Limitation du nombre de caract�res
  $patient_nom_search    = trim($patient_nom);
  $patient_prenom_search = trim($patient_prenom);
  if ($limit_char_search = CAppUI::conf("dPpatients CPatient limit_char_search")) {
    $patient_nom_search    = substr($patient_nom_search   , 0, $limit_char_search);
    $patient_prenom_search = substr($patient_prenom_search, 0, $limit_char_search);
  }
  
  if ($patient_nom_search) {
    $patient_nom_soundex = $soundexObj->build($patient_nom_search);
    $where[] = "`nom` LIKE '$patient_nom_search%' OR `nom_jeune_fille` LIKE '$patient_nom_search%'";
    $whereSoundex[] = "`nom_soundex2` LIKE '$patient_nom_soundex%' OR `nomjf_soundex2` LIKE '$patient_nom_soundex%'";
  }
  
  if ($patient_prenom_search) {
    $patient_prenom_soundex = $soundexObj->build($patient_prenom_search);
    $where["prenom"]                 = "LIKE '$patient_prenom_search%'";
    $whereSoundex["prenom_soundex2"] = "LIKE '$patient_prenom_soundex%'";
  }
  
  if ($patient_year || $patient_month || $patient_day) {
    $patient_naissance = 
      CValue::first($patient_year , "%") . "-" .
      CValue::first($patient_month, "%") . "-" .
      CValue::first($patient_day  , "%");
    $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$patient_naissance'";
  }
	
  if ($patient_ville) $where["ville"] = $whereSoundex["ville"] = "LIKE '$patient_ville%'";
  if ($patient_cp)    $where["cp"]    = $whereSoundex["cp"]    = "LIKE '$patient_cp%'";
	
	$patients        = array();
	$patientsSoundex = array();

	$pat = new CPatient();
	if ($where) {
	  $patients = $pat->loadList($where, "nom, prenom, naissance", "0, 100");
	}
	if($whereSoundex && ($nbExact = (100 - count($patients)))) {
	  $patientsSoundex = $pat->loadList($whereSoundex, "nom, prenom, naissance", "0, $nbExact");
	  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
	}
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));
$smarty->assign("patient_ipp"    , $patient_ipp                );
$smarty->assign("board"          , $board                      );

$smarty->assign("nom"            , $patient_nom                );
$smarty->assign("prenom"         , $patient_prenom             );
$smarty->assign("ville"          , $patient_ville              );
$smarty->assign("cp"             , $patient_cp                 );
$smarty->assign("naissance"      , $patient_naissance          );
$smarty->assign("nom_search"     , $patient_nom_search         );
$smarty->assign("prenom_search"  , $patient_prenom_search      );

$smarty->assign("useVitale"      , $useVitale                  );
$smarty->assign("patVitale"      , $patVitale                  );
$smarty->assign("patients"       , $patients                   );
$smarty->assign("patientsCount"  , count($patients)            );
$smarty->assign("patientsSoundexCount", count($patientsSoundex));
$smarty->assign("patientsSoundex", $patientsSoundex            );
$smarty->assign("patient"        , $patient                    );

$smarty->display("inc_list_patient.tpl");