<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireModuleClass("dPccam", "codable");

class CConsultation extends CCodable {
  const PLANIFIE       = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS       = 48;
  const TERMINE        = 64;
 
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;
  var $sejour_id       = null;
  
  // DB fields
  var $heure           = null;
  var $duree           = null;
  var $secteur1        = null;
  var $secteur2        = null;
  var $chrono          = null;
  var $annule          = null;
  
  var $patient_date_reglement = null;
  var $tiers_date_reglement   = null;
  
  var $motif            = null;
  var $rques            = null;
  var $examen           = null;
  var $histoire_maladie = null;
  var $conclusion       = null;
  
  var $traitement          = null;
  var $premiere            = null;
  var $adresse             = null; // Le patient a-t'il �t� adress� ?
  var $adresse_par_prat_id = null;
  var $tarif               = null;
  
  var $arrivee         = null;
  var $categorie_id    = null;
  var $valide          = null; // Cotation valid�e
  var $si_desistement  = null;
 
  var $total_assure    = null;
  var $total_amc       = null; 
  var $total_amo       = null;

  var $du_patient       = null; // somme que le patient doit r�gler
  var $du_tiers         = null;
  var $accident_travail = null;
  var $concerne_ALD     = null;
	
  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null;
  var $_check_adresse  = null;
  var $_somme          = null;
  var $_types_examen   = null;
  var $_precode_acte   = null;
  var $_exam_fields    = null;
  var $_type           = null;  // Type de la consultation
  
  // Fwd References
  var $_ref_patient      = null; // Declared in CCodable
  var $_ref_sejour       = null; // Declared in CCodable
  var $_ref_plageconsult = null;
  var $_ref_adresse_par_prat = null;
  
  // FSE
  var $_bind_fse       = null;
  var $_ids_fse        = null;
  var $_ext_fses       = null;
  var $_current_fse    = null;
  var $_fse_intermax   = null;

  // Tarif
  var $_bind_tarif     = null;
  var $_tarif_id       = null;
  
  // Back References
  var $_ref_consult_anesth     = null;
  var $_ref_examaudio          = null;
  var $_ref_examcomp           = null;
  var $_ref_examnyha           = null;
  var $_ref_exampossum         = null;
  var $_ref_examigs            = null;
  var $_count_fiches_examen    = null;
  var $_ref_reglements         = null;
  var $_ref_reglements_patient = null;
  var $_ref_reglements_tiers   = null;

  var $_ref_prescription = null; 
  var $_ref_categorie    = null;
  
  // Distant fields
  var $_ref_chir                 = null;
  var $_date                     = null;
  var $_datetime                 = null;
  var $_is_anesth                = null; 
  var $_du_patient_restant       = null;
  var $_reglements_total_patient = null;
  var $_du_tiers_restant         = null;
  var $_reglements_total_tiers   = null;
  var $_forfait_se               = null;
  var $_facturable               = null;
  
  // Filter Fields
  var $_date_min	 	           = null;
  var $_date_max 		           = null;
  var $_prat_id 		           = null;
  var $_etat_reglement_patient = null;
  var $_etat_reglement_tiers   = null;
  var $_type_affichage         = null;
  var $_coordonnees            = null;
  var $_plages_vides           = null;
  var $_empty_places           = null;
  var $_non_pourvues           = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation';
    $spec->key   = 'consultation_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consult_anesth"] = "CConsultAnesth consultation_id";
    $backProps["examaudio"]      = "CExamAudio consultation_id";
    $backProps["examcomp"]       = "CExamComp consultation_id";
    $backProps["examnyha"]       = "CExamNyha consultation_id";
    $backProps["exampossum"]     = "CExamPossum consultation_id";
    $backProps["examigs"]        = "CExamIgs consultation_id";
    $backProps["prescriptions"]  = "CPrescription object_id";
    $backProps["reglements"]     = "CReglement consultation_id";
    
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["sejour_id"]         = "ref class|CSejour";
    $specs["plageconsult_id"]   = "ref notNull class|CPlageconsult seekable show|0";
    $specs["patient_id"]        = "ref class|CPatient purgeable seekable show|1";
    $specs["categorie_id"]      = "ref class|CConsultationCategorie show|0";
		$specs["_praticien_id"]     ="ref class|CMediusers seekable show|1"; //is put here for view
    
    $specs["motif"]             = "text helped seekable";
    $specs["heure"]             = "time notNull show|0";
    $specs["duree"]             = "numchar maxLength|1 show|0";
    $specs["secteur1"]          = "currency min|0 show|0";
    $specs["secteur2"]          = "currency show|0";
    $specs["chrono"]            = "enum notNull list|16|32|48|64 show|0";
    $specs["annule"]            = "bool show|0";
    $specs["_etat"]             = "str";
    
    $specs["rques"]             = "text helped seekable";
    $specs["examen"]            = "text helped seekable show|0";
    $specs["traitement"]        = "text helped seekable";
    $specs["histoire_maladie"]  = "text helped seekable";
    $specs["conclusion"]        = "text helped seekable";
    
    $specs["facture"]           = "bool default|0 show|0";
    
    $specs["premiere"]            = "bool show|0";
    $specs["adresse"]             = "bool show|0";
    $specs["adresse_par_prat_id"] = "ref class|CMedecin";
    $specs["tarif"]               = "str show|0";
    $specs["arrivee"]             = "dateTime show|0";
    $specs["concerne_ALD"]        = "bool";
		
    $specs["patient_date_reglement"]    = "date show|0";
    $specs["tiers_date_reglement"]      = "date show|0";
    $specs["du_patient"]                = "currency show|0";
    $specs["du_tiers"  ]                = "currency show|0";
		
    $specs["accident_travail"]  = "date";

    $specs["total_amo"]         = "currency show|0";
    $specs["total_amc"]         = "currency show|0";
    $specs["total_assure"]      = "currency show|0";

    $specs["valide"]            = "bool show|0";
    $specs["si_desistement"]    = "bool notNull default|0";

