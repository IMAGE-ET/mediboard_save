<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkAdmin();

// Filtre sur les enregistrements
$sejour = new CSejour();
$action = CValue::get("action", "start");

// Tous les d�parts possibles
$idMins = array(
  "start"    => "000000",
  "continue" => CValue::getOrSession("idContinue"),
  "retry"    => CValue::getOrSession("idRetry"),
);

$idMin = CValue::first(@$idMins[$action], "000000");
CValue::setSession("idRetry", $idMin);

// Requ�tes
$where = array();
$where[$sejour->_spec->key] = "> '$idMin'";
$where['annule'] = " = '0'";

$sip_config = CAppUI::conf("sip");

// Bornes
if (preg_match("/(\d{4})-(\d{2})-(\d{2})/", $sip_config["repair_date_min"])) {
  $where['entree'] = " >= '".$sip_config["repair_date_min"]."'";
}

if (preg_match("/(\d{4})-(\d{2})-(\d{2})/", $sip_config["repair_date_max"])) {
  $where['sortie'] = " <= '".$sip_config["repair_date_max"]."'";
}

$ljoin = array();
$ljoin["id_sante400"] = "sejour.sejour_id = id_sante400.object_id AND id_sante400.object_class = 'CSejour'";
$where["id_sante400.id_sante400_id"] = "IS NULL";

// Comptage
$count = $sejour->countList($where, null, null, null, $ljoin);
$max = $sip_config["repair_segment"];
$max = min($max, $count);
CAppUI::stepAjax("Export de $max sur $count objets de type 'CSejour' � partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 120);
CAppUI::stepAjax("Limite de temps du script positionn� � '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

$errors = 0;
// Export r�el
if (!$sip_config["verify_repair"]) {
  $sejours = $sejour->loadList($where, $sejour->_spec->key, "0, $max");

	if (!CAppUI::conf("dPplanningOp CSejour tag_dossier") || !CAppUI::conf("dPpatients CPatient tag_ipp")) {
	  CAppUI::stepAjax("Aucun tag (patient/s�jour) de d�fini pour la synchronisation.", UI_MSG_ERROR);
	  return;
	}
	
	$echange = 0;
	foreach ($sejours as $sejour) {
	  $sejour->loadRefPraticien();
	  $sejour->loadRefPatient();
	  $sejour->_ref_patient->loadIPP();
	  if ($sejour->_ref_prescripteurs) {
	    $sejour->loadRefsPrescripteurs();
	  }
	  $sejour->loadRefAdresseParPraticien();
	  $sejour->_ref_patient->loadRefsFwd();
	  $sejour->loadRefsActes();
	  foreach ($sejour->_ref_actes_ccam as $actes_ccam) {
	    $actes_ccam->loadRefPraticien();
	  }
	  $sejour->loadRefsAffectations();
	  $sejour->loadNumDossier();
	  $sejour->loadLogs();
	  $sejour->loadRefsConsultations();
	  $sejour->loadRefsConsultAnesth();
	      
	  $sejour->_ref_last_log->type = "create";
	  $dest_hprim = new CDestinataireHprim();
	  $dest_hprim->message = "patients";
	  $dest_hprim->loadMatchingObject();
	
	  if (!$sejour->_num_dossier) {
	    $num_dossier = new CIdSante400();
	    //Param�trage de l'id 400
	    $num_dossier->object_class = "CSejour";
	    $num_dossier->object_id = $num_dossier->_id;
	    $num_dossier->tag = $dest_hprim->_tag_sejour;
	    $num_dossier->loadMatchingObject();
	
	    $sejour->_num_dossier = $num_dossier->id400;
	  }
	
	  if (CAppUI::conf("sip send_sej_pa") && ($sejour->_etat != "preadmission")) {
	    continue;
	  }
	
	  if (CAppUI::conf("sip sej_no_numdos") && $sejour->_num_dossier && ($sejour->_num_dossier != "-")) {
	    continue;
	  }
	  
	  $domEvenement = new CHPrimXMLVenuePatient();
	  $domEvenement->emetteur     = CAppUI::conf('mb_id');
	  $domEvenement->destinataire = $dest_hprim->nom;
	  $domEvenement->group_id     = $dest_hprim->group_id;
	  
	  $messageEvtPatient = $domEvenement->generateTypeEvenement($sejour);
	  $doc_valid = $domEvenement->schemaValidate();
	  
	  if (!$doc_valid) {
	    $errors++;
	    trigger_error("Cr�ation de l'�v�nement s�jour impossible.", E_USER_WARNING);
	    CAppUI::stepAjax("Import de '$sejour->_view' �chou�", UI_MSG_WARNING);
	  }
	  
	  if ($sejour->_ref_patient->code_regime) {
	    $domEvenement = new CHPrimXMLDebiteursVenue();
	    $domEvenement->emetteur     = CAppUI::conf('mb_id');
	    $domEvenement->destinataire = $dest_hprim->nom;
	    $domEvenement->group_id     = $dest_hprim->group_id;
	    
	    $messageEvtPatient = $domEvenement->generateTypeEvenement($sejour);
	    $doc_valid = $domEvenement->schemaValidate();
	    
	    if (!$doc_valid) {
	      $errors++;
	      trigger_error("Cr�ation de l'�v�nement debiteurs impossible.", E_USER_WARNING);
	      CAppUI::stepAjax("Import de '$sejour->_view' �chou�", UI_MSG_WARNING);
	    }
	  }
	  $echange++;
	}
	// Enregistrement du dernier identifiant dans la session
	if (@$sejour->_id) {
	  CValue::setSession("idContinue", $sejour->_id);
	  CAppUI::stepAjax("Dernier ID trait� : '$sejour->_id'", UI_MSG_OK);
	  CAppUI::stepAjax("$echange de cr��s", UI_MSG_OK);
	}
	
	CAppUI::stepAjax("R�paration termin� avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);
} else {
	
}


?>