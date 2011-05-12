<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CPatient Class
 */
class CPatient extends CMbObject {
  static $dossier_cabinet_prefix = array (
    "dPcabinet" => "?m=dPcabinet&tab=vw_dossier&patSel=",
    "dPpatients" => "?m=dPpatients&tab=vw_full_patients&patient_id="
  );
 
  // http://www.msa47.fr/front/id/msa47/S1153385038825/S1153385272497/S1153385352251
  static $libelle_exo_guess = array(
    "code_exo" => array(
      //0 => null,
      4 => array(
        "affection", 
        "ald", 
        "hors liste"
      ),
      /*3 => array(
        "st�rilit�", 
        "pr�matur�", 
        "HIV"
      ),*/
      5 => array(
        "rente AT", 
        "pension d'invalidit�", 
        "pension militaire", 
        "enceinte", 
        "maternit�",
      ),
      9 => array(
        "FSV", 
        "FNS", 
        "vieillesse"
      ),
    ),
    "_art115" => array(
      true => array(
        "pension militaire"
      )
    ),
    "_type_exoneration" => array(
      "aldHorsListe" =>  array("hors liste"),
      "aldListe" =>      array("ald liste"),
      "aldMultiple" =>   array("ald multiple"),
      "alsaceMoselle" => array("alsace", "moselle"),
      "article115" =>    array("pension militaire"),
      "fns" =>           array("fns", "fsv", "vieillesse"),
      "autreCas" =>      array(),
      "autreCasAlsaceMoselle" => array(),
    ),
  );
  
  static $rangToQualBenef = array(
    "01" => 0,
    "31" => 1,
    "02" => 2,
    "11" => 6,
  );
  
  // DB Table key
  var $patient_id = null;

  // DB Fields
  var $nom              = null;
  var $nom_jeune_fille  = null;
  var $prenom           = null;
  var $prenom_2         = null;
  var $prenom_3         = null;
  var $prenom_4         = null;
  var $nom_soundex2     = null;
  var $nomjf_soundex2   = null;
  var $prenom_soundex2  = null;
  var $naissance        = null;
  var $sexe             = null;
  var $civilite         = null;
  var $adresse          = null;
  var $ville            = null;
  var $cp               = null;
  var $tel              = null;
  var $tel2             = null;
  var $tel_autre        = null;
  var $email            = null;
  var $vip              = null;
  
  var $medecin_traitant_declare = null;
  var $medecin_traitant = null;
  var $incapable_majeur = null;
  var $ATNC             = null;
  var $matricule        = null;
  
  var $code_regime      = null;
  var $caisse_gest      = null;
  var $centre_gest      = null;
  var $code_gestion     = null;
  var $centre_carte     = null;
  var $regime_sante     = null;
  var $rques            = null;
  var $cmu              = null;
  var $ald              = null;
  var $code_exo         = null;
  var $libelle_exo		= null;
  var $notes_amo        = null;
  var $notes_amc        = null;
  var $deb_amo          = null;
  var $fin_amo          = null;
  var $code_sit         = null;
  var $regime_am        = null;
  var $mutuelle_types_contrat = null;
  
  var $rang_beneficiaire= null;
  var $qual_beneficiaire= null; // LogicMax, VitaleVision
  var $rang_naissance   = null;
  var $fin_validite_vitale = null;
  
  var $pays                 = null;
  var $pays_insee           = null;
  var $lieu_naissance       = null;
  var $cp_naissance         = null;
  var $pays_naissance_insee = null;
  var $profession           = null;

  var $employeur_nom     = null;
  var $employeur_adresse = null;
  var $employeur_cp      = null;
  var $employeur_ville   = null;
  var $employeur_tel     = null;
  var $employeur_urssaf  = null;

  var $prevenir_nom     = null;
  var $prevenir_prenom  = null;
  var $prevenir_adresse = null;
  var $prevenir_cp      = null;
  var $prevenir_ville   = null;
  var $prevenir_tel     = null;
  var $prevenir_parente = null;

  var $confiance_nom     = null;
  var $confiance_prenom  = null;
  var $confiance_adresse = null;
  var $confiance_cp      = null;
  var $confiance_ville   = null;
  var $confiance_tel     = null;
  var $confiance_parente = null;

  // Assur�
  var $assure_nom                   = null;
  var $assure_nom_jeune_fille       = null;
  var $assure_prenom                = null;
  var $assure_prenom_2              = null;
  var $assure_prenom_3              = null;
  var $assure_prenom_4              = null;
  var $assure_naissance             = null;
  var $assure_sexe                  = null;
  var $assure_civilite              = null;
  var $assure_adresse               = null;
  var $assure_ville                 = null;
  var $assure_cp                    = null;
  var $assure_tel                   = null;
  var $assure_tel2                  = null;
  var $assure_pays                  = null;
  var $assure_pays_insee            = null;
  var $assure_cp_naissance          = null;
  var $assure_lieu_naissance        = null;
  var $assure_pays_naissance_insee  = null;
  var $assure_profession            = null;
  var $assure_rques                 = null;
  var $assure_matricule             = null;
  
  // Other fields
  var $date_lecture_vitale          = null;
  var $_static_cim10                = null;
  var $_pays_naissance_insee        = null;
  var $_assure_pays_naissance_insee = null;
  
  // Behaviour fields 
  var $_anonyme                     = null;
  
  // Form fields
  var $_age         = null;
  var $_age_assure  = null;
  var $_civilite    = null;
  var $_civilite_long = null;
  var $_longview    = null;
  var $_art115      = null;
  var $_type_exoneration = null;
  var $_exoneration = null;
  var $_can_see_photo = null;
  
  var $_age_min       = null;
  var $_age_max       = null;

  // Vitale behaviour
  var $_bind_vitale   = null;
  var $_update_vitale = null;
  var $_id_vitale     = null;
  
  // Navigation Fields
  var $_dossier_cabinet_url = null;

  // HPRIM Fields
  var $_prenoms          = null; // multiple
  var $_nom_naissance    = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2   = null;
  var $_adresse_ligne3   = null;
  var $_pays             = null;
  var $_IPP              = null;
  var $_fusion           = null; // fusion
  var $_patient_elimine  = null; // fusion
  var $_hprim_initiateur_group_id  = null; // group initiateur du message HPRIM
  
  // Object References
  var $_nb_docs                     = null;
  var $_ref_sejours                 = null;
  var $_ref_consultations           = null;
  var $_ref_prescriptions           = null;
  var $_ref_curr_affectation        = null;
  var $_ref_next_affectation        = null;
  var $_ref_medecin_traitant        = null;
  var $_ref_medecins_correspondants = null;
  var $_ref_dossier_medical         = null;
  var $_ref_IPP                     = null;
  var $_ref_constantes_medicales    = null;
  