    $specs["_du_patient_restant"]       = "currency";
    $specs["_du_tiers_restant"]         = "currency";
    $specs["_reglements_total_patient"] = "currency";
    $specs["_reglements_total_tiers"  ] = "currency";
    $specs["_etat_reglement_patient"]   = "enum list|reglee|non_reglee";
    $specs["_etat_reglement_tiers"  ]   = "enum list|reglee|non_reglee";
    $specs["_forfait_se"]               = "bool default|0";
    $specs["_facturable"]               = "bool default|1";
    
    $specs["_date"]             = "date";
    $specs["_datetime"]         = "dateTime show|1";
    $specs["_date_min"]         = "date";
    $specs["_date_max"] 	      = "date moreEquals|_date_min";
    $specs["_type_affichage"]   = "enum list|complete|totaux";
    $specs["_coordonnees"]      = "bool default|0";
    $specs["_plages_vides"]     = "bool default|1";
    $specs["_non_pourvues"]     = "bool default|1";
    
    $specs["_check_premiere"]   = "";
    $specs["_check_adresse"]    = "";
    $specs["_somme"]            = "currency";		
    $specs["_type"]             = "enum list|urg|anesth";
    $specs["_prat_id"]          = "";
    
    return $specs;
  }
  
  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = mbTransformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
    if($this->chrono)
      $this->_etat = $etat[$this->chrono];
    if ($this->annule) {
      $this->_etat = "Ann.";
    }
  }
  
  function getTemplateClasses(){
    $this->loadRefsFwd();
    
    $tab = array();
    
    // Stockage des objects li�s � l'op�ration
    $tab['CConsultation'] = $this->_id;
    $tab['CPatient'] = $this->_ref_patient->_id;
    
    $tab['CConsultAnesth'] = 0;
    $tab['COperation'] = 0;
    $tab['CSejour'] = 0;
    
    return $tab;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  	$this->_somme = $this->secteur1 + $this->secteur2;
    if($this->patient_date_reglement === "0000-00-00") {
      $this->patient_date_reglement = null;
    }
    $this->du_patient = round($this->du_patient, 2);
    $this->du_tiers   = round($this->du_tiers  , 2);
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_premiere = $this->premiere;
    $this->_check_adresse = $this->adresse;
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;
    // pour r�cuperer le praticien depuis la plage consult
    $this->loadRefPlageConsult(true);
    // si _coded vaut 1 alors, impossible de modifier la cotation
    $this->_coded = $this->valide;
    
    $this->_exam_fields = self::getExamFields();
  }
   
  function updateDBFields() {
    if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    
    // Nom de tarif manuel
    if ($this->tarif === "manuel") {
			// Get all acts
      $this->loadRefsActes();
      foreach ($this->_ref_actes as $acte) {
        $this->tarif.= " $acte->_shortview";
      }
    }
    
    // Liaison FSE prioritaire sur l'�tat
    if ($this->_bind_fse) {
      $this->valide = 0;
    }
    
    // Cas du paiement d'un s�jour
  	if ($this->sejour_id !== null && $this->sejour_id && $this->secteur1 !== null && $this->secteur2 !== null){
  		$this->du_tiers = $this->secteur1 + $this->secteur2;
  		$this->du_patient = 0;
  	}
  }

  function check() {
    // Data checking
    $msg = null;
    if(!$this->_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      return $msg . parent::check();
    }
    
    $this->loadOldObject();
    $this->loadRefsReglements();
    
    // D�validation avec r�glement d�j� effectu�
    if ($this->fieldModified("valide", "0")) {
      if (count($this->_ref_reglements)){
        $msg .= "Vous ne pouvez plus d�valider le tarif, des r�glements ont d�j� �t� effectu�s";
      }
    }
    
    /*
    if ($this->_old->valide === "0") {
      // R�glement sans validation
      if ($this->fieldModified("tiers_date_reglement") 
       || ($this->fieldModified("patient_date_reglement") && $this->patient_date_reglement !== "")) {
        $msg .= "Vous ne pouvez pas effectuer le r�glement si le tarif n'a pas �t� valid�";
      }
    }
    */
    if ($this->_old->valide === "1" && $this->valide === "1") {
      // Modification du tarif d�j� valid�
      if ($this->fieldModified("secteur1") 
       || $this->fieldModified("secteur2")
       || $this->fieldModified("total_assure") 
       || $this->fieldModified("total_amc") 
       || $this->fieldModified("total_amo") 
       || $this->fieldModified("du_patient") 
       || $this->fieldModified("du_tiers")) {
        //$msg .= $this->du_patient." vs. ".$this->_old->du_patient." (".$this->fieldModified("du_patient").")";
        $msg .= "Vous ne pouvez plus modifier le tarif, il est d�j� valid�";
      }
    }
    
    return $msg . parent::check();
  }
  
  function loadView() {
  	parent::loadView();
    $this->loadRefsFichesExamen(); 
    $this->loadRefsActesNGAP();
  }

  /**
   * Chargement des identifiants des FSE associ�es
   * @return void
   */
  function loadIdsFSE() {
    $id_fse = new CIdSante400();
    $id_fse->setObject($this);
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse = $id_fse->loadMatchingList();
    $this->_ids_fse = CMbArray::pluck($id_fse, "id400");
    
    // Chargement des FSE externes
    $fse = @new CLmFSE();
    if(!isset($fse->_spec->ds)){
      return;
    }
    $where = array();
    $where["S_FSE_NUMERO_FSE"] = CSQLDataSource::prepareIn($this->_ids_fse);
    $this->_ext_fses = $fse->loadList($where);
    
    // Last FSE
    $this->_current_fse = null;
    foreach ($this->_ext_fses as $_ext_fse) {
      if (!$_ext_fse->_annulee) {
        $this->_current_fse = $_ext_fse;
      }
    }
  }
  
  /**
   * deleteActes() Redefinition
   * @return string Store-like message
   */  
  function deleteActes() {
    if ($msg = parent::deleteActes()) {
      return $msg;
    }

    $this->secteur1 = "";
    $this->secteur2 = "";
//    $this->valide = 0; // Ne devrait pas �tre n�cessaire
    $this->total_assure = 0.0;
    $this->total_amc = 0.0;
    $this->total_amo = 0.0;
    $this->du_patient = 0.0;
    $this->du_tiers = 0.0;
    
    if ($msg = $this->store()) {
      return $msg;
    }
  }  
 
  function bindTarif(){
    $this->_bind_tarif = false;
    
    // Chargement du tarif
    $tarif = new CTarif();
    $tarif->load($this->_tarif_id);
 
    // Cas de la cotation poursuivie
		if ($this->tarif == "pursue") {
	    $this->secteur1 += $tarif->secteur1;
	    $this->secteur2 += $tarif->secteur2;
	    $this->tarif     = "composite";
	 	}
    // Cas de la cotation normale
		else {
	    $this->secteur1 = $tarif->secteur1;
	    $this->secteur2 = $tarif->secteur2;
      $this->tarif    = $tarif->description;
 		}

    $this->du_patient   = $this->secteur1 + $this->secteur2;
    
    // Mise � jour de codes CCAM pr�vus, sans information serialis�e compl�mentaire
    foreach($tarif->_codes_ccam as $_code_ccam) {
      $this->_codes_ccam[] = substr($_code_ccam, 0, 7);
    }
    $this->codes_ccam = implode("|", $this->_codes_ccam);
    if ($msg = $this->store()) {
      return $msg;
    }

    // Precodage des actes NGAP avec information s�rialis�e compl�te
    
    $this->_tokens_ngap = $tarif->codes_ngap;
    if ($msg = $this->precodeNGAP()){
      return $msg;
    }  

    $this->codes_ccam = $tarif->codes_ccam;
    // Precodage des actes CCAM avec information s�rialis�e compl�te
    if ($msg = $this->precodeCCAM()){
      return $msg;
    }  
  }
    
  /**
   * Create a LogicMaxFSE from the consult
   * Conterpart to Bind FSE
   * @return void
   */
  function makeFSE() {
    $this->_fse_intermax = array();

    // Ajout des actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acte_ngap) {
	    $acteNumber = count($this->_fse_intermax)+1;
	    $this->_fse_intermax["ACTE_$acteNumber"] = array(
        "PRE_ACTE_TYPE"   => 0,
	      "PRE_DEPASSEMENT" => $acte_ngap->montant_depassement,
	      "PRE_CODE"        => $acte_ngap->code,
	      "PRE_COEFFICIENT" => $acte_ngap->demi ? $acte_ngap->coefficient/2 : $acte_ngap->coefficient,
	      "PRE_QUANTITE"    => $acte_ngap->quantite,
	      "PRE_DEMI"        => $acte_ngap->demi,
	    );
    }
    
    // Ajout des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acte_ccam) {
	    $acteNumber = count($this->_fse_intermax)+1;
	    $ACTE = array(
        "PRE_ACTE_TYPE"     => 1,
	      "PRE_DEPASSEMENT"   => $acte_ccam->montant_depassement,
	      "PRE_CODE_CCAM"     => $acte_ccam->code_acte,
	      "PRE_CODE_ACTIVITE" => $acte_ccam->code_activite,
	      "PRE_CODE_PHASE"    => $acte_ccam->code_phase,
	      "PRE_ASSOCIATION"   => $acte_ccam->code_association,
	      "PRE_RMB_EXCEP"     => $acte_ccam->_rembex ? "O" : "N",
	      );
	    
	    // Ajout des modificateurs
	    for ($i = 1; $i <= 4; $i++) {
	      $ACTE["PRE_MODIF_$i"] = @$acte_ccam->_modificateurs[$i-1];
	    }

	    $this->_fse_intermax["ACTE_$acteNumber"] = $ACTE;
    }

    // Section FSE
    $this->_fse_intermax["FSE"] = array();

    if ($this->accident_travail) {
		  $this->_fse_intermax["FSE"]["FSE_NATURE_ASSURANCE"] = "AT";
		  $this->_fse_intermax["FSE"]["FSE_DATE_AT"] = mbDateToLocale($this->accident_travail);
    }
    
    if ($this->concerne_ALD) {
      $this->_fse_intermax["FSE"]["FSE_ALD"] = "1";      
    }
    
    if (!count($this->_fse_intermax["FSE"])) {
      unset($this->_fse_intermax["FSE"]);
    }
  }
  
  /**
   * Bind a FSE to current consult
   * @return string Store-like message
   */
  function bindFSE() {
    // Prevents recursion
    $this->_bind_fse = false;
    
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }
    
    // Make id externe
    $fse = $intermax["FSE"];
    $fseNumero = $fse["FSE_NUMERO_FSE"];
    $id_fse = new CIdSante400();
    $id_fse->object_class = $this->_class_name;
    $id_fse->id400 = $fseNumero;
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse->loadMatchingObject();
    
    // Autre association ?
    if ($id_fse->object_id && $id_fse->object_id != $this->_id) {
      $id_fse->loadTargetObject();
      $consOther =& $id_fse->_ref_object;
      $consOther->loadRefsFwd();
      return sprintf ("FSE d�j� associ�e � la consultation du patient %s - %s le %s",
        $consOther->_ref_patient->_view,
        $consOther->_ref_chir->_view,
        mbDateToLocale($consOther->_date));
    }
    
    $id_fse->object_id = $this->_id;
    $id_fse->last_update = mbDateTime();
    
    if ($msg = $id_fse->store()) {
      return $msg;
    }
    
    // Ajout des actes CCAM et NGAP r�cup�r�s
    for ($iActe = 1; $fseActe = @$intermax["ACTE_$iActe"]; $iActe++) {
      switch ($typeActe = $fseActe["PRE_ACTE_TYPE"]) {
        case "0": 
        $acte = new CActeNGAP();
        $acte->setObject($this);
        $acte->executant_id  = $this->getExecutantId();
        $acte->code        = $fseActe["PRE_CODE"];
        $acte->quantite    = $fseActe["PRE_QUANTITE"];
        $acte->coefficient = $fseActe["PRE_COEFFICIENT"];
        $acte->demi        = $fseActe["PRE_DEMI"];        
        $acte->getLibelle();
        
        // Coefficient facial doubl�
        if ($acte->demi) {
          $acte->coefficient *= 2;
        }
        
        break;
        
        case "1": 
        $acte = new CActeCCAM();
        $acte->setObject($this);
        $acte->_adapt_object = true;
        $acte->executant_id  = $this->getExecutantId($acte->code_acte);
        $acte->code_acte     = $fseActe["PRE_CODE_CCAM"];
        $acte->code_activite = $fseActe["PRE_CODE_ACTIVITE"];
        $acte->code_phase    = $fseActe["PRE_CODE_PHASE"];
        $acte->execution     = $this->_acte_execution;
        if ($fseActe["PRE_RMB_EXCEP"]) {
          $acte->rembourse     = "1";
        }
        $acte->modificateurs = null;
        
        for ($iModif = 1; $iModif <= 4; $iModif++) {
          $acte->modificateurs .= $fseActe["PRE_MODIF_$iModif"];
        }
        
        $acte->code_association = $fseActe["PRE_ASSOCIATION"];
        
        break;
        
        default: 
        return "Acte LogicMax de type inconnu (Numero = '$typeActe')";
      }
      
      $acte->montant_base = $fseActe["PRE_BASE"];
      $acte->montant_depassement = $fseActe["PRE_MONTANT"] - $fseActe["PRE_BASE"];

      if ($msg = $acte->store()) {
        return $msg;
      }
    }
    
    // Nom par d�faut si non d�fini
    $consult = new CConsultation();
    $consult->load($this->_id);
    
    // Sauvegarde des tarifs de la consultation
    $consult->total_assure = $fse["FSE_TOTAL_ASSURE"];
    $consult->total_amo    = $fse["FSE_TOTAL_AMO"];
    $consult->total_amc    = $fse["FSE_TOTAL_AMC"];
    $consult->accident_travail = mbDateFromLocale($fse["FSE_DATE_AT"]);
    
    $consult->du_patient = $consult->total_assure;
    $consult->du_tiers   = $consult->total_amo + $consult->total_amc;
    
    if (!in_array($fse["FSE_TIERS_PAYANT"], array("2", "3"))) {
      $consult->du_patient += $consult->total_amo;
      $consult->du_tiers   -= $consult->total_amo;
    }
    
    if (!in_array($fse["FSE_TIERS_PAYANT"], array("3", "4"))) {
      $consult->du_patient += $consult->total_amc;
      $consult->du_tiers   -= $consult->total_amc;
    }
    
    $consult->valide = '1';
    if (!$consult->tarif) {
      $consult->tarif = "FSE LogicMax";
    }
    
    return $consult->store();
  }

  function precodeCCAM() {
    $this->loadRefPlageConsult();
    // Explode des codes_ccam du tarif
    $listCodesCCAM = explode("|", $this->codes_ccam);
    foreach($listCodesCCAM as $key => $code){
      $acte = new CActeCCAM();
      $acte->_adapt_object = true;
        
      $acte->_preserve_montant = true;
      $acte->setFullCode($code);
      
      // si le code ccam est compos� de 3 elements, on le precode
      if($acte->code_activite != "" && $acte->code_phase != ""){
      	// Permet de sauvegarder le montant de base de l'acte CCAM
      	$acte->_calcul_montant_base = 1;
      	
        // Mise a jour de codes_ccam suivant les _tokens_ccam du tarif
        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class_name;
        $acte->executant_id = $this->_ref_chir->_id;
        $acte->execution = mbDateTime();
        if($msg = $acte->store()){
          return $msg;
        }
      }
    }
  }
  
  function precodeNGAP() {
    $listCodesNGAP = explode("|",$this->_tokens_ngap);
    foreach($listCodesNGAP as $key => $code_ngap){
      if($code_ngap) {
	      $acte = new CActeNGAP();
	      $acte->_preserve_montant = true;
        $acte->setFullCode($code_ngap);

        $acte->object_id = $this->_id;
	      $acte->object_class = $this->_class_name;
        $acte->executant_id = $this->getExecutantId();
	      if (!$acte->countMatchingList()) {
	        if ($msg = $acte->store()) {
	          return $msg;
	        }
	      }
	    }
    } 
  }
  
  function doUpdateMontants(){
    // Initialisation des montants
    $secteur1_NGAP = 0;
    $secteur1_CCAM = 0;
    $secteur2_NGAP = 0;
    $secteur2_CCAM = 0;
    
    // Chargement des actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acteNGAP) { 
      $secteur1_NGAP += $acteNGAP->montant_base;
      $secteur2_NGAP += $acteNGAP->montant_depassement;
    }
   
    // Chargement des actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acteCCAM) { 
      $secteur1_CCAM += $acteCCAM->montant_base;
      $secteur2_CCAM += $acteCCAM->montant_depassement;
    }
    
    // Remplissage des montant de la consultation
    $this->secteur1 = $secteur1_NGAP + $secteur1_CCAM;
    $this->secteur2 = $secteur2_NGAP + $secteur2_CCAM;
    
    return $this->store();
    
  }
  
  /**
   * @todo: refactoring complet de la fonction store de la consultation

ANALYSE DU CODE
1. gestion du d�sistement
2. premier if : creation d'une consultation � laquelle on doit attacher un s�jour (conf active) : comportement DEPART / ARRIVEE
3. mise en cache du forfait FSE et facturable : uniquement dans le cas d'un s�jour
4. on load le s�jour de la consultation
5. on initialise le _adjust_sejour � false
6. dans le cas ou on a un s�jour
  6.1. s'il est de type consultation, on ajuste le s�jour en fonction du comportement DEPART / ARRIVEE
  6.2. si la plage de consultation a �t� modifi�e, adjust_sejour passe � true et on ajuste le s�jour en fonction du comportement DEPART / ARRIVEE (en passant par l'adjustSejour() )
  6.3. si on a un id (� virer) et que le chrono est modifi� en PATIENT_ARRIVE, si on g�re les admissions auto (conf) on met une entr�e r�elle au s�jour
7. Si le patient est modifi�, qu'on est pas en train de merger et qu'on a un s�jour, on empeche le store
8. On appelle le parent::store()
9. On passe le forfait SE et facturable au s�jour
10. On propage la modification du patient de la consultation au s�jour
11. si on a ajust� le s�jour et qu'on est dans un s�jour de type conclut et que le s�jour n'a plus de consultations, on essaie de le supprimer, sinon on l'annule
12. Gestion du tarif et pr�codage des actes (bindTarif)
13. Bind FSE

ACTIONS : 
- Faire une fonction comportement_DEPART_ARRIVEE()
- Merger le 2, le 6.1 et le 6.2 (et le passer en 2 si possible)
- Faire une fonction pour le 6.3, le 7, le 10, le 11
- Am�liorer les fonctions 12 et 13 en incluant le test du behaviour fields

COMPORTEMENT DEPART ARRIVEE
modif de la date d'une consultation ayant un s�jour sur le mod�le DEPART / ARRIVEE:
1. Pour le DEPART :
-> on d�croche la consultation de son ancien s�jour
-> on ne touche pas � l'ancien s�jour si :
- il est de type autre que consultation
- il a une entr�e r�elle
- il a d'autres consultations
-> sinon on l'annule

2. Pour l'ARRIVEE
-> si on a un s�jour qui englobe : on la colle dedans
-> sinon on cr�e un s�jour de consultation

TESTS A EFFECTUER
0. Cr�ation d'un pause
0.1. D�placement d'une pause
1. Cr�ation d'une consultation simple C1 (S�jour S1)
2. Cr�ation d'une deuxi�me consultation le m�me jour / m�me patient C2 (S�jour S1)
3. Cr�ation d'une troisi�me consultation le m�me jour / m�me patient C3 (S�jour S1)
4. D�placement de la consultation C1 un autre jour (S�jour S2)
5. Changement du nom du patient C2 (pas de modification car une autre consultation)
6. D�placement de C3 au m�me jour (Toujours s�jour S1)
7. Annulation de C1 (Suppression ou annulation de S1)
8. D�placement de C2 et C3 � un autre jour (s�jour S3 cr��, s�jour S1 supprim� ou annul�)
9. Arriv�e du patient pour C2 (S3 a une entr�e r�elle)
10. D�placement de C3 dans un autre jour (S4)
11. D�placement de C2 dans un autre jour (S5 et S3 reste tel quel)
   */
  function store() {
    $this->completeField('sejour_id', 'heure', 'plageconsult_id', 'si_desistement');
    
    if ($this->si_desistement === null) {
      $this->si_desistement = 0;
    }

    // Consultation dans un s�jour
    if (!$this->_id && !$this->sejour_id && 
        CAppUI::conf("dPcabinet CConsultation attach_consult_sejour") && $this->patient_id) {
      // Recherche s�jour englobant
      $facturable = $this->_facturable;
      if ($facturable === null) {
        $facturable = 1;  
      }
      $this->loadRefPlageConsult();
      $datetime = $this->_datetime;
      $minutes_before_consult_sejour = CAppUI::conf("dPcabinet CConsultation minutes_before_consult_sejour");
      $where = array();
      $where['annule']     = " = '0'";
      $where['patient_id'] = " = '$this->patient_id'";
      $where['group_id']   = " = '".CGroups::loadCurrent()->_id."'";
      $where['facturable']     = " = '$facturable'";
      $datetime_before     = mbDateTime("+$minutes_before_consult_sejour minute", "$this->_date $this->heure");
      $where[] = "`sejour`.`entree` <= '$datetime_before' AND `sejour`.`sortie` >= '$datetime'";

      $sejour = new CSejour();
      $sejour->loadObject($where);

      // Si pas de s�jour et config alors le cr�er en type consultation
      if (!$sejour->_id && CAppUI::conf("dPcabinet CConsultation create_consult_sejour")) {
        $sejour->patient_id = $this->patient_id;
        $sejour->praticien_id = $this->_ref_chir->_id;
        $sejour->group_id = CGroups::loadCurrent()->_id;
        $sejour->type = "consult";
        $sejour->facturable = $facturable;
        $datetime = ($this->_date && $this->heure) ? "$this->_date $this->heure" : NULL;
        if ($this->chrono == self::PLANIFIE) {
          $sejour->entree_prevue = $datetime;
        } else {
          $sejour->entree_reelle = $datetime;
        }
        $sejour->sortie_prevue = "$this->_date 23:59:59";
        if ($msg = $sejour->store()) {
          return $msg;
        }  
      }
      $this->sejour_id = $sejour->_id;
    }
    
    // must be BEFORE loadRefSejour()
    $facturable = $this->_facturable;
    $forfait_se = $this->_forfait_se;
    
    $this->loadRefSejour();
    $this->_adjust_sejour = false;
    
    if ($this->sejour_id) {
      $this->loadRefPlageConsult();

      // Si le s�jour est de type consult
      if ($this->_ref_sejour->type == 'consult') {
      	$this->_ref_sejour->loadRefsConsultations();
      	$nb_consults_dans_sejour = count($this->_ref_sejour->_ref_consultations);
      	$this->_ref_sejour->_hour_entree_prevue = null;
        $this->_ref_sejour->_min_entree_prevue  = null;
        $this->_ref_sejour->_hour_sortie_prevue = null;
        $this->_ref_sejour->_min_sortie_prevue  = null;
        
        $date_consult = mbDate($this->_datetime);
        
        // On d�place l'entr�e et la sortie du s�jour
        $entree = $this->_datetime;
        $sortie = $date_consult . " 23:59:59";

        // Si on a une entr�e r�elle et que la date de la consultation est avant l'entr�e r�elle, on sort du store
        if($this->_ref_sejour->entree_reelle && $date_consult < mbDate($this->_ref_sejour->entree_reelle)) {
          return CAppUI::tr("CConsultation-denyDayChange");
        }
        
        // Si on a une sortie r�elle et que la date de la consultation est apr�s la sortie r�elle, on sort du store
        if($this->_ref_sejour->sortie_reelle && $date_consult > mbDate($this->_ref_sejour->sortie_reelle)) {
      	  return CAppUI::tr("CConsultation-denyDayChange-exit");
        }

        // S'il y a d'autres consultations dans le s�jour, on �tire l'entr�e et la sortie
        // en parcourant la liste des consultations
        foreach ($this->_ref_sejour->_ref_consultations as $_consultation) {
      		if ($_consultation->_id != $this->_id) {
      		  $_consultation->loadRefPlageConsult();
      		  
        		if ($_consultation->_datetime < $entree)
        		  $entree = $_consultation->_datetime;
        		  
        		if ($_consultation->_datetime > $sortie)
               $sortie = mbDate($_consultation->_datetime) . " 23:59:59";
          }
      	}

        $this->_ref_sejour->entree_prevue = $entree;
	      $this->_ref_sejour->sortie_prevue = $sortie;
        $this->_ref_sejour->updateFormFields();
        $this->_ref_sejour->_check_bounds = 0;
        $this->_ref_sejour->store();
      }

      // Changement de journ�e pour la consult 
      if ($this->fieldModified("plageconsult_id")) {
        $this->_adjust_sejour = true;
        
        // Pas le permettre si admission est d�j� faite
        $max_hours = CAppUI::conf("dPcabinet CConsultation hours_after_changing_prat");
        if ($this->_ref_sejour->entree_reelle &&
           (mbDateTime("+ $max_hours HOUR", $this->_ref_sejour->entree_reelle) < mbDateTime())) {
          return CAppUI::tr("CConsultation-denyDayChange");
        }
        
        $this->loadRefPlageConsult();
        $dateTimePlage = $this->_datetime;
        $where = array();
        $where['patient_id']   = " = '$this->patient_id'";
        $where[] = "`sejour`.`entree` <= '$dateTimePlage' AND `sejour`.`sortie` >= '$dateTimePlage'";
        
        $sejour = new CSejour();
        $sejour->loadObject($where);
        
        $this->adjustSejour($sejour, $dateTimePlage);
      }
      
      if ($this->_id && $this->fieldModified("chrono", self::PATIENT_ARRIVE)) {
        $this->completeField("plageconsult_id");
        $this->loadRefPlageConsult();
        $this->_ref_chir->loadRefFunction();
        $function = $this->_ref_chir->_ref_function;
        if ($function->admission_auto) {
          $sejour = new CSejour();
          $sejour->load($this->sejour_id);
          $sejour->entree_reelle = $this->arrivee;
          if ($msg = $sejour->store()) {
            return $msg;
          }
        }
      }
    }
    
    $patient_modified = $this->fieldModified("patient_id");
        
    // Si le patient est modifi� et qu'il y a plus d'une consult dans le sejour, on empeche le store
    if (!$this->_forwardRefMerging && $this->sejour_id && $patient_modified) {
      $this->loadRefSejour();
      
      $consultations = $this->_ref_sejour->countBackRefs("consultations");
      if ($consultations > 1) {
        return "D'autres consultations sont pr�vues dans ce s�jour, impossible de changer le patient.";
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // Forfait SE et facturable. A laisser apres le store()
    if ($this->sejour_id && CAppUI::conf("dPcabinet CConsultation attach_consult_sejour")) {
      if($forfait_se !== null) {
        $this->_ref_sejour->forfait_se = $forfait_se;
        if ($msg = $this->_ref_sejour->store()) {
          return $msg;
        }
        $this->_forfait_se = null;
      }
      if($facturable !== null) {
        $this->_ref_sejour->facturable = $facturable;
        if ($msg = $this->_ref_sejour->store()) {
          return $msg;
        }
        $this->_facturable = null;
      }
    }
    
    // Changement du patient pour la consult 
    if ($this->sejour_id && $patient_modified) {
      $this->loadRefSejour();
      
      // Si patient est diff�rent alors on met a jour le sejour
      if ($this->_ref_sejour->patient_id != $this->patient_id) {
        $this->_ref_sejour->patient_id = $this->patient_id;
        if ($msg = $this->_ref_sejour->store()) {
          return $msg;
        }
      }
    }

    if ($this->_adjust_sejour && ($this->_ref_sejour->type === "consult") && $sejour->_id) {
      $consultations = $this->_ref_sejour->countBackRefs("consultations");
      if ($consultations < 1) {
        if ($msg = $this->_ref_sejour->delete()) {
          $this->_ref_sejour->annule = 1;
          if ($msg = $this->_ref_sejour->store()) {
            return $msg;
          }
        }
      }
    }  
    
    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id){
      if($msg = $this->bindTarif()){
        return $msg;
      }
    }
        
    // Bind FSE
    if ($this->_bind_fse && $this->_id) {
      return $this->bindFSE();
    }    
  }
  
  function loadRefCategorie($cache = 0) {
    $this->_ref_categorie = $this->loadFwdRef("categorie_id", $cache);
  }
  
  function loadComplete() {
    parent::loadComplete();
    $this->_ref_patient->loadRefConstantesMedicales();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  function loadRefPatient($cache = 1) {
    $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }
  
  // Chargement du sejour et du RPU dans le cas d'une urgence
  function loadRefSejour($cache = 1){
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
    $this->_ref_sejour->loadRefRPU();
    
    if (CAppUI::conf("dPcabinet CConsultation attach_consult_sejour")) {
      $this->_forfait_se = $this->_ref_sejour->forfait_se;
      $this->_facturable = $this->_ref_sejour->facturable;
    }
  }
  
  function getActeExecution() {
    $this->loadRefPlageConsult();
  }
  
  function loadRefPlageConsult($cache = 1) {
    if ($this->_ref_plageconsult) {
      return; 
    }

    $this->_ref_plageconsult = $this->loadFwdRef("plageconsult_id", $cache);
    $this->_ref_plageconsult->loadRefsFwd($cache);
    
    // Distant fields
    $this->_ref_chir =& $this->_ref_plageconsult->_ref_chir;
    $this->_date     = $this->_ref_plageconsult->date;
    $this->_datetime = mbAddDateTime($this->heure,$this->_date);
    $this->_acte_execution = $this->_datetime;
    $this->_is_anesth    = $this->_ref_chir->isFromType(array("Anesth�siste"));
    $this->_praticien_id = $this->_ref_chir->_id;
  }
  
  function loadRefPraticien(){
  	$this->loadRefPlageConsult();
    return $this->_ref_praticien =& $this->_ref_chir;
  }
  
  function getType() {
    $this->loadRefPraticien();
		$praticien =& $this->_ref_praticien;
		
    $this->loadRefSejour();
		$sejour =& $this->_ref_sejour;
    $sejour->loadRefRPU();
		
    // Consultations d'urgences
    if ($praticien->isUrgentiste() && $sejour->_ref_rpu->_id) {
      $this->_type = "urg";
    }
		
		// Consultation d'anesth�sie
    if ($praticien->isAnesth()) {
      $this->_type = "anesth";
    }
  }
  
  function preparePossibleActes() {
  	$this->loadRefPlageConsult();
  }
  
  function loadRefsFwd($cache = 1) {
    $this->loadRefPatient($cache);
    $this->_ref_patient->loadRefConstantesMedicales(1);
    $this->loadRefPlageConsult($cache);
    $this->_view = "Consult. de ".$this->_ref_patient->_view." - ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".mbTransformTime(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
    $this->loadExtCodesCCAM();
  }

  function loadRefsDocs() {
    parent::loadRefsDocs();
    
    // On ajoute les documents de la consultation d'anesth�sie
  	$this->loadRefConsultAnesth();
  	$consult_anesth =& $this->_ref_consult_anesth;
    if ($consult_anesth->_id) {
      $consult_anesth->loadRefsDocs();
      $this->_ref_documents = CMbArray::mergeKeys($this->_ref_documents, $consult_anesth->_ref_documents);
    }

    return count($this->_ref_documents);
  }
  
  function getExecutantId($code_activite = null) {
  	$this->loadRefPlageConsult();
    return $this->_praticien_id;
  }
  
  function countDocItems($permType = null){
  	if (!$this->_nb_files_docs) {
      parent::countDocItems($permType);
    }
    
    if ($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs)";
    }
  }
  
  function countDocs(){
    $nbDocs = parent::countDocs();
		
    // Ajout des documents de la consultation d'anesth�sie     
   	$this->loadRefConsultAnesth();
    if ($this->_ref_consult_anesth->_id) {
      $nbDocs += $this->_ref_consult_anesth->countDocs();
    }

    return $nbDocs;
  }

  function loadRefConsultAnesth() {
  	$this->_ref_consult_anesth = $this->loadUniqueBackRef("consult_anesth");
  }
  
  function loadRefsExamAudio(){
  	// @todo Ne pas utiliser la backref => ne fonctionne pas 
  	//$this->_ref_examaudio = $this->loadUniqueBackRef("examaudio");

  	$this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
  }
  
  function loadRefsExamNyha(){
    $this->_ref_examnyha = $this->loadUniqueBackRef("examnyha");
  }
  
  function loadRefsExamPossum(){
    $this->_ref_exampossum = $this->loadUniqueBackRef("exampossum");
  }
  
  function loadRefsExamIgs(){
    $this->_ref_examigs = $this->loadUniqueBackRef("examigs");
  }
  
  function loadRefsFichesExamen() {
    $this->loadRefsExamAudio();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->loadRefsExamIgs();
    $this->_count_fiches_examen = 0;
    $this->_count_fiches_examen += $this->_ref_examaudio ->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_examnyha  ->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_exampossum->_id ? 1 : 0; 
    $this->_count_fiches_examen += $this->_ref_examigs   ->_id ? 1 : 0; 
  }
  
  // Chargement des prescriptions li�es � la consultation
  function loadRefsPrescriptions() {
  	$prescriptions = $this->loadBackRefs("prescriptions");
    // Cas du module non install�
    if(!is_array($prescriptions)){
      $this->_ref_prescriptions = null;
      return;
  	}
  	$this->_count_prescriptions = count($prescriptions);
  	
    foreach($prescriptions as &$prescription){
    	$this->_ref_prescriptions[$prescription->type] = $prescription;
    }
  }

  function loadRefsReglements() {
    $this->_ref_reglements = $this->loadBackRefs('reglements', 'date');
    $this->_ref_reglements_patient = array();
    $this->_ref_reglements_tiers   = array();
    
    foreach ($this->_ref_reglements as $_reglement) {
      $_reglement->loadRefBanque(1);
      if($_reglement->emetteur == "patient") {
        $this->_ref_reglements_patient[$_reglement->_id] = $_reglement;
      } else {
        $this->_ref_reglements_tiers[$_reglement->_id] = $_reglement;
      }
    }
    
    // Calcul de la somme du restante du patient
    $this->_du_patient_restant = $this->du_patient;
    $this->_reglements_total_patient = 0;
    foreach ($this->_ref_reglements_patient as $_reglement) {
      $this->_du_patient_restant -= $_reglement->montant;
      $this->_reglements_total_patient += $_reglement->montant;
    }
    $this->_du_patient_restant       = round($this->_du_patient_restant, 2);
    $this->_reglements_total_patient = round($this->_reglements_total_patient, 2);
    
    // Calcul de la somme du restante du tiers
    $this->_du_tiers_restant = $this->du_tiers;
    $this->_reglements_total_tiers = 0;
    foreach ($this->_ref_reglements_tiers as $_reglement) {
      $this->_du_tiers_restant -= $_reglement->montant;
      $this->_reglements_total_tiers += $_reglement->montant;
    }
    $this->_du_tiers_restant       = round($this->_du_tiers_restant, 2);
    $this->_reglements_total_tiers = round($this->_reglements_total_tiers, 2);
  }
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsDocItems();
    $this->countDocItems();
    $this->loadRefConsultAnesth();
    
    $this->loadExamsComp();
    
    $this->loadRefsFichesExamen();
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
    $this->loadRefsReglements();
  }
  
  function loadExamsComp(){
  	$this->_ref_examcomp = new CExamComp;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $order = "examen";
    $this->_ref_examcomp = $this->_ref_examcomp->loadList($where,$order);
    
    foreach($this->_ref_examcomp as $keyExam => &$currExam){
      $this->_types_examen[$currExam->realisation][$keyExam] = $currExam;
    }
  }
  
  static function getExamFields() {
    $fields = array(
      "motif",
      "rques",
    );
    if(CAppUI::conf("dPcabinet CConsultation show_histoire_maladie")) {
      $fields[] = "histoire_maladie";
    }
    if(CAppUI::conf("dPcabinet CConsultation show_examen")) {
      $fields[] = "examen";
    }
    if(CAppUI::pref("view_traitement")) {
      $fields[] = "traitement";
    }
    if(CAppUI::conf("dPcabinet CConsultation show_conclusion")) {
      $fields[] = "conclusion";
    }
    return $fields;
  }
  
  function getPerm($permType) {
    if (!$this->_ref_plageconsult) {
      $this->loadRefPlageConsult();
    }
    return $this->_ref_plageconsult->getPerm($permType);
  }
  
  function fillTemplate(&$template) {
    $this->updateFormFields();
  	$this->loadRefsFwd();
    $this->_ref_plageconsult->loadRefsFwd();
		$this->_ref_plageconsult->_ref_chir->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    if (CModule::getActive('dPprescription')) {
      // Chargement du fillTemplate de la prescription
	    $this->loadRefsPrescriptions();
	    $prescription = isset($this->_ref_prescriptions["externe"]) ? $this->_ref_prescriptions["externe"] : new CPrescription();
	    $prescription->type = "externe";
	    $prescription->fillLimitedTemplate($template);
    }
		$this->loadRefSejour();
		if ($this->_ref_sejour->_id) {
			$this->_ref_sejour->fillLimitedTemplate($template);
		}
	
  }
  
  function fillLimitedTemplate(&$template) {
    $this->updateFormFields();
    $this->loadRefsFwd();
    
    $template->addDateProperty("Consultation - date"  , $this->_ref_plageconsult->date);
    $template->addLongDateProperty("Consultation - date longue", $this->_ref_plageconsult->date);
    $template->addTimeProperty("Consultation - heure" , $this->heure);
    $locExamFields = array(
      "motif"            => "motif",
      "rques"            => "remarques",
      "examen"           => "examen",
      "traitement"       => "traitement",
      "histoire_maladie" => "histoire maladie",
      "conclusion"       => "conclusion"
    );
    foreach($this->_exam_fields as $field) {
    	$loc_field = $locExamFields[$field];
      $template->addProperty("Consultation - $loc_field", $this->$field);
    }
    if(!in_array("traitement", $this->_exam_fields)) {
      $template->addProperty("Consultation - traitement", $this->traitement);
    }
		
    $medecin = new CMedecin();
    $medecin->load($this->adresse_par_prat_id);
    $nom = "{$medecin->nom} {$medecin->prenom}";
    $template->addProperty("Consultation - adress� par", $nom);
    $template->addProperty("Consultation - adress� par - adresse", "{$medecin->adresse}\n{$medecin->cp} {$medecin->ville}");
  }
    
  function canDeleteEx() {
    // Date d�pass�e
    $this->loadRefPlageConsult();
    if ($this->_ref_plageconsult->date < mbDate()) {
      return "Impossible de supprimer une consultation pass�e";
    }
    return parent::canDeleteEx();
  }
  
  private function adjustSejour(CSejour $sejour, $dateTimePlage) {
    if ($sejour->_id == $this->_ref_sejour->_id) {
      return;
    }
    
    // Journ�e dans lequel on d�place � d�j� un s�jour 
    if ($sejour->_id) {
      // Affecte � la consultation le nouveau s�jour
      $this->sejour_id = $sejour->_id;
      return;
    }
    
    // Journ�e qui n'a pas de s�jour en cible
    $count_consultations = $this->_ref_sejour->countBackRefs("consultations");
    
    // On d�place les dates du s�jour
    if (($count_consultations == 1) && ($this->_ref_sejour->type === "consult")) {
      $this->_ref_sejour->entree_prevue = $dateTimePlage;
      $this->_ref_sejour->sortie_prevue = mbDate($dateTimePlage)." 23:59:59";
      $this->_ref_sejour->_hour_entree_prevue = null;
      $this->_ref_sejour->_hour_sortie_prevue = null;
      if ($msg = $this->_ref_sejour->store()) {
        return $msg;
      }
      
      return;
    } 
    
    // On cr�� le s�jour de consultation
    $sejour->patient_id = $this->patient_id;
    $sejour->praticien_id = $this->_ref_chir->_id;
    $sejour->group_id = CGroups::loadCurrent()->_id;
    $sejour->type = "consult";
    $sejour->entree_prevue = $dateTimePlage;
    $sejour->sortie_prevue = mbDate($dateTimePlage)." 23:59:59";
    if ($msg = $sejour->store()) {
      return $msg;
    }
    $this->sejour_id = $sejour->_id; 
  }
}

?>