  // Distant fields
  var $_ref_praticiens = null; // Praticiens ayant particip� � la pec du patient
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'patients';
    $spec->key   = 'patient_id';
    $spec->measureable = true;
    return $spec;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["constantes"]            = "CConstantesMedicales patient_id";
	  $backProps["consultations"]         = "CConsultation patient_id";
	  $backProps["correspondants"]        = "CCorrespondant patient_id";
	  $backProps["hprim21_patients"]      = "CHprim21Patient patient_id";
	  $backProps["prescriptions_labo"]    = "CPrescriptionLabo patient_id";
	  $backProps["product_deliveries"]    = "CProductDelivery patient_id";
	  $backProps["sejours"]               = "CSejour patient_id";
	  $backProps["dossier_medical"]       = "CDossierMedical object_id";
    $backProps["echanges_hprim"]        = "CEchangeHprim object_id";
    $backProps["echanges_hprim21"]      = "CEchangeHprim21 object_id";
	  return $backProps;
	}  
  
  function getProps() {
    $specs = parent::getProps();
    $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    
    $specs["nom"]               = "str notNull confidential seekable|begin";
    $specs["prenom"]            = "str notNull seekable|begin";
    $specs["prenom_2"]          = "str";
    $specs["prenom_3"]          = "str";
    $specs["prenom_4"]          = "str";
    $specs["nom_jeune_fille"]   = "str confidential";
    $specs["nom_soundex2"]      = "str";
    $specs["prenom_soundex2"]   = "str";
    $specs["nomjf_soundex2"]    = "str";
    $specs["medecin_traitant_declare"] = "bool";
    $specs["medecin_traitant"]  = "ref class|CMedecin";
    $specs["matricule"]         = "code insee confidential mask|9S99S99S9xS999S999S99";
    $specs["code_regime"]       = "numchar length|2";
    $specs["caisse_gest"]       = "numchar length|3";
    $specs["centre_gest"]       = "numchar length|4";
    $specs["code_gestion"]      = "str length|2";
    $specs["centre_carte"]      = "numchar length|4";
    $specs["regime_sante"]      = "str";
    $specs["sexe"]              = "enum list|m|f default|m";
    $specs["civilite"]          = "enum list|m|mme|mlle|enf|dr|pr|me|vve default|m";
    $specs["adresse"]           = "text confidential";
    $specs["ville"]             = "str confidential seekable";
    $specs["cp"]                = "numchar minLength|4 maxLength|5 confidential";
    $specs["tel"]               = "numchar confidential length|10 mask|$phone_number_format";
    $specs["tel2"]              = "numchar confidential length|10 mask|$phone_number_format";
    $specs["tel_autre"]         = "str maxLength|20";
    $specs["email"]             = "str confidential";
    $specs["vip"]               = "bool default|0";
    $specs["incapable_majeur"]  = "bool";
    $specs["ATNC"]              = "bool";
    
    $conf = CAppUI::conf("dPpatients CPatient identitovigilence");
    if($conf === "date" || $conf === "doublons"){
      $specs["naissance"]       = "birthDate notNull mask|99/99/9999 format|$3-$2-$1";
    } else {
      $specs["naissance"]       = "birthDate mask|99/99/9999 format|$3-$2-$1";  
    }
    
    $specs["rques"]             = "text";
    $specs["cmu"]               = "bool";
    $specs["ald"]               = "bool";
    $specs["code_exo"]          = "enum list|0|4|5|9 default|0";
    $specs["libelle_exo"]		    = "text";
    $specs["deb_amo"]           = "date";
    $specs["fin_amo"]           = "date";
    $specs["notes_amo"]         = "text";
    $specs["notes_amc"]         = "text";
    $specs["rang_beneficiaire"] = "enum list|01|02|09|11|12|13|14|15|16|31";
    $specs["qual_beneficiaire"] = "enum list|0|1|2|3|4|5|6|7|8|9";
    $specs["rang_naissance"]    = "enum list|1|2|3|4|5|6 default|1";
    $specs["fin_validite_vitale"] = "date";
    $specs["code_sit"]          = "numchar length|4";
    $specs["regime_am"]         = "bool default|0";
    $specs["mutuelle_types_contrat"] = "text";
    
    $specs["pays"]                 = "str";
    $specs["pays_insee"]           = "str";
    $specs["lieu_naissance"]       = "str";
    $specs["cp_naissance"]         = "numchar minLength|4 maxLength|5";
    $specs["pays_naissance_insee"] = "str";
    $specs["profession"]           = "str autocomplete";
      
    $specs["employeur_nom"]     = "str confidential";
    $specs["employeur_adresse"] = "text";
    $specs["employeur_cp"]      = "numchar minLength|4 maxLength|5";
    $specs["employeur_ville"]   = "str confidential";
    $specs["employeur_tel"]     = "numchar confidential length|10 mask|$phone_number_format";
    $specs["employeur_urssaf"]  = "numchar length|11 confidential";

    $specs["prevenir_nom"]      = "str confidential";
    $specs["prevenir_prenom"]   = "str";
    $specs["prevenir_adresse"]  = "text";
    $specs["prevenir_cp"]       = "numchar minLength|4 maxLength|5";
    $specs["prevenir_ville"]    = "str confidential";
    $specs["prevenir_tel"]      = "numchar confidential length|10 mask|$phone_number_format";
    $specs["prevenir_parente"]  = "enum list|conjoint|enfant|ascendant|colateral|divers";
    
    $specs["confiance_nom"]      = "str confidential";
    $specs["confiance_prenom"]   = "str";
    $specs["confiance_adresse"]  = "text";
    $specs["confiance_cp"]       = "numchar minLength|4 maxLength|5";
    $specs["confiance_ville"]    = "str confidential";
    $specs["confiance_tel"]      = "numchar confidential length|10 mask|$phone_number_format";
    $specs["confiance_parente"]  = "enum list|conjoint|enfant|ascendant|colateral|divers";
    
    $specs["assure_nom"]                  = "str confidential";
    $specs["assure_prenom"]               = "str";
    $specs["assure_prenom_2"]             = "str";
    $specs["assure_prenom_3"]             = "str";
    $specs["assure_prenom_4"]             = "str";
    $specs["assure_nom_jeune_fille"]      = "str confidential";
    $specs["assure_sexe"]                 = "enum list|m|f default|m";
    $specs["assure_civilite"]             = "enum list|m|mme|mlle|enf|dr|pr|me|vve default|m";
    $specs["assure_naissance"]            = "birthDate confidential mask|99/99/9999 format|$3-$2-$1";
    $specs["assure_adresse"]              = "text confidential";
    $specs["assure_ville"]                = "str confidential";
    $specs["assure_cp"]                   = "numchar minLength|4 maxLength|5 confidential";
    $specs["assure_tel"]                  = "numchar confidential length|10 mask|$phone_number_format";
    $specs["assure_tel2"]                 = "numchar confidential length|10 mask|$phone_number_format";
    $specs["assure_pays"]                 = "str";
    $specs["assure_pays_insee"]           = "str";
    $specs["assure_lieu_naissance"]       = "str";
    $specs["assure_lieu_naissance"]       = "str";
    $specs["assure_cp_naissance"]         = "numchar minLength|4 maxLength|5";
    $specs["assure_pays_naissance_insee"] = "str";
    $specs["assure_profession"]           = "str autocomplete";
    $specs["assure_rques"]                = "text";
    $specs["assure_matricule"]            = "code insee confidential mask|9S99S99S99S999S999S99";
    $specs["date_lecture_vitale"]         = "dateTime";
    $specs["_id_vitale"]                  = "num";
    $specs["_pays_naissance_insee"]       = "str";
    $specs["_assure_pays_naissance_insee"]= "str";
    $specs["_art115"]                     = "bool";
    
    $types_exo = array(
      "aldHorsListe",
      "aldListe",
      "aldMultiple",
      "alsaceMoselle",
      "article115",
      "autreCas",
      "autreCasAlsaceMoselle",
      "fns",
    );
    
    $specs["_type_exoneration"]           = "enum list|".implode("|", $types_exo);
    $specs["_age"]                        = "num show|1";
    $specs["_age_assure"]                 = "num";
    
    $specs["_age_min"]                    = "num min|0";
    $specs["_age_max"]                    = "num min|0";
    
    $specs["_IPP"]                        = "str";
    
    return $specs;
  }
  
  function checkMerge($patients = array()/*<CPatient>*/) {
    if ($msg = parent::checkMerge($patients)) {
      return $msg;
    }
		
		$sejour = new CSejour;
		$where["patient_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($patients, "_id"));
		$sejours = $sejour->loadList($where);
		
		foreach ($sejours as $_sejour1) {
	    foreach ($sejours as $_sejour2) {
        if ($_sejour1->collides($_sejour2)) {
          $_sejour1->loadRefPatient(1);
          $_sejour2->loadRefPatient(1);
          return CAppUI::tr("CPatient-merge-warning-venue-conflict", $_sejour1->_view, $_sejour2->_view);
        }
	    }
		}
  }
  
  function merge($objects = array/*<CPatient>*/(), $fast = false) {
  	// Load the matching CDossierMedical objects 
  	$where = array(
  	  'object_class' => "='$this->_class_name'",
  	  'object_id'    => CSQLDataSource::prepareIn(CMbArray::pluck($objects, 'patient_id'))
  	);
  	$dossier_medical = new CDossierMedical();
  	$list = $dossier_medical->loadList($where);
  	
    foreach ($objects as $object) {
      $object->loadIPP();
    }
    
  	if ($msg = parent::merge($objects, $fast)) return $msg;
    
    // Merge them
    if (count($list) > 1) {
    	if ($msg = $dossier_medical->mergeDBFields($list)) return $msg;
    	$dossier_medical->object_class = $this->_class_name;
    	$dossier_medical->object_id = $this->_id;
    	return $dossier_medical->merge($list);
    }
  }
  
  function check(){
    // Standard check
    if ($msg = parent::check()) {
      return $msg;
    }

    // Creation d'un patient
    if(!$this->_merging && !$this->_id && CAppUI::conf('dPpatients CPatient identitovigilence') == "doublons"){
      if($this->loadMatchingPatient(true, false) > 0) {
        return "Doublons d�tect�s";
      }
    }
  }
	
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->checkAnonymous()) {
    	$this->nom = $this->_id;
    	$this->_anonyme = true;
    	$this->store();
    }
    
    // Vitale
    if ($msg = $this->bindVitale()) {
      return $msg;
    }
  }
       
  /**
   * Bind LogicMax idex to patient
   * @return Store-like message
   */
  function bindVitale() {
    if (!$this->_bind_vitale) {
      return;
    }
    
    $this->_bind_vitale = null;
    
    if (!$this->_id) {
      return;
    }
    
    // Make id400
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $vitNumero = $vitale["VIT_NUMERO_LOGICMAX"];
    $id_vitale = new CIdSante400();
    $id_vitale->object_class = $this->_class_name;
    $id_vitale->id400 = $vitNumero;
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    
    // Autre association ?
    if ($id_vitale->object_id && $id_vitale->object_id != $this->_id) {
      $id_vitale->loadTargetObject();
      $patOther =& $id_vitale->_ref_object;
      return "B�n�ficiaire Vitale d�j� associ� au patient " . $patOther->_view .
        " n� le " . mbDateToLocale($patOther->naissance);
    }
    
    $id_vitale->object_id = $this->_id;
    $id_vitale->last_update = mbDateTime();
    
    if ($msg = $id_vitale->store()) {
      return $msg;
    }

    // Mise � jour dupuis Vitale
    if ($this->_update_vitale) {
      $patient_vitale = new CPatient;
      $patient_vitale->getValuesFromVitale();
      $patient_vitale->date_lecture_vitale = mbDateTime();
      foreach (array_keys($this->getDBFields()) as $field) {
        $vitale_value = $patient_vitale->$field;
        if ($vitale_value || $vitale_value === "0") {
          $this->$field = $patient_vitale->$field;
        }
      }
      
	    if ($msg = $this->store()) {
	      return $msg;
	    }
    }
  }

  function loadIdVitale() {
    if (!$this->_id) {
      return;
    }
    
    $id_vitale = new CIdSante400();
    if (!$id_vitale->_ref_module) {
      return;
    }
    
    $id_vitale->setObject($this);
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    $this->_id_vitale = $id_vitale->id400;
  }
  
  /**
   * Load exact patient associated with id vitale
   */
  function loadFromIdVitale() {
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $vitNumero = $vitale["VIT_NUMERO_LOGICMAX"];
    
    // Make id vitale
    $id_vitale = new CIdSante400();
    $id_vitale->object_class = $this->_class_name;
    $id_vitale->id400 = $vitNumero;
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    
    // Load patient from found id vitale
    if ($id_vitale->object_id) {
      $this->load($id_vitale->object_id);
    }
  }
  
  function getValuesFromVitale() {
    if (null == $intermax = CValue::postOrSessionAbs("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $this->nom    = $vitale["VIT_NOM"];
    $this->prenom = $vitale["VIT_PRENOM"];
    $this->naissance = mbDateFromLocale($vitale["VIT_DATE_NAISSANCE"]);
    
    $this->rang_naissance = $vitale["VIT_RANG_GEMELLAIRE"];
    
    // Adresse
    $this->adresse = trim(implode("\n", array(
      $vitale["VIT_ADRESSE_1"],
      $vitale["VIT_ADRESSE_2"],
      $vitale["VIT_ADRESSE_3"],
      $vitale["VIT_ADRESSE_4"])
    ));
    
    // CP et ville
    @list($this->cp, $this->ville) = explode(" ", $vitale["VIT_ADRESSE_5"], 2);
    
    // Matricules
    $this->assure_matricule = $vitale["VIT_NUMERO_SS"].$vitale["VIT_CLE_SS"];
    $this->matricule = $this->assure_matricule;
    if (CValue::read($vitale, "VIT_NUMERO_SS_INDIV")) {
      $this->matricule = $vitale["VIT_NUMERO_SS_INDIV"].$vitale["VIT_CLE_SS_INDIV"];
    }
    
    $sexeMatrix = array (
      "1" => "m",
      "2" => "f",
    );
    
    // Sexe r�cup�r� que quand le b�n�ficiaire est l'assur�
    if ($vitale["VIT_CODE_QUALITE"] == "00") {
      $this->sexe = $sexeMatrix[$this->matricule[0]];
    }

    // Assur�
    $this->assure_nom          = $vitale["VIT_NOM_ASSURE"];
    $this->assure_prenom       = $vitale["VIT_PRENOM_ASSURE"];
    $this->fin_validite_vitale = mbDateFromLocale($vitale["VIT_FIN_VALID_VITALE"]);
    
    // R�gime
    $this->code_regime  = $vitale["VIT_CODE_REGIME"];
    $this->caisse_gest  = $vitale["VIT_CAISSE_GEST"];
    $this->centre_gest  = $vitale["VIT_CENTRE_GEST"];
    $this->regime_sante = CValue::read($vitale, "VIT_NOM_AMO");
    
    //@todo: quelle est la cl� pour le code gestion ?
    //$this->code_gestion = CValue::read($vitale, "??");
    
    $this->qual_beneficiaire = intval($vitale["VIT_CODE_QUALITE"]);
    
    // Recherche de la p�riode AMO courante
    foreach ($intermax as $category => $periode) {
      if (preg_match("/PERIODE_AMO_(\d)+/i", $category)) {
        $deb_amo = mbDateFromLocale($periode["PER_AMO_DEBUT"]);
        $fin_amo = CValue::first(mbDateFromLocale($periode["PER_AMO_FIN"]), "2015-01-01");
        if (CMbRange::in(mbDate(), $deb_amo, $fin_amo)) {
          $this->deb_amo  = $deb_amo;
          $this->fin_amo  = $fin_amo;
          $this->ald      = $periode["PER_AMO_ALD"];
          $this->code_exo = $periode["PER_AMO_CODE_EXO"];
          $this->code_sit = $periode["PER_AMO_CODE_SIT"];
          $this->cmu      = $vitale["VIT_CMU"];
        }
      }
    }
    
    $this->regime_am = CValue::read($vitale, "VIT_REGIME_AM");
  }
  
  function guessExoneration(){
    $this->completeField("libelle_exo");
   
    if (!$this->libelle_exo) return;
    
    foreach(self::$libelle_exo_guess as $field => $values) {
      if ($this->$field !== null) continue;
      
      foreach($values as $value => $rules) {
        foreach($rules as $rule) {
          if (preg_match("/$rule/i", $this->libelle_exo)) {
            $this->$field = $value;
            break;
          }
        }
      }
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Noms
    $this->nom = CMbString::upper($this->nom);
    $this->nom_jeune_fille = CMbString::upper($this->nom_jeune_fille);
    $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));
    
    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom; 
    $this->_prenoms = array($this->prenom, $this->prenom_2, $this->prenom_3, $this->prenom_4);
  
    if($this->libelle_exo) {
      $this->_art115 = preg_match("/pension militaire/i", $this->libelle_exo);
    }
  
    $this->evalAge();
		
    $this->_civilite = CAppUI::tr("CPatient.civilite.$this->civilite");
    if ($this->civilite === "enf") {
      $this->_civilite_long = $this->sexe === "m" ? CAppUI::tr("CPatient.civilite.le_jeune") : CAppUI::tr("CPatient.civilite.la_jeune");
    } else {
    	$this->_civilite_long = CAppUI::tr("CPatient.civilite.$this->civilite-long");
    }
    
    $nom_naissance   = $this->nom_jeune_fille && $this->nom_jeune_fille != $this->nom ? " ($this->nom_jeune_fille)" : "";
    $this->_view     = "$this->_civilite $this->nom$nom_naissance $this->prenom";
    $this->_longview = "$this->_civilite_long $this->nom$nom_naissance $this->prenom";
		
   // Navigation fields
    //$this->_dossier_cabinet_url = self::$dossier_cabinet_prefix[CAppUI::pref("DossierCabinet")] . $this->_id;
    $this->_dossier_cabinet_url = self::$dossier_cabinet_prefix["dPpatients"] . $this->_id;
  }
  
  /**
   * Calcul l'�ge du patient en ann�es
   * @param date $date Date de r�f�rence pour le calcul, maintenant si null
   * @return int l'age du patient en ann�es
   */
  function evalAge($date = null) {
  	$achieved = CMbDate::achievedDurations($this->naissance, $date);
		return $this->_age = $achieved["year"];
  }
  
  /**
   * Calcul l'�ge de l'assur� en ann�es
   * @param date $date Date de r�f�rence pour le calcul, maintenant si null
   * @return int l'age de l'assur� en ann�es
   */
  function evalAgeAssure($date = null) {
    $achieved = CMbDate::achievedDurations($this->assure_naissance, $date);
    return $this->_age_assure = $achieved["year"];
  }

  /**
   * Calcul l'�ge du patient en mois
   * @param date $date Date de r�f�rence pour le calcul, maintenant si null
   * @return int l'age du patient en mois
   */
  function evalAgeMois($date = null){
    $achieved = CMbDate::achievedDurations($this->naissance, $date);
    return $achieved["month"];
  }
  
  /**
   * Calcul l'�ge du patient en semaines
   */
  function evalAgeSemaines($date = null){
    $jours = $this->evalAgeJours($date);
    return intval($jours/7);
  }
  
  /**
   * Calcul l'�ge du patient en jours
   */
  function evalAgeJours($date = null){
    $date = $date ? $date : mbDate();
    if (!$this->naissance || $this->naissance === "0000-00-00") {
      return 0;
    }
    return mbDaysRelative($this->naissance, $date);
  }
      
  function updateDBFields() {
  	parent::updateDBFields();
  	 
    $soundex2 = new soundex2;
    if ($this->nom) {
  	  $this->nom = CMbString::upper($this->nom);
      $this->nom_soundex2 = $soundex2->build($this->nom);
    }
    
    if ($this->nom_jeune_fille) {
  	  $this->nom_jeune_fille = CMbString::upper($this->nom_jeune_fille);
      $this->nomjf_soundex2 = $soundex2->build($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));
      $this->prenom_soundex2 = $soundex2->build($this->prenom);
    }
    
    if ($this->cp === "00000") {
      $this->cp = "";
    }
  	
    if ($this->assure_nom) {
  	  $this->assure_nom = CMbString::upper($this->assure_nom);
    }
    
    if ($this->assure_nom_jeune_fille) {
  	  $this->assure_nom_jeune_fille = CMbString::upper($this->assure_nom_jeune_fille);
    }

    if ($this->assure_prenom) {
      $this->assure_prenom = CMbString::capitalize(CMbString::lower($this->assure_prenom));
    }
  	
    if ($this->assure_cp === "00000") {
      $this->assure_cp = "";
    }
    
    if ($this->_pays_naissance_insee) {
      $this->pays_naissance_insee = $this->updatePatNumPaysInsee($this->_pays_naissance_insee);
    }
    
    if ($this->pays) {
      $this->pays_insee = $this->updatePatNumPaysInsee($this->pays);
    }
    
    if ($this->_assure_pays_naissance_insee) {
      $this->assure_pays_naissance_insee = $this->updatePatNumPaysInsee($this->_assure_pays_naissance_insee);
    }
    
    if ($this->assure_pays) {
      $this->assure_pays_insee = $this->updatePatNumPaysInsee($this->assure_pays);
    }
		
		// D�termine la civilit� du patient automatiquement (utile en cas d'import)
		$this->completeField("civilite");
		if ($this->civilite === "guess" || !$this->civilite) {
      $this->evalAge();
			$this->civilite = ($this->_age < CAppUI::conf("dPpatients CPatient adult_age")) ?
	      "enf" : (($this->sexe === "m") ? "m" : "mme");
		}
		
    // D�termine la civilit� de l'assure automatiquement (utile en cas d'import)
    $this->completeField("assure_civilite");
		if ($this->assure_civilite === "guess" || !$this->assure_civilite) {
      $this->evalAgeAssure();
      $sexe = $this->assure_sexe ? $this->assure_sexe : $this->sexe;
      $this->assure_civilite = ($this->_age_assure < CAppUI::conf("dPpatients CPatient adult_age")) ?
        "enf" : (($sexe === "m") ? "m" : "mme");
    }
  }
  
  // Backward references
  function loadRefsSejours($where = null) {
    if (!$this->_id) { 
    	return array();
    }
    
  	$sejour = new CSejour;
    if ($where === null) {
      $where = array();
    }

    $where["patient_id"] = "= '$this->_id'";
    $order = "entree DESC";
    return $this->_ref_sejours = $sejour->loadList($where, $order);
  }
  
  function getCurrSejour($dateTime = null) {
    if (!$dateTime) {
      $dateTime = mbDateTime();
    }
    
    $where[] = "'$dateTime' BETWEEN entree AND sortie";
    $this->loadRefsSejours($where);
  }
  
  /*
   * Get the next sejour from today or from a given date
   * @return array(CSejour, COperation);
   */ 
  function getNextSejourAndOperation($date = null, $withOperation = true) {
    $sejour = new CSejour;
    $op     = new COperation;
    if(!$date) {
      $date = mbDate();
    }
    if(!$this->_ref_sejours) {
      $this->loadRefsSejours();
    }
    foreach($this->_ref_sejours as $_sejour) {
      if(in_array($_sejour->type, array("ambu", "comp", "exte")) && !$_sejour->annule && $_sejour->entree_prevue >= $date) {
        if(!$sejour->_id) {
          $sejour = $_sejour;
        } 
        else {
          if($_sejour->entree_prevue < $sejour->entree_prevue) {
            $sejour = $_sejour;
          }
        }
        
        if ($withOperation) {
          if(!$_sejour->_ref_operations) {
            $_sejour->loadRefsOperations(array("annulee" => "= '0'"));
          }
          foreach($_sejour->_ref_operations as $_op) {
            $_op->loadRefPlageOp();
            if(!$op->_id) {
              $op = $_op;
            } 
            else {
              if($_op->_datetime < $op->_datetime) {
                $op = $_op;
              }
            }
          }
        }
      }
    }
    return array("CSejour" => $sejour, "COperation" => $op);
  }
  
  /**
   * Get an associative array of uncancelled sejours and their dates
   * @return array Sejour ID => array("entree_prevue" => DATE, "sortie_prevue" => DATE)
   */
  function getSejoursCollisions() {
    $sejours_collision = array();
    $group_id = CGroups::loadCurrent()->_id;
    
    if ($this->_ref_sejours) {
			foreach ($this->_ref_sejours as $_sejour) {
			  if (!$_sejour->annule && $_sejour->group_id == $group_id && !in_array($_sejour->type, array("urg", "seances", "consult"))) {
				  $sejours_collision[$_sejour->_id] = array (
				    "entree" => mbDate($_sejour->_entree),
				    "sortie" => mbDate($_sejour->_sortie)
				  );
			  }
			}
    }
    
    return $sejours_collision;
  }
  
  /**
   * Charge le patient ayant les traits suivants :
   * - M�me nom � la casse et aux s�parateurs pr�s
   * - M�me pr�nom � la casse et aux s�parateurs pr�s
   * - Strictement la m�me date de naissance
   * @param $other
   * @param $loadObject Permet de ne pas charger le patient, seulement renvoyer le nombre de matches
   * @return Nombre d'occurences trouv�es 
   */
  function loadMatchingPatient($other = false, $loadObject = true) {
    $ds = $this->_spec->ds;
    
    if ($other && $this->_id) {
   	  $where["patient_id"] = " != '$this->_id'";
    }
        
    $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom);
    $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom);

    if ($this->nom_jeune_fille) {
	    $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom_jeune_fille);
	    $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom_jeune_fille);
    } 

    $where[] = implode(" OR ", $whereOr);
    $where["prenom"]          = $ds->prepareLikeName($this->prenom);

    if ($this->prenom_2) $where["prenom_2"] = $ds->prepareLikeName($this->prenom_2);
    if ($this->prenom_3) $where["prenom_3"] = $ds->prepareLikeName($this->prenom_3);
    if ($this->prenom_4) $where["prenom_4"] = $ds->prepareLikeName($this->prenom_4);

    $where["naissance"] = $ds->prepare("= %", $this->naissance);
    
    if ($loadObject) {
      $this->loadObject($where);
    }
		
    return $this->countList($where);
  }
	
  /**
   * Finds patient siblings with at least two exact matching traits out of 
   * nom, prenom, naissance
   * Optimized version with split queries for index usage forcing
   * @return array[CPatient] Array of siblings
   */
  function getSiblings() {
    $ds =& $this->_spec->ds;

    $where = array (
      "nom"    => $ds->prepareLikeName($this->nom),
      "prenom" => $ds->prepareLikeName($this->prenom),
      "patient_id" => "!= '$this->_id'",
    );
		
    $siblings = $this->loadList($where);
    
    if ($this->naissance !== "0000-00-00") {
      $where = array (
        "nom"       => $ds->prepareLikeName($this->nom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));

      $where = array (
        "prenom"    => $ds->prepareLikeName($this->prenom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));
    }
    
    return $siblings;
  }
	  
  /**
   * Find patient phoning similar
   * @param $date restrict to a venue collide date
   * @return array[CPatient] Array of phoning patients
   */
  function getPhoning($date = null) {
    $whereNom[] = "nom_soundex2    LIKE '$this->nom_soundex2%'";
    $whereNom[] = "nomjf_soundex2  LIKE '$this->nom_soundex2%'";

    if ($this->nomjf_soundex2) {
	    $whereNom[] = "nom_soundex2    LIKE '$this->nomjf_soundex2%'";
	    $whereNom[] = "nomjf_soundex2  LIKE '$this->nomjf_soundex2%'";
    } 
    
    $where[] = implode(" OR ", $whereNom);
    $where["prenom_soundex2"] = "LIKE '$this->prenom_soundex2%'";
    $where["patients.patient_id"] = "!= '$this->_id'";

		$join = null;
		if ($date) {
			$join["sejour"] = "sejour.patient_id = patients.patient_id";
			$min = mbDateTime("-1 DAY", $date);
      $max = mbDateTime("+1 DAY", $date);
      $whereDate[] = "sejour.entree_reelle BETWEEN '$min' AND '$max'";
      $whereDate[] = "sejour.entree_prevue BETWEEN '$min' AND '$max'";
      $where[] = implode(" OR ", $whereDate);
		}
		
    return $this->loadList($where, null, null, null, $join);
  }


  function checkSimilar($nom, $prenom) {
    $soundex2 = new soundex2;
    $testNom    = $this->nom_soundex2    == $soundex2->build($nom);
    $testPrenom = $this->prenom_soundex2 == $soundex2->build($prenom);
    return($testNom && $testPrenom);
  }
  
  function loadRefsConsultations($where = null) {
    $consultation = new CConsultation();
    $curr_user = CAppUI::$user;
    if ($this->patient_id){
      if ($where === null) {
        $where = array();
      }
      if(!$curr_user->isAdmin()) {
        $where[] = "functions_mediboard.consults_partagees = '1' ||
                    (functions_mediboard.consults_partagees = '0' && functions_mediboard.function_id = '$curr_user->function_id')";
      }
      $where["patient_id"] = "= '$this->patient_id'";
      $order = "plageconsult.date DESC";
      $leftjoin = array();
      $leftjoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
      $leftjoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
      $leftjoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
      $this->_ref_consultations = $consultation->loadList($where, $order, null, null, $leftjoin);
    }
  }
  
  function loadRefDossierMedical() {
    $this->_ref_dossier_medical = $this->loadUniqueBackRef("dossier_medical");
    $this->_ref_dossier_medical->loadRefsBack();
		return $this->_ref_dossier_medical;
  }
  
  function loadRefsAffectations() {
    $this->loadRefsSejours();
    // Affectation actuelle et prochaine affectation
    $where["affectation.sejour_id"] = CSQLDataSource::prepareIn(array_keys($this->_ref_sejours));
    $where["sejour.group_id"] = ">= '".CGroups::loadCurrent()->_id."'";
    $ljoin["sejour"]      = "sejour.sejour_id = affectation.sejour_id";
    $order = "affectation.entree";
    $now = mbDateTime();
    
    $this->_ref_curr_affectation = new CAffectation();
    if($this->_ref_curr_affectation->_ref_module) {
      $where["affectation.entree"] = "< '$now'";
      $where["affectation.sortie"] = ">= '$now'";
      $this->_ref_curr_affectation->loadObject($where, $order, null, $ljoin);
    } else {
      $this->_ref_curr_affectation = null;
    }
    
    $this->_ref_next_affectation = new CAffectation();
    if($this->_ref_next_affectation->_ref_module) {
      $where["affectation.entree"] = "> '$now'";
      $this->_ref_next_affectation->loadObject($where, $order, null, $ljoin);
    } else {
      $this->_ref_next_affectation = null;
    }
  }
  
  function loadRefsDocs() {
    $docs_valid = parent::loadRefsDocs();
    if ($docs_valid)
      $this->_nb_docs .= "$docs_valid";
  }
  
  function loadRefsPrescriptions($perm = null) {
    if (CModule::getInstalled("dPlabo")) {
      $prescription = new CPrescriptionLabo();
      $where = array("patient_id" => "= '$this->_id'");
      $order = "date DESC";
      $this->_ref_prescriptions = $prescription->loadListWithPerms($perm, $where, $order);
    }
  }
  
  function loadRefConstantesMedicales() {
    $latest = CConstantesMedicales::getLatestFor($this);
    
    list($this->_ref_constantes_medicales, $dates) = $latest;
    $this->_ref_constantes_medicales->updateFormFields();
    
    return $latest;
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsAffectations();
    $this->loadRefsPrescriptions();
  }

  // Forward references
  function loadRefsFwd() {
    $this->loadRefsCorrespondants();
    $this->loadIdVitale();
  }
  
  // Liste statique des codes CIM10 initiaux
  function loadStaticCIM10($user = 0) {
    if (!CAppUI::conf("dPpatients CDossierMedical diags_static_cim")) {
      return;
    }
    
    // Liste des favoris
    if($user) {
      $favoris = new CFavoricim10;
      $where = array();
      $where["favoris_user"] = "= '$user'";
      $order = "favoris_code";
      if ($favoris = $favoris->loadList($where, $order)) {
	      foreach($favoris as $key => $value) {
	        $this->_static_cim10["favoris"][$value->favoris_code] = new CCodeCIM10($value->favoris_code, 1);
	      }
      }
			
      $ds = CSQLDataSource::get("std");
      $sql = "SELECT DP, count(DP) as nb_code
        FROM `sejour`
        WHERE sejour.praticien_id = '$user'
        AND DP IS NOT NULL
        AND DP != ''
        GROUP BY DP
        ORDER BY nb_code DESC
        LIMIT 20;";
      $cimStat = $ds->loadlist($sql);
      foreach($cimStat as $key => $value) {
        $this->_static_cim10["favoris"][$value["DP"]] = new CCodeCIM10($value["DP"], 1);
      }
    }
    
    // Liste statique
    //$this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I20", 1);       // Angor
    //$this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I21", 1);       // Infarctus
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("(I20-I25)", 1); // Cardiopathies ischemiques
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("J81", 1);       // O.A.P ?
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("R60", 1);       // Oedemes
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I776", 1);      // Art�rite
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("R943", 1);      // ECG
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I10", 1);       // HTA
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("A15", 1);       // Pleur�sie1
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("A16", 1);       // Pleur�sie2
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("(J10-J18)", 1); // Pneumonie
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("J45", 1);       // Asthme
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("J180", 1);      // BPCO
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("R230", 1);      // Cyanose
    $this->_static_cim10["divers"][]           = new CCodeCIM10("Z88", 1);       // Allergies
    $this->_static_cim10["divers"][]           = new CCodeCIM10("(B15-B19)", 1); // Hepatite
    $this->_static_cim10["divers"][]           = new CCodeCIM10("(E10-E14)", 1); // Diabete
    $this->_static_cim10["divers"][]           = new CCodeCIM10("H40", 1)      ; // Glaucome
    
    // Sommaire complet
    $sommaire = new CCodeCIM10();
    $sommaire = $sommaire->getSommaire();
    foreach($sommaire as $key => $value) {
      $this->_static_cim10["sommaire"][] = new CCodeCIM10($value["code"], 1);
    }
  }
    
  function loadComplete(){
    parent::loadComplete();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->canRead();
    $this->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_dossier_medical->loadRefsTraitements();  
  }
  
  function loadDossierComplet($permType = null) {
  	$this->_ref_praticiens = array();
    $pat_id = $this->loadRefs();
        
    $this->canRead();
    $this->canEdit();
    $this->countDocItems($permType);
    $this->loadRefPhotoIdentite();
    $this->loadRefsNotes();
    
    // Affectations courantes
    $affectation =& $this->_ref_curr_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    $affectation =& $this->_ref_next_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    // Si le loadRef n'a pas fonctionn�, on arrete la
    if(!$pat_id) {
      return;
    }

    // Consultations
    foreach ($this->_ref_consultations as $keyConsult => $valueConsult) {
      if ($valueConsult->sejour_id) {
        unset($this->_ref_consultations[$keyConsult]);
        continue;
      }

      $consult =& $this->_ref_consultations[$keyConsult];
      
      $consult->loadRefConsultAnesth();
      $consult->loadRefsFichesExamen();
      $consult->loadExamsComp();
      $consult->countDocItems($permType);
      
      $consult->loadRefsFwd(1);
      $this->_ref_praticiens[$consult->_ref_chir->user_id] = $consult->_ref_chir;
      $consult->getType();
      $consult->_ref_chir->loadRefFunction();
      $consult->_ref_chir->_ref_function->loadRefGroup();
      $consult->canRead();
      $consult->canEdit();
    }

    // Sejours
    foreach ($this->_ref_sejours as $keySejour => $valueSejour) {
      $sejour =& $this->_ref_sejours[$keySejour];
      $sejour->loadNumDossier();
      $sejour->loadRefsAffectations();
      $sejour->loadRefsOperations();
      $sejour->countDocItems($permType);
      $sejour->loadRefsFwd(1);
      $this->_ref_praticiens[$sejour->praticien_id] = $sejour->_ref_praticien;
      $sejour->canRead();
      $sejour->canEdit();
      $sejour->countDocItems($permType);
      foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
        $operation =& $sejour->_ref_operations[$keyOp];
        $operation->loadRefsFwd(1);
        $this->_ref_praticiens[$operation->chir_id] = $operation->_ref_chir;
        $operation->countDocItems($permType);
        $operation->canRead();
        $operation->canEdit();
      }
      $sejour->loadRefRPU();
      if($sejour->_ref_rpu && $sejour->_ref_rpu->_id) {
        $sejour->_ref_rpu->countDocItems($permType);
      }
      $sejour->loadRefsConsultations();
      foreach ($sejour->_ref_consultations as $_consult) {
        $_consult->loadRefConsultAnesth();
        $_consult->loadRefsFichesExamen();
        $_consult->loadExamsComp();
        $_consult->countDocItems($permType);
        
        $_consult->loadRefsFwd(1);
        $_consult->getType();
        $_consult->_ref_chir->loadRefFunction();
        $_consult->_ref_chir->_ref_function->loadRefGroup();
        $_consult->canRead();
        $_consult->canEdit();
      }
    }
  }
  
  function loadRefsCorrespondants() {
    // M�decin traitant
    $this->_ref_medecin_traitant = new CMedecin();
    $this->_ref_medecin_traitant->load($this->medecin_traitant);
    
    // Autres correspondant
    $this->_ref_medecins_correspondants = $this->loadBackRefs("correspondants");
    foreach ($this->_ref_medecins_correspondants as &$corresp) {
      $corresp->loadRefsFwd();
    }

  	return $this->_ref_medecins_correspondants;
  }
	
	/**
	 * Construit le tag IPP en fonction des variables de configuration
   * @param $group_id Permet de charger l'IPP pour un �tablissement donn� si non null
	 * @return string
	 */
	static function getTagIPP($group_id = null) {
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return;
    }

    // Permettre des IPP en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    // Pr�f�rer un identifiant externe de l'�tablissement
    if ($tag_group_idex = CAppUI::conf("dPpatients CPatient tag_ipp_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }
    
    if (CAppUI::conf('sip server')) {
      $tag_ipp = CAppUI::conf('sip tag_ipp');
    }
    
    return str_replace('$g', $group_id, $tag_ipp);
	}

  /**
   * Charge l'IPP du patient pour l'�tablissement courant
   * @param $group_id Permet de charger l'IPP pour un �tablissement donn� si non null
   */
  function loadIPP($group_id = null) {
    if (!$this->_id) {
      return;
    }
    
    // Prevent loading twice
    if ($this->_IPP) {
      return;
    }
    
  	// Pas de tag IPP => pas d'affichage d'IPP
  	if (null == $tag_ipp = $this->getTagIPP($group_id)) {
  		$this->_IPP = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
    	return;
    }

    // R�cup�ration du premier IPP cr��, utile pour la gestion des doublons
    $order = "id400 ASC";
  	
  	// Recuperation de la valeur de l'id400
  	$idex = new CIdSante400();
  	$idex->setObject($this);
  	$idex->tag = $tag_ipp;
  	$idex->loadMatchingObject($order);
  	
    // Stockage de la valeur de l'id400
    $this->_ref_IPP = $idex;
    $this->_IPP     = $idex->id400;
  }

  function loadFromIPP($group_id = null) {
    if (!$this->_IPP) {
      return;
    }
    
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = $this->getTagIPP($group_id)) {
      return;
    }
		
    // Recuperation de la valeur de l'id400
    $idex = new CIdSante400();
    $idex->object_class= 'CPatient';
    $idex->tag = $tag_ipp;
    $idex->id400 = $this->_IPP;
    $idex->loadMatchingObject();

    $this->load($idex->object_id);
  }
  
  function loadRefPhotoIdentite() {
    global $can;
    $file = new CFile();
    $file->setObject($this);
    $file->file_name = 'identite.jpg';
    $file->loadMatchingObject();
    $this->_ref_photo_identite = $file;
    $this->_can_see_photo = 1;
    if($this->_ref_photo_identite->_id) {
      $this->_ref_photo_identite->loadLogs();
    
      $author = new CMediusers;
      $author->load($this->_ref_photo_identite->_ref_first_log->_ref_user->_id);
      $author->loadRefFunction();
      
      $current_user = CAppUI::$user;
      $current_user->loadRefFunction();
      $this->_can_see_photo = ($current_user->function_id == $author->function_id) || !($can->admin == ''); 
    }
  }
  
  function fillLimitedTemplate(&$template) {
    $cdestinataire = new CDestinataire;
    $cdestinataire->makeAllFor($this);
    
    $destinataires = CDestinataire::$destByClass;

    foreach($destinataires as $_destinataires_by_class)
      foreach($_destinataires_by_class as $_destinataire) {
        if (!isset($_destinataire->nom) || strlen($_destinataire->nom) == 0 || $_destinataire->nom === " ") continue;
        $template->destinataires[] =
          array("nom"   => $_destinataire->nom,
                "email" => $_destinataire->email,
                "tag"   => $_destinataire->tag);
      }
    $this->loadRefsFwd();
    $this->loadRefConstantesMedicales();
    $this->loadIPP();
    
    $template->addProperty("Patient - article"           , $this->_civilite  );
    $template->addProperty("Patient - article long"      , $this->_civilite_long);
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - nom jeune fille"   , $this->nom_jeune_fille);
    $template->addProperty("Patient - pr�nom"            , $this->prenom     );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - �ge"               , $this->_age       );
    $template->addDateProperty("Patient - date de naissance", $this->naissance);
    $template->addProperty("Patient - lieu de naissance" , $this->lieu_naissance);
    $template->addProperty("Patient - num�ro d'assur�"   , $this->getFormattedValue("matricule"));
    $template->addProperty("Patient - t�l�phone"         , $this->getFormattedValue("tel"));
    $template->addProperty("Patient - mobile"            , $this->getFormattedValue("tel2"));
    $template->addProperty("Patient - t�l�phone autre"   , $this->tel_autre  );
    $template->addProperty("Patient - profession"        , $this->profession );
    $template->addProperty("Patient - IPP"               , $this->_IPP       );
    $template->addProperty("Patient - Qualit� b�n�ficiaire", $this->qual_beneficiaire);

    $this->guessExoneration();
    $template->addProperty("Patient - Qualit� b�n�ficiaire - Libell�", $this->libelle_exo);
    $template->addProperty("Patient - Num�ro de s�curit� sociale", $this->getFormattedValue("matricule"));
    $template->addBarcode ("Patient - Code barre ID"     , "PID$this->_id"   );
    $template->addBarcode ("Patient - Code barre IPP"    , "IPP$this->_IPP"  );
    
    if ($this->sexe === "m"){
      $template->addProperty("Patient - il/elle"         , "il"              );
      $template->addProperty("Patient - Il/Elle"         , "Il"              );
      $template->addProperty("Patient - le/la"           , "le"              );
      $template->addProperty("Patient - Le/La"           , "Le"              );
      $template->addProperty("Patient - du/de la"        , "du"              );
      $template->addProperty("Patient - accord genre"    , ""                );
    } else {
      $template->addProperty("Patient - il/elle"         , "elle"            );
      $template->addProperty("Patient - Il/Elle"         , "Elle"            );
      $template->addProperty("Patient - le/la"           , "la"              );
      $template->addProperty("Patient - Le/La"           , "La"              );
      $template->addProperty("Patient - du/de la"        , "de la"           );
      $template->addProperty("Patient - accord genre"    , "e"               );
    }
    
    if ($this->medecin_traitant) {
    	$medecin = $this->_ref_medecin_traitant;
      $template->addProperty("Patient - m�decin traitant"          , "$medecin->nom $medecin->prenom");
      $template->addProperty("Patient - m�decin traitant - adresse", "$medecin->adresse \n $medecin->cp $medecin->ville");
    } else {
      $template->addProperty("Patient - m�decin traitant");
      $template->addProperty("Patient - m�decin traitant - adresse");
    }
    
    // Employeur
    $template->addProperty("Patient - employeur - nom"    , $this->employeur_nom);
    $template->addProperty("Patient - employeur - adresse", $this->employeur_adresse);
    $template->addProperty("Patient - employeur - cp"     , $this->employeur_cp);
    $template->addProperty("Patient - employeur - ville"  , $this->employeur_ville);
    $template->addProperty("Patient - employeur - tel"    , $this->getFormattedValue("employeur_tel"));
    $template->addProperty("Patient - employeur - urssaf" , $this->employeur_urssaf);
    
    // Prevenir
    $template->addProperty("Patient - pr�venir - nom"    , $this->prevenir_nom);
    $template->addProperty("Patient - pr�venir - pr�nom" , $this->prevenir_prenom);
    $template->addProperty("Patient - pr�venir - adresse", $this->prevenir_adresse);
    $template->addProperty("Patient - pr�venir - cp"     , $this->prevenir_cp);
    $template->addProperty("Patient - pr�venir - ville"  , $this->prevenir_ville);
    $template->addProperty("Patient - pr�venir - tel"    , $this->getFormattedValue("prevenir_tel"));
    $template->addProperty("Patient - pr�venir - parente", $this->prevenir_parente);
    
    // Confiance
    $template->addProperty("Patient - confiance - nom"    , $this->confiance_nom);
    $template->addProperty("Patient - confiance - pr�nom" , $this->confiance_prenom);
    $template->addProperty("Patient - confiance - adresse", $this->confiance_adresse);
    $template->addProperty("Patient - confiance - cp"     , $this->confiance_cp);
    $template->addProperty("Patient - confiance - ville"  , $this->confiance_ville);
    $template->addProperty("Patient - confiance - tel"    , $this->getFormattedValue("confiance_tel"));
    $template->addProperty("Patient - confiance - parente", $this->confiance_parente);
	
    // Vider les anciens holders
    for ($i = 1; $i < 4; $i++) {
      $template->addProperty("Patient - m�decin correspondant $i");
      $template->addProperty("Patient - m�decin correspondant $i - adresse");
      $template->addProperty("Patient - m�decin correspondant $i - sp�cialit�");
    }

    $this->loadRefsCorrespondants();
    $i = 0;
    $noms = array();
    foreach ($this->_ref_medecins_correspondants as $corresp) {
      $i++;
      $corresp->loadRefsFwd();
      $medecin = $corresp->_ref_medecin;
      $nom = "{$medecin->nom} {$medecin->prenom}";
      $noms[] = $nom;
      $template->addProperty("Patient - m�decin correspondant $i", $nom);
      $template->addProperty("Patient - m�decin correspondant $i - adresse", "{$medecin->adresse}\n{$medecin->cp} {$medecin->ville}");
      $template->addProperty("Patient - m�decin correspondant $i - sp�cialit�", htmlentities($medecin->disciplines));
    }
    
    $template->addProperty("Patient - m�decins correspondants", implode(" - ", $noms));

    //Liste des s�jours du patient
    $this->loadRefsSejours();
    
    if (is_array($this->_ref_sejours)){
    	foreach($this->_ref_sejours as $_sejour) {
    		$_sejour->loadRefPraticien();
    	}
      $smarty = new CSmartyDP("modules/dPpatients");
	    $smarty->assign("sejours", $this->_ref_sejours);
	    $sejours = $smarty->fetch("print_closed_sejours.tpl",'','',0);
	    $sejours = preg_replace('`([\\n\\r])`', '', $sejours); 
	   } else {
    	$sejours = CAppUI::tr("CSejour.none");
    }
    $template->addProperty("Patient - liste des s�jours", $sejours, '', false);
    
    $const_med = $this->_ref_constantes_medicales;
    
    // Version compl�te du tableau de constantes
    $csteByTime[0][$const_med->datetime] = array();
    $i = 0;
        
    foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
      if (count($csteByTime[$i][$const_med->datetime]) > 9) {
        $i++;
        $csteByTime[$i] = array();
        $csteByTime[$i][$const_med->datetime] = array();
      } 
      $csteByTime[$i][$const_med->datetime][$_constante] = $const_med->$_constante;
    }
    
    // Version minimale du tableau de constantes
    $conf_constantes = explode("|", CAppUI::conf("dPpatients CConstantesMedicales important_constantes"));
    $selection = array_flip($conf_constantes);

    $csteByTimeMin = array();
    $csteByTimeMin[0][$const_med->datetime] = array();
    $constantes_min = array();
    $i = 0;
    
    foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
      if (count($csteByTimeMin[$i][$const_med->datetime]) > 9) {
        $i++;
        $csteByTimeMin[$i] = array();
        $csteByTimeMin[$i][$const_med->datetime] = array();
      } 
      if (array_key_exists($_constante, $selection) || $const_med->$_constante != '') {
        $csteByTimeMin[$i][$const_med->datetime][$_constante] = $const_med->$_constante;
      }
    }

    $smarty = new CSmartyDP("modules/dPpatients");

    $smarty->assign("csteByTime", $csteByTime);
    $constantes_complet_horiz = $smarty->fetch("print_constantes.tpl",'','',0);
    $constantes_complet_horiz = preg_replace('`([\\n\\r])`', '', $constantes_complet_horiz); 
    
    $smarty->assign("csteByTime" , $csteByTimeMin);
    $constantes_minimal_horiz = $smarty->fetch("print_constantes.tpl",'','',0);
    $constantes_minimal_horiz = preg_replace('`([\\n\\r])`', '', $constantes_minimal_horiz);
    
    $smarty->assign("csteByTime", $csteByTime);
    $constantes_complet_vert  = $smarty->fetch("print_constantes_vert.tpl",'','',0);
    $constantes_complet_vert  = preg_replace('`([\\n\\r])`', '', $constantes_complet_vert);

    $smarty->assign("csteByTime" , $csteByTimeMin);
    $constantes_minimal_vert  = $smarty->fetch("print_constantes_vert.tpl",'','',0);
    $constantes_minimal_vert  = preg_replace('`([\\n\\r])`', '', $constantes_minimal_vert);
    
    $template->addProperty("Patient - Constantes - mode complet horizontal", $constantes_complet_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode minimal horizontal", $constantes_minimal_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode complet vertical"  , $constantes_complet_vert, '', false);
    $template->addProperty("Patient - Constantes - mode minimal vertical"  , $constantes_minimal_vert, '', false);
    $template->addProperty("Patient - poids",  "$const_med->poids kg");
    $template->addProperty("Patient - taille", "$const_med->taille cm");
    $template->addProperty("Patient - Pouls",  $const_med->pouls);
    $template->addProperty("Patient - IMC",    $const_med->_imc);
    $template->addProperty("Patient - VST",    $const_med->_vst);
    $template->addProperty("Patient - TA",     ($const_med->ta ? "$const_med->_ta_systole / $const_med->_ta_diastole" : ""));   
  }
  
  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);

    // Dossier m�dical
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->fillTemplate($template);
  }
  
  function getLabelTable() {
    return
        array ("[NOM]"        => $this->nom,
               "[PRENOM]"     => $this->prenom,
               "[SEXE]"       => $this->sexe,
               "[NOM JF]"     => $this->nom_jeune_fille,
               "[DATE NAISS]" => $this->naissance,
               "[NUM SECU]"   => $this->matricule);
  }
  
  function updateNomPaysInsee() {
  	$pays = new CPaysInsee();
  	if ($this->pays_naissance_insee) {
	  	$pays->numerique = $this->pays_naissance_insee;
	    $pays->loadMatchingObject();
	    $this->_pays_naissance_insee = $pays->nom_fr;
  	}
  	if ($this->assure_pays_naissance_insee) {
  		$pays->numerique = $this->assure_pays_naissance_insee;
      $pays->loadMatchingObject();
      $this->_assure_pays_naissance_insee = $pays->nom_fr;
  	}
  }
  
  function updatePatNumPaysInsee($nomPays) {
    $pays = new CPaysInsee();
    $pays->nom_fr = $this->_spec->ds->escape($nomPays);
    $pays->loadMatchingObject();
    return $pays->numerique;
  }
  
  function checkAnonymous() {
  	return $this->nom === "ANONYME" && $this->prenom === "Anonyme";
  }
  
  function toVcard(CMbvCardExport $vcard) {
    $vcard->addName($this->prenom, $this->nom, ucfirst($this->civilite));
    $vcard->addBirthDate($this->naissance);
    $vcard->addPhoneNumber($this->tel, 'HOME');
    $vcard->addPhoneNumber($this->tel2, 'CELL');
    $vcard->addPhoneNumber($this->tel_autre, 'WORK');
    $vcard->addEmail($this->email);
    $vcard->addAddress($this->adresse, $this->ville, $this->cp, $this->pays, 'HOME');
    $vcard->addTitle(ucfirst($this->profession));

    $this->loadRefPhotoIdentite();
    if ($this->_ref_photo_identite->_id) {
      $vcard->addPicture($this->_ref_photo_identite);
    }
  }
  
  function isIPPConflict($ipp) {
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return;
    }
    
    $idex = new CIdSante400();
    $idex->object_class= 'CPatient';
    $idex->tag = $tag_ipp;
    $idex->id400 = $ipp;
    $idex->loadMatchingObject();

    return $idex->_id;
  }
  
  function countMatchingPatients() {
    $ds = CSQLDataSource::get("std");

    $res = $ds->query("SELECT COUNT(*) AS total,
      CONVERT( GROUP_CONCAT(`patient_id` SEPARATOR '|') USING latin1 ) AS ids , 
      LOWER( CONCAT_WS( '-', 
        REPLACE( REPLACE( REPLACE( REPLACE( `nom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) , 
        REPLACE( REPLACE( REPLACE( REPLACE( `prenom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) , 
        `naissance`  
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `nom_jeune_fille` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_2` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_3` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_4` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
      )) AS hash
      FROM `patients`
      GROUP BY hash
      HAVING total > 1");
    
    return intval($ds->numRows($res));
  }
  
  function loadView() {
  	parent::loadView();
    $this->loadIPP();
  }
  
  function completeLabelFields() {
  	$this->loadIPP();
    return array("DATE NAISS"     => mbDateToLocale($this->naissance), "IPP"    => $this->_IPP,
                 "LIEU NAISSANCE" => $this->lieu_naissance,
                 "NOM"            => $this->nom,       "NOM JF" => $this->nom_jeune_fille,
                 "NUM SECU"       => $this->matricule, "PRENOM" => $this->prenom,
                 "SEXE"           => $this->sexe, "CIVILITE" => $this->civilite,
                 "CIVILITE LONGUE" => $this->_civilite_long, "ACCORD GENRE" => $this->sexe == "f" ? "e" : "");
  }
}

?>