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

/**
 * The CPatient Class
 */
class CPatient extends CPerson {
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

  static $fields_etiq = array(
    "DATE NAISS", "IPP", "LIEU NAISSANCE",
    "NOM", "NOM JF", "FORMULE NOM JF", "PRENOM", "SEXE",
    "CIVILITE", "CIVILITE LONGUE",
    "ACCORD GENRE", "CODE BARRE IPP", "ADRESSE", "MED. TRAITANT",
    "TEL", "TEL PORTABLE", "TEL ETRANGER", "PAYS",
    "PREVENIR - NOM", "PREVENIR - PRENOM", "PREVENIR - ADRESSE",
    "PREVENIR - TEL", "PREVENIR - PORTABLE", "PREVENIR - CP VILLE"
  );

  // DB Table key
  public $patient_id;

  // Owner
  public $function_id;

  // DB Fields
  public $nom;
  public $nom_jeune_fille;
  public $prenom;
  public $prenom_2;
  public $prenom_3;
  public $prenom_4;
  public $nom_soundex2;
  public $nomjf_soundex2;
  public $prenom_soundex2;
  public $naissance;
  public $deces;
  public $sexe;
  public $civilite;
  public $adresse;
  public $province;
  public $ville;
  public $cp;
  public $tel;
  public $tel2;
  public $tel_autre;
  public $email;
  public $vip;
  public $tutelle;
  public $situation_famille;
  public $is_smg; //titulaire de soins m�dicaux gratuits

  public $medecin_traitant_declare;
  public $medecin_traitant;
  public $incapable_majeur;
  public $ATNC;
  public $matricule;
  public $avs;

  public $code_regime;
  public $caisse_gest;
  public $centre_gest;
  public $code_gestion;
  public $centre_carte;
  public $regime_sante;
  public $rques;
  public $cmu;
  public $ald;
  public $code_exo;
  public $libelle_exo;
  public $notes_amo;
  public $notes_amc;
  public $deb_amo;
  public $fin_amo;
  public $code_sit;
  public $regime_am;
  public $mutuelle_types_contrat;

  public $rang_beneficiaire;
  public $qual_beneficiaire; // LogicMax, VitaleVision
  public $rang_naissance;
  public $fin_validite_vitale;

  public $pays;
  public $pays_insee;
  public $lieu_naissance;
  public $cp_naissance;
  public $pays_naissance_insee;
  public $profession;
  public $csp; // Cat�gorie socioprofessionnelle
  public $patient_link_id; // Patient link

  // Assur�
  public $assure_nom;
  public $assure_nom_jeune_fille;
  public $assure_prenom;
  public $assure_prenom_2;
  public $assure_prenom_3;
  public $assure_prenom_4;
  public $assure_naissance;
  public $assure_sexe;
  public $assure_civilite;
  public $assure_adresse;
  public $assure_ville;
  public $assure_cp;
  public $assure_tel;
  public $assure_tel2;
  public $assure_pays;
  public $assure_pays_insee;
  public $assure_cp_naissance;
  public $assure_lieu_naissance;
  public $assure_pays_naissance_insee;
  public $assure_profession;
  public $assure_rques;
  public $assure_matricule;

  // Other fields
  public $date_lecture_vitale;
  public $allow_sms_notification;
  public $_pays_naissance;
  public $_pays_naissance_insee;
  public $_assure_pays_naissance_insee;

  // Behaviour fields
  public $_anonyme;
  public $_generate_IPP = true;

  // Form fields
  public $_vip;
  public $_annees;
  public $_age;
  public $_age_assure;
  public $_civilite;
  public $_civilite_long;
  public $_assure_civilite;
  public $_assure_civilite_long;
  public $_longview;
  public $_art115;
  public $_type_exoneration;
  public $_exoneration;
  public $_can_see_photo;
  public $_csp_view;
  public $_nb_enfants;
  public $_overweight;
  public $_age_min;
  public $_age_max;
  public $_taille;
  public $_poids;

  // Vitale behaviour
  public $_bind_vitale;
  public $_update_vitale;
  public $_id_vitale;
  public $_vitale_lastname;
  public $_vitale_firstname;
  public $_vitale_birthdate;
  public $_vitale_nir_certifie;

  //ean (for switzerland)
  public $_assuranceCC_ean;
  public $_assureCC_id;
  public $_assuranceCC_id;

  // Navigation Fields
  public $_dossier_cabinet_url;

  // EAI Fields
  public $_eai_initiateur_group_id; // group initiateur du message EAI

  // HPRIM Fields
  public $_prenoms; // multiple
  public $_nom_naissance; // +/- = nom_jeune_fille
  public $_adresse_ligne2;
  public $_adresse_ligne3;
  public $_pays;
  public $_IPP;
  public $_fusion; // fusion

  // DMP
  public $_dmp_create;
  // Acc�s urgence
  public $_dmp_urgence_15;
  // Acc�s bris de glace
  public $_dmp_urgence_PS;
  public $_dmp_medecin_traitant;
  public $_dmp_access_authorization;

  /** @var  CMediusers */
  public $_dmp_mediuser;
  public $_dmp_vitale_nir_certifie;
  public $_dmp_vitale_nom_usuel;
  public $_dmp_vitale_nom_patronymique;
  public $_dmp_vitale_prenom_usuel;
  public $_dmp_vitale_date;

  /** @var CPatient */
  public $_patient_elimine; // fusion

  public $_nb_docs;
  public $_total_docs;

  /** @var CSejour[] */
  public $_ref_sejours;

  /** @var CConsultation[] */
  public $_ref_consultations;
  public $_ref_prescriptions;
  public $_ref_grossesses;
  public $_ref_last_grossesse;
  public $_ref_first_constantes;
  public $_ref_patient_links;

  /** @var CAffectation */
  public $_ref_curr_affectation;

  /** @var CAffectation */
  public $_ref_next_affectation;

  /** @var CMedecin */
  public $_ref_medecin_traitant;

  /** @var CCorrespondant[] */
  public $_ref_medecins_correspondants;

  /** @var CCorrespondantPatient[] */
  public $_ref_correspondants_patient;
  public $_ref_cp_by_relation;


  /** @var CFunctions */
  public $_ref_function;
  /** @var CDossierMedical */
  public $_ref_dossier_medical;
  /** @var CIdSante400 */
  public $_ref_IPP;
  /** @var CIdSante400 */
  public $_ref_vitale_idsante400;
  /** @var CConstantesMedicales */
  public $_ref_constantes_medicales;
  /** @var CDevenirDentaire[] */
  public $_refs_devenirs_dentaires;
  /** @var  CINSPatient[] */
  public $_refs_ins;
  /** @var  CINSPatient */
  public $_ref_last_ins;


  // Distant fields
  public $_ref_praticiens; // Praticiens ayant particip� � la pec du patient

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'patients';
    $spec->key   = 'patient_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["constantes"]            = "CConstantesMedicales patient_id";
    $backProps["contextes_constante"]   = "CConstantesMedicales context_id";
    $backProps["consultations"]         = "CConsultation patient_id";
    $backProps["correspondants"]        = "CCorrespondant patient_id";
    $backProps["correspondants_patient"] = "CCorrespondantPatient patient_id";
    $backProps["hprim21_patients"]      = "CHprim21Patient patient_id";
    $backProps["prescriptions_labo"]    = "CPrescriptionLabo patient_id";
    $backProps["product_deliveries"]    = "CProductDelivery patient_id";
    $backProps["sejours"]               = "CSejour patient_id";
    $backProps["dossier_medical"]       = "CDossierMedical object_id";
    $backProps["echanges_any"]          = "CExchangeAny object_id";
    $backProps["echanges_hprim"]        = "CEchangeHprim object_id";
    $backProps["echanges_hprim21"]      = "CEchangeHprim21 object_id";
    $backProps["echanges_hprimsante"]   = "CExchangeHprimSante object_id";
    $backProps["echanges_hl7v2"]        = "CExchangeHL7v2 object_id";
    $backProps["echanges_hl7v3"]        = "CExchangeHL7v3 object_id";
    $backProps["echanges_mvsante"]      = "CExchangeMVSante object_id";
    $backProps["devenirs_dentaires"]    = "CDevenirDentaire patient_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    $backProps["grossesses"]            = "CGrossesse parturiente_id";
    $backProps["facture_patient_consult"] = "CFactureCabinet patient_id";
    $backProps["facture_patient_sejour"]  = "CFactureEtablissement patient_id";
    // interfere avec CMbObject-back-observation_result_sets
    $backProps["patient_observation_result_sets"] = "CObservationResultSet patient_id";
    $backProps["patient_links"]         = "CPatient patient_link_id";
    $backProps["cv_pyxvital"]           = "CPvCV id_patient";
    $backProps["ins_patient"]           = "CINSPatient patient_id";
    $backProps["dmp_documents"]         = "CDMPDocument patient_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["function_id"]       = "ref class|CFunctions";
    $props["nom"]               = "str notNull confidential seekable|begin index";
    $props["prenom"]            = "str notNull seekable|begin index";
    $props["prenom_2"]          = "str";
    $props["prenom_3"]          = "str";
    $props["prenom_4"]          = "str";
    $props["nom_jeune_fille"]   = "str confidential seekable|begin index";
    $props["nom_soundex2"]      = "str index";
    $props["prenom_soundex2"]   = "str index";
    $props["nomjf_soundex2"]    = "str index";
    $props["medecin_traitant_declare"] = "bool";
    $props["medecin_traitant"]  = "ref class|CMedecin";
    $conf = CAppUI::conf("dPpatients CPatient check_code_insee");
    $props["matricule"]         = $conf ? "code insee confidential mask|9S99S99S9xS999S999S99" : "str maxLength|15";
    $props["code_regime"]       = "numchar length|2";
    $props["caisse_gest"]       = "numchar length|3";
    $props["centre_gest"]       = "numchar length|4";
    $props["code_gestion"]      = "str length|2";
    $props["centre_carte"]      = "numchar length|4";
    $props["regime_sante"]      = "str";
    $props["sexe"]              = "enum list|m|f";
    $props["civilite"]          = "enum list|m|mme|mlle|enf|dr|pr|me|vve";
    $props["adresse"]           = "text confidential";
    $props["province"]          = "str maxLength|40";
    $props["is_smg"]            = "bool default|0";
    $props["ville"]             = "str confidential seekable|begin";
    $props["cp"]                = "str minLength|4 maxLength|5 confidential";
    $props["tel"]               = "phone confidential";
    $props["tel2"]              = "phone confidential";
    $props["tel_autre"]         = "str maxLength|20";
    $props["email"]             = "str confidential";
    $props["vip"]               = "bool default|0";
    $props["situation_famille"] = "enum list|S|M|G|P|D|W|A";
    $props["tutelle"]           = "enum list|aucune|tutelle|curatelle default|aucune";
    $props["incapable_majeur"]  = "bool";
    $props["ATNC"]              = "bool";
    $props["avs"]               = "str maxLength|16";// mask|999.99.999.999";

    $conf = CAppUI::conf("dPpatients CPatient identitovigilence");
    $props["naissance"] = $conf === "date" || $conf === "doublons" ? "birthDate notNull" : "birthDate";

    $props["deces"]             = "date progressive";
    $props["rques"]             = "text";
    $props["cmu"]               = "bool";
    $props["ald"]               = "bool";
    $props["code_exo"]          = "enum list|0|4|5|9 default|0";
    $props["libelle_exo"]        = "text";
    $props["deb_amo"]           = "date";
    $props["fin_amo"]           = "date";
    $props["notes_amo"]         = "text";
    $props["notes_amc"]         = "text";
    $props["rang_beneficiaire"] = "enum list|01|02|09|11|12|13|14|15|16|31";
    $props["qual_beneficiaire"] = "enum list|0|1|2|3|4|5|6|7|8|9";
    $props["rang_naissance"]    = "enum list|1|2|3|4|5|6 default|1";
    $props["fin_validite_vitale"] = "date";
    $props["code_sit"]          = "numchar length|4";
    $props["regime_am"]         = "bool default|0";
    $props["mutuelle_types_contrat"] = "text";

    $props["pays"]                 = "str";
    $props["pays_insee"]           = "numchar length|3";
    $props["lieu_naissance"]       = "str";
    $props["cp_naissance"]         = "str minLength|4 maxLength|5 confidential";
    $props["pays_naissance_insee"] = "numchar length|3";
    $props["profession"]           = "str autocomplete";
    $props["csp" ]                 = "numchar length|2";
    $props["patient_link_id"]      = "ref class|CPatient";

    $props["assure_nom"]                  = "str confidential";
    $props["assure_prenom"]               = "str";
    $props["assure_prenom_2"]             = "str";
    $props["assure_prenom_3"]             = "str";
    $props["assure_prenom_4"]             = "str";
    $props["assure_nom_jeune_fille"]      = "str confidential";
    $props["assure_sexe"]                 = "enum list|m|f";
    $props["assure_civilite"]             = "enum list|m|mme|mlle|enf|dr|pr|me|vve";
    $props["assure_naissance"]            = "birthDate confidential mask|99/99/9999 format|$3-$2-$1";
    $props["assure_adresse"]              = "text confidential";
    $props["assure_ville"]                = "str confidential";
    $props["assure_cp"]                   = "str minLength|4 maxLength|5 confidential";
    $props["assure_tel"]                  = "phone confidential";
    $props["assure_tel2"]                 = "phone confidential";
    $props["assure_pays"]                 = "str";
    $props["assure_pays_insee"]           = "numchar length|3";
    $props["assure_lieu_naissance"]       = "str";
    $props["assure_cp_naissance"]         = "str minLength|4 maxLength|5 confidential";
    $props["assure_pays_naissance_insee"] = "numchar length|3";
    $props["assure_profession"]           = "str autocomplete";
    $props["assure_rques"]                = "text";
    $props["assure_matricule"]            = "code insee confidential mask|9S99S99S99S999S999S99";
    $props["date_lecture_vitale"]         = "dateTime";
    $props["allow_sms_notification"]      = "bool default|0";
    $props["_id_vitale"]                  = "num";
    $props["_pays_naissance_insee"]       = "str";
    $props["_assure_pays_naissance_insee"]= "str";
    $props["_art115"]                     = "bool";

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

    $props["_type_exoneration"] = "enum list|".implode("|", $types_exo);
    $props["_annees"]           = "num show|1";
    $props["_age"]              = "str";
    $props["_vip"]              = "bool";
    $props["_age_assure"]       = "num";
    $props["_poids"]            = "float show|1";
    $props["_taille"]           = "float show|1";

    $props["_age_min"]          = "num min|0";
    $props["_age_max"]          = "num min|0";

    $props["_assuranceCC_id"]   = "str length|5";
    $props["_assureCC_id"]      = "str maxLength|20";
    $props["_assuranceCC_ean"]  = "str";

    $props["_IPP"]              = "str show|1";

    // DMP
    $props["_dmp_create"]               = "bool";
    $props["_dmp_access_authorization"] = "bool default|1";
    $props["_dmp_medecin_traitant"]     = "bool";
    $props["_dmp_urgence_15"]           = "bool";
    $props["_dmp_urgence_PS"]           = "bool";
    //@todo � factoriser _dmp_vitale et _vitale
    $props["_dmp_vitale_nir_certifie"]     = "str confidential";
    $props["_dmp_vitale_nom_usuel"]        = "str";
    $props["_dmp_vitale_nom_patronymique"] = "str";
    $props["_dmp_vitale_prenom_usuel"]     = "str";
    $props["_dmp_vitale_date"]             = "str confidential";
    $props["_vitale_lastname"]             = "str";
    $props["_vitale_firstname"]             = "str";
    $props["_vitale_birthdate"]             = "str confidential";
    $props["_vitale_nir_certifie"]          = "str confidential";

    return $props;
  }

  /**
   * V�rification de la possibilit� de merger
   * une liste de patients
   *
   * @param CPatient[] $patients Liste des patients � merger
   *
   * @return string
   */
  function checkMerge($patients = array()) {
    if ($msg = parent::checkMerge($patients)) {
      return $msg;
    }

    $sejour = new CSejour();
    $where["patient_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($patients, "_id"));
    /** @var CSejour[] $sejours */
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
    return null;
  }

  /**
   * Fusion de patients
   *
   * @param CPatient[] $objects Liste des patientsx
   * @param bool       $fast    Mode rapide
   *
   * @return string|null
   */
  function merge($objects = array(), $fast = false) {
    // Load the matching CDossierMedical objects
    if ($this->_id) {
      $merged_objects = array_merge($objects, array($this));
    }
    else {
      $merged_objects = $objects;
    }

    $where = array(
      'object_class' => "='$this->_class'",
      'object_id'    => CSQLDataSource::prepareIn(CMbArray::pluck($merged_objects, 'patient_id'))
    );
    $dossier_medical = new CDossierMedical();
    $list = $dossier_medical->loadList($where);

    foreach ($objects as $object) {
      $object->loadIPP();
    }

    if ($msg = parent::merge($objects, $fast)) {
      return $msg;
    }

    // Merge them
    if (count($list) > 1) {
      $dossier_medical->mergePlainFields($list);
      $dossier_medical->object_class = $this->_class;
      $dossier_medical->object_id = $this->_id;
      return $dossier_medical->merge($list);
    }
    return null;
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Standard check
    if ($msg = parent::check()) {
      return $msg;
    }

    // Creation d'un patient
    if (!$this->_merging && CAppUI::conf('dPpatients CPatient identitovigilence') == "doublons") {
      if ($this->loadMatchingPatient(true, false) > 0) {
        return "Doublons d�tect�s";
      }
    }
    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {

    $this->completeField("patient_link_id");
    if ($this->_id && $this->_id == $this->patient_link_id) {
      $this->patient_link_id = "";
    }

    // Cr�ation d'un patient en mode cabinets distincts
    if (CAppUI::conf('dPpatients CPatient function_distinct') && !$this->_id) {
      $this->function_id = CMediusers::get()->function_id;
    }

    if ($this->fieldModified("naissance") || $this->fieldModified("sexe")) {
      // _guid is not valued yet !!
      DSHM::remKeys("alertes-*-CPatient-".$this->_id);
    }

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
    if (CModule::getActive("fse")) {
      $cv = CFseFactory::createCV();
      if ($cv) {
        if ($msg = $cv->bindVitale($this)) {
          return $msg;
        }
      }
    }

    // G�n�ration de l'IPP ?
    if ($this->_generate_IPP) {
      if ($msg = $this->generateIPP()) {
        return $msg;
      }
    }

    if ($this->_vitale_nir_certifie) {
      if ($msg = CInscTools::createINSC($this)) {
        return $msg;
      }
    }

    return null;
  }

  /**
   * G�n�ration de l'IPP du patient
   *
   * @return null|string
   */
  function generateIPP() {
    $group = CGroups::loadCurrent();
    if (!$group->isIPPSupplier()) {
      return null;
    }

    $this->loadIPP($group->_id);
    if ($this->_IPP) {
      return null;
    }

    if (!$IPP = CIncrementer::generateIdex($this, self::getTagIPP($group->_id), $group->_id)) {
      return CAppUI::tr("CIncrementer_undefined");
    }
    return null;
  }

  /**
   * Evaluation des libell�s d'exon�ration
   *
   * @return void
   */
  function guessExoneration(){
    $this->completeField("libelle_exo");

    if (!$this->libelle_exo) {
      return;
    }

    foreach (self::$libelle_exo_guess as $field => $values) {
      if ($this->$field !== null) {
        continue;
      }

      foreach ($values as $value => $rules) {
        foreach ($rules as $rule) {
          if (preg_match("/$rule/i", $this->libelle_exo)) {
            $this->$field = $value;
            break;
          }
        }
      }
    }
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    // Noms
    $this->nom = self::applyModeIdentitoVigilance($this->nom);
    $this->nom_jeune_fille = self::applyModeIdentitoVigilance($this->nom_jeune_fille);
    $this->prenom = self::applyModeIdentitoVigilance($this->prenom, true);

    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom;
    $this->_prenoms = array($this->prenom, $this->prenom_2, $this->prenom_3, $this->prenom_4);

    if ($this->libelle_exo) {
      $this->_art115 = preg_match("/pension militaire/i", $this->libelle_exo);
    }

    $relative = CMbDate::relative($this->naissance);
    if ($this->deces && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->deces) &&
        !preg_match('/^0000-[0-9]{2}-[0-9]{2}$/', $this->deces) &&
        !preg_match('/^[0-9]{4}-00-[0-9]{2}$/', $this->deces) &&
        !preg_match('/^[0-9]{4}-[0-9]{2}-00$/', $this->deces)) {
      $relative = CMbDate::relative($this->naissance, $this->deces);
    }

    if ($relative["count"] < 0) {
      $relative["count"] = 0;
    }

    $this->evalAge();

    $str = $relative["unit"] . ($relative["count"] > 1 ? "s" : "") .($relative["unit"] == "year" ? "_old" : "");
    $this->_age = $relative["count"] . " " . CAppUI::tr($str);

    $this->checkVIP();

    $this->_civilite = CAppUI::tr("CPatient.civilite.$this->civilite");
    if ($this->civilite === "enf") {
      $this->_civilite_long = CAppUI::tr("CPatient.civilite.".($this->sexe === "m" ? "le_jeune" : "la_jeune"));
    }
    else {
      $this->_civilite_long = CAppUI::tr("CPatient.civilite.$this->civilite-long");
    }

    $this->_assure_civilite = CAppUI::tr("CPatient.civilite.$this->assure_civilite");
    if ($this->assure_civilite === "enf") {
      $this->_assure_civilite_long = CAppUI::tr("CPatient.civilite.".($this->assure_sexe === "m" ? "le_jeune" : "la_jeune"));
    }
    else {
      $this->_assure_civilite_long = CAppUI::tr("CPatient.civilite.$this->assure_civilite-long");
    }

    $nom_naissance   = $this->nom_jeune_fille && $this->nom_jeune_fille != $this->nom ? " ($this->nom_jeune_fille)" : "";
    $this->_view     = "$this->_civilite $this->nom$nom_naissance $this->prenom";
    $this->_longview = "$this->_civilite_long $this->nom$nom_naissance $this->prenom";
    $this->_view .= $this->vip ? " [Conf.]" : "";
    $this->_view .= $this->deces ? " [D�c�s.]" : "";
    $this->_longview .= $this->vip ? " [Conf.]" : "";
    $this->_longview .= $this->deces ? " [D�c�s.]" : "";

    // Navigation fields
    //$this->_dossier_cabinet_url = self::$dossier_cabinet_prefix[CAppUI::pref("DossierCabinet")] . $this->_id;
    $this->_dossier_cabinet_url = self::$dossier_cabinet_prefix["dPpatients"] . $this->_id;

    if ($this->pays_insee && !$this->pays) {
      $this->pays = $this->updatePatNomPays($this->pays_insee);
    }

    if ($this->csp) {
      $this->_csp_view = $this->getCSPName();
    }

    $this->mapPerson();

  }

  /**
   * Calcul l'�ge du patient en ann�es
   *
   * @param string $date Date de r�f�rence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en ann�es
   */
  function evalAge($date = null) {
    $achieved = CMbDate::achievedDurations($this->naissance, $date);
    return $this->_annees = $achieved["year"];
  }

  /**
   * Calcul l'aspect confidentiel du patient
   *
   * @return bool on a acc�s ou pas
   */
  function checkVIP() {
    if ($this->_vip !== null) {
      return;
    }

    $this->_vip = false;
    $user = CMediusers::get();

    if ($this->vip&& !CModule::getCanDo("dPpatient")->admin()) {

      // Test si le praticien est pr�sent dans son dossier

      $praticiens = $this->loadRefsPraticiens();
      $user_in_list_prat = array_key_exists($user->_id, $praticiens);

      // Test si un l'utilisateur est pr�sent dans les logs
      $user_in_logs      = false;
      $this->loadLogs();

      foreach ($this->_ref_logs as $_log) {
        if ($user->_id == $_log->user_id) {
          $user_in_logs = true;
          break;
        }
      }
      $this->_vip = !$user_in_list_prat && !$user_in_logs;
    }
    if ($this->_vip) {
      CValue::setSession("patient_id", 0);
    }
  }

  /**
   * Calcul l'�ge de l'assur� en ann�es
   *
   * @param string $date Date de r�f�rence pour le calcul, maintenant si null
   *
   * @return int l'age de l'assur� en ann�es
   */
  function evalAgeAssure($date = null) {
    $achieved = CMbDate::achievedDurations($this->assure_naissance, $date);
    return $this->_age_assure = $achieved["year"];
  }

  /**
   * Calcul l'�ge du patient en mois
   *
   * @param string $date Date de r�f�rence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en mois
   */
  function evalAgeMois($date = null){
    $achieved = CMbDate::achievedDurations($this->naissance, $date);
    return $achieved["month"];
  }

  /**
   * Calcul l'�ge du patient en semaines
   *
   * @param string $date Date de r�f�rence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en semaines
   */
  function evalAgeSemaines($date = null){
    $jours = $this->evalAgeJours($date);
    return intval($jours/7);
  }

  /**
   * Calcul l'�ge du patient en jours
   *
   * @param string $date Date de r�f�rence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en jours
   */
  function evalAgeJours($date = null){
    $date = $date ? $date : CMbDT::date();
    if (!$this->naissance || $this->naissance === "0000-00-00") {
      return 0;
    }
    return CMbDT::daysRelative($this->naissance, $date);
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();

    $soundex2 = new soundex2;
    if ($this->nom) {
      $this->nom = self::applyModeIdentitoVigilance($this->nom);
      $this->nom_soundex2 = $soundex2->build($this->nom);
    }

    if ($this->nom_jeune_fille) {
      $this->nom_jeune_fille = self::applyModeIdentitoVigilance($this->nom_jeune_fille);
      $this->nomjf_soundex2 = $soundex2->build($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = self::applyModeIdentitoVigilance($this->prenom, true);
      $this->prenom_soundex2 = $soundex2->build($this->prenom);
    }

    if ($this->cp === "00000") {
      $this->cp = "";
    }

    if ($this->assure_nom) {
      $this->assure_nom = self::applyModeIdentitoVigilance($this->assure_nom);
    }

    if ($this->assure_nom_jeune_fille) {
      $this->assure_nom_jeune_fille = self::applyModeIdentitoVigilance($this->assure_nom_jeune_fille);
    }

    if ($this->assure_prenom) {
      $this->assure_prenom = self::applyModeIdentitoVigilance($this->assure_prenom, true);
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
      $this->naissance = CMbDT::dateFromLocale($this->naissance);
      $this->evalAge();
      $this->civilite = ($this->_annees < CAppUI::conf("dPpatients CPatient adult_age")) ?
        "enf" : (($this->sexe === "m") ? "m" : "mme");
    }

    // D�termine la civilit� de l'assure automatiquement (utile en cas d'import)
    $this->completeField("assure_civilite");
    if ($this->assure_civilite === "guess" || !$this->assure_civilite) {
      $this->assure_naissance = CMbDT::dateFromLocale($this->assure_naissance);
      $this->evalAgeAssure();
      $sexe = $this->assure_sexe ? $this->assure_sexe : $this->sexe;
      $this->assure_civilite = ($this->_age_assure < CAppUI::conf("dPpatients CPatient adult_age")) ?
        "enf" : (($sexe === "m") ? "m" : "mme");
    }
  }

  /**
   * Chargement de la fonction reli�e
   *
   * @return CFunctions
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * Charge les s�jours du patient
   *
   * @param array $where SQL where clauses
   *
   * @return CSejour[]
   */
  function loadRefsSejours($where = array()) {
    if (!$this->_id) {
      return $this->_ref_sejours = array();
    }

    $sejour = new CSejour();
    $group_id = CGroups::loadCurrent()->_id;

    $where["patient_id"] = "= '$this->_id'";
    if (CAppUI::conf("dPpatients CPatient multi_group") == "hidden") {
      $where["sejour.group_id"] = "= '$group_id'";
    }
    $order = "entree DESC";

    return $this->_ref_sejours = $sejour->loadList($where, $order);
  }

  /**
   * Chargement du s�jour courant du patient
   *
   * @param string $dateTime Date de r�f�rence, maintenant si null
   * @param String $group_id group_id, groupe courant si null
   *
   * @return CSejour[]
   */
  function getCurrSejour($dateTime = null, $group_id = null) {
    if (!$dateTime) {
      $dateTime = CMbDT::dateTime();
    }
    if (!$group_id) {
      $group_id = CGroups::loadCurrent()->_id;
    }

    $where["group_id"] = "= '$group_id'";
    $where[] = "'$dateTime' BETWEEN entree AND sortie";
    return $this->loadRefsSejours($where);
  }

  /**
   * Get patient links
   *
   * @return CPatient[]
   */
  function loadPatientLinks() {
    if ($this->patient_link_id) {
      return $this->_ref_patient_links = array($this->loadFwdRef("patient_link_id"));
    }
    return null;
    // return $this->_ref_patient_links = $this->loadBackRefs("patient_links");
  }

  /**
   * Get the next sejour from today or from a given date
   *
   * @param string $date          Date de d�but de recherche
   * @param bool   $withOperation Avec rechreche des interventions
   * @param int    $consult_id    Identifiant de la consultation de r�f�rence
   *
   * @return array;
   */
  function getNextSejourAndOperation($date = null, $withOperation = true, $consult_id = null) {
    $sejour = new CSejour();
    $op     = new COperation();
    if (!$date) {
      $date = CMbDT::date();
    }
    if (!$this->_ref_sejours) {
      $this->loadRefsSejours();
    }
    foreach ($this->_ref_sejours as $_sejour) {
      // Conditions d'exlusion du s�jour
      if (!in_array($_sejour->type, array("ambu", "comp", "exte")) || $_sejour->annule || $_sejour->entree_prevue < $date) {
        continue;
      }
      if (!$sejour->_id) {
        $sejour = $_sejour;
      }
      elseif ($_sejour->entree_prevue < $sejour->entree_prevue) {
        $sejour = $_sejour;
      }

      if (!$withOperation) {
        continue;
      }
      if (!$_sejour->_ref_operations) {
        $_sejour->loadRefsOperations(array("annulee" => "= '0'"));
      }
      foreach ($_sejour->_ref_operations as $_op) {
        $consult_anesth = $_op->loadRefsConsultAnesth();
        if ($consult_id && $consult_anesth->consultation_id == $consult_id) {
          continue;
        }
        $_op->loadRefPlageOp();
        if (!$op->_id) {
          $op = $_op;
        }
        elseif ($_op->_datetime < $op->_datetime) {
          $op = $_op;
        }
      }
    }

    $sejour->loadRefPraticien()->loadRefFunction();
    $op->loadRefPraticien()->loadRefFunction();

    return array("CSejour" => $sejour, "COperation" => $op);
  }

  /**
   * Get an associative array of uncancelled sejours and their dates
   *
   * @return array Sejour ID => array("entree_prevue" => DATE, "sortie_prevue" => DATE)
   */
  function getSejoursCollisions() {
    $sejours_collision = array();
    $group_id = CGroups::loadCurrent()->_id;

    if ($this->_ref_sejours) {
      foreach ($this->_ref_sejours as $_sejour) {
        if (!$_sejour->annule && $_sejour->group_id == $group_id && !in_array($_sejour->type, array("urg", "seances", "consult"))) {
          $sejours_collision[$_sejour->_id] = array (
            "entree" => CMbDT::date($_sejour->entree),
            "sortie" => CMbDT::date($_sejour->sortie)
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
   *
   * @param bool $other      V�rifier qu'on n'inclut pas $this
   * @param bool $loadObject Permet de ne pas charger le patient, seulement renvoyer le nombre de matches
   *
   * @return int Nombre d'occurences trouv�es
   */
  function loadMatchingPatient($other = false, $loadObject = true) {
    $ds = $this->_spec->ds;

    if (CAppUI::conf('dPpatients CPatient function_distinct')) {
      $function_id = CMediusers::get()->function_id;
      $where["function_id"] = "= '$function_id'";
    }

    if ($other && $this->_id) {
      $where["patient_id"] = " != '$this->_id'";
    }

    // if no birthdate, sql request too strong
    if (!$this->naissance) {
      return null;
    }

    $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom);
    $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom);

    if ($this->nom_jeune_fille) {
      $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom_jeune_fille);
      $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom_jeune_fille);
    }

    $where[] = implode(" OR ", $whereOr);
    $where["prenom"]          = $ds->prepareLikeName($this->prenom);

    if ($this->prenom_2) {
      $where["prenom_2"] = $ds->prepareLikeName($this->prenom_2);
    }
    if ($this->prenom_3) {
      $where["prenom_3"] = $ds->prepareLikeName($this->prenom_3);
    }
    if ($this->prenom_4) {
      $where["prenom_4"] = $ds->prepareLikeName($this->prenom_4);
    }

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
   *
   * @return CPatient[] Array of siblings
   */
  function getSiblings() {
    $ds =& $this->_spec->ds;

    $where = array (
      "nom"    => $ds->prepareLikeName($this->nom),
      "prenom" => $ds->prepareLikeName($this->prenom),
      "patient_id" => "!= '$this->_id'",
    );

    if (CAppUI::conf('dPpatients CPatient function_distinct')) {
      $function_id = CMediusers::get()->function_id;
      $where["function_id"] = "= '$function_id'";
    }

    $siblings = $this->loadList($where);

    if ($this->naissance !== "0000-00-00") {
      $where = array (
        "nom"       => $ds->prepareLikeName($this->nom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      if (CAppUI::conf('dPpatients CPatient function_distinct')) {
        $function_id = CMediusers::get()->function_id;
        $where["function_id"] = "= '$function_id'";
      }
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where, null, null, "patients.patient_id"));

      $where = array (
        "prenom"    => $ds->prepareLikeName($this->prenom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      if (CAppUI::conf('dPpatients CPatient function_distinct')) {
        $function_id = CMediusers::get()->function_id;
        $where["function_id"] = "= '$function_id'";
      }
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where, null, null, "patients.patient_id"));
    }

    return $siblings;
  }

  /**
   * Find patient phoning similar
   *
   * @param string $date restrict to a venue collide date
   *
   * @return CPatient[] Array of phoning patients
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
      $min = CMbDT::dateTime("-1 DAY", $date);
      $max = CMbDT::dateTime("+1 DAY", $date);
      // Ne pas utiliser de OR entree_prevue / entree_reelle ici: probl�mes de performance
      $where["sejour.entree"] = "BETWEEN '$min' AND '$max'";
    }

    if (CAppUI::conf('dPpatients CPatient function_distinct')) {
      $function_id = CMediusers::get()->function_id;
      $where["function_id"] = "= '$function_id'";
    }

    return $this->loadList($where, null, null, "patients.patient_id", $join);
  }


  /**
   * V�rification de la similarit� du patient avec un autre nom / pr�nom
   *
   * @param string $nom    Nom � tester
   * @param string $prenom Pr�nom � taster
   * @param bool   $strict Test strict
   *
   * @return bool
   */
  function checkSimilar($nom, $prenom, $strict = true) {
    $soundex2 = new soundex2;

    $testNom    = CMbString::lower($this->nom_soundex2)    == CMbString::lower($soundex2->build($nom));
    $testPrenom = CMbString::lower($this->prenom_soundex2) == CMbString::lower($soundex2->build($prenom));

    if ($strict) {
      return($testNom && $testPrenom);
    }
    else {
      return($testNom || $testPrenom);
    }
  }

  /**
   * Charge les consultations du patient
   *
   * @param array|null $where [optional] Clauses SQL
   *
   * @return CConsultation[]
   */
  function loadRefsConsultations($where = null) {
    $consultation = new CConsultation();
    $group_id = CGroups::loadCurrent()->_id;
    $curr_user = CAppUI::$user;
    if ($this->_id) {
      if ($where === null) {
        $where = array();
      }
      if (!$curr_user->isAdmin()) {
        $where[] = "functions_mediboard.consults_partagees = '1' ||
                    (functions_mediboard.consults_partagees = '0' && functions_mediboard.function_id = '$curr_user->function_id')";
      }
      $where["patient_id"] = "= '$this->_id'";
      if (CAppUI::conf("dPpatients CPatient multi_group") == "hidden") {
        $where["functions_mediboard.group_id"] = "= '$group_id'";
      }
      $order = "plageconsult.date DESC";
      $leftjoin = array();
      $leftjoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
      $leftjoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
      $leftjoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
      return $this->_ref_consultations = $consultation->loadList($where, $order, null, null, $leftjoin);
    }

    return $this->_ref_consultations = array();
  }

  /**
   * Chargement du dossier m�dical
   *
   * @param bool $load_refs_back Avec chargement des backrefs
   *
   * @return CDossierMedical
   */
  function loadRefDossierMedical($load_refs_back = true) {
    $this->_ref_dossier_medical = $this->loadUniqueBackRef("dossier_medical");

    if ($load_refs_back) {
      $this->_ref_dossier_medical->loadRefsBack();
    }

    return $this->_ref_dossier_medical;
  }

  /**
   * Chargement des devenirs dentaires du patient
   *
   * @return CDevenirDentaire[]
   */
  function loadRefsDevenirDentaire() {
    return $this->_refs_devenirs_dentaires = $this->loadBackRefs("devenirs_dentaires");
  }

  /**
   * Chargement des affectations courantes et � venir du patient
   *
   * @param date $date Date de r�f�rence
   *
   * @return void
   */
  function loadRefsAffectations($date = null) {
    $affectation = new CAffectation();

    // Affectations inactives
    if (!$affectation->_ref_module) {
      $this->_ref_curr_affectation = null;
      $this->_ref_next_affectation = null;
    }

    if (!$date) {
      $date = CMbDT::dateTime();
    }

    $sejours = $this->loadRefsSejours();
    $group = CGroups::loadCurrent();

    // Affectation actuelle et prochaine affectation
    $where["affectation.sejour_id"] = CSQLDataSource::prepareIn(array_keys($sejours));
    $where["sejour.group_id"]       = "= '$group->_id'";
    $order = "affectation.entree";

    // @FIXME A quoi sert cette jointure ?
    $ljoin["sejour"]                = "sejour.sejour_id = affectation.sejour_id";

    // Affection courante
    $this->_ref_curr_affectation = new CAffectation();
    $where["affectation.entree"] = "<  '$date'";
    $where["affectation.sortie"] = ">= '$date'";
    $this->_ref_curr_affectation->loadObject($where, $order, null, $ljoin);

    // Prochaine affectations
    $this->_ref_next_affectation = new CAffectation();
    $where["affectation.entree"] = "> '$date'";
    $this->_ref_next_affectation->loadObject($where, $order, null, $ljoin);
  }

  function loadRefsDocs() {
    $docs_valid = parent::loadRefsDocs();
    if ($docs_valid) {
      $this->_nb_docs .= "$docs_valid";
    }
    return $docs_valid;
  }

  function loadRefsPrescriptions($perm = null) {
    if (CModule::getInstalled("dPlabo")) {
      $prescription = new CPrescriptionLabo();
      $where = array("patient_id" => "= '$this->_id'");
      $order = "date DESC";
      $this->_ref_prescriptions = $prescription->loadListWithPerms($perm, $where, $order);
    }
  }

  /**
   * Load the CConstantesMedicales of the patient
   *
   * @param string    $datetime  The reference datetime
   * @param array     $selection A selection of constantes to load
   * @param CMbObject $context   A particular context
   * @param boolean   $use_cache Force the function to return the latest_values is already set
   *
   * @return array
   */
  function loadRefConstantesMedicales($datetime = null, $selection = array(), $context = null, $use_cache = true) {
    $latest = CConstantesMedicales::getLatestFor($this, $datetime, $selection, $context, $use_cache);

    list($this->_ref_constantes_medicales, /*$dates*/) = $latest;
    $this->_ref_constantes_medicales->updateFormFields();

    return $latest;
  }

  function loadRefsGrossesses($order = "terme_prevu DESC") {
    return $this->_ref_grossesses = $this->loadBackRefs("grossesses", $order);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsCorrespondants();
    $this->loadRefsAffectations();
    $this->loadRefsPrescriptions();
    $this->loadRefsGrossesses();
  }

  function loadIdVitale() {
    if (CModule::getActive("fse")) {
      $cv = CFseFactory::createCV();
      if ($cv) {
        $cv->loadIdVitale($this);
      }
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefFunction();
    $this->loadIdVitale();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete(){
    parent::loadComplete();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefsCorrespondantsPatient();
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->canDo();
    $this->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_dossier_medical->loadRefsTraitements();
    $prescription = $this->_ref_dossier_medical->loadRefPrescription();

    if ($prescription && is_array($prescription->_ref_prescription_lines)) {
      foreach ($prescription->_ref_prescription_lines as $_line) {
        $_line->loadRefsPrises();
      }
    }

    $this->loadRefConstantesMedicales(null, array("poids", "taille"));
    $const_med = $this->_ref_constantes_medicales;

    if ($const_med) {
      $this->_poids  = $const_med->poids;
      $this->_taille = $const_med->taille;
    }
  }

  function loadRefsPraticiens() {
    $this->_ref_praticiens = array();

    // Consultations
    $this->loadRefsConsultations();
    foreach ($this->_ref_consultations as $_consult) {
      $praticien = $_consult->loadRefPraticien();
      $praticien->loadRefFunction()->loadRefGroup();
      $this->_ref_praticiens[$praticien->_id] = $praticien;
    }
    // S�jours
    $this->loadRefsSejours();
    foreach ($this->_ref_sejours as $_sejour) {
      $praticien = $_sejour->loadRefPraticien();
      $praticien->loadRefFunction()->loadRefGroup();
      $this->_ref_praticiens[$praticien->_id] = $praticien;
      $_sejour->loadRefsOperations();
      foreach ($_sejour->_ref_operations as $_operation) {
        $praticien = $_operation->loadRefPraticien();
        $praticien->loadRefFunction()->loadRefGroup();
        $this->_ref_praticiens[$praticien->_id] = $praticien;
      }
    }
    return $this->_ref_praticiens;
  }

  function loadDossierComplet($permType = null) {
    $this->_total_docs = 0;
    $this->_ref_praticiens = array();

    if (!$this->_id) {
      return;
    }

    // Patient permission
    $this->canDo();

    // Doc items
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->_total_docs += $this->countDocItems($permType);

    // Photos et Notes
    $this->loadRefPhotoIdentite();
    $this->loadRefsNotes();

    // Correspondants
    $this->loadRefsCorrespondants();
    $this->loadRefsCorrespondantsPatient();

    // Affectations courantes
    $this->loadRefsAffectations();
    $affectation = $this->_ref_curr_affectation;
    if ($affectation && $affectation->_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }

    $affectation = $this->_ref_next_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }

    $maternite_active = CModule::getActive("maternite");
    if ($maternite_active) {
      $this->loadRefsGrossesses();
    }

    // Consultations
    $this->loadRefsConsultations();
    foreach ($this->_ref_consultations as $consult) {
      if ($consult->sejour_id) {
        unset($this->_ref_consultations[$consult->_id]);
        continue;
      }

      $consult->loadRefConsultAnesth();
      $consult->loadRefsFichesExamen();
      $consult->loadRefsExamsComp();
      if (!count($consult->_refs_dossiers_anesth)) {
        $this->_total_docs += $consult->countDocItems($permType);
      }

      // Praticien
      $consult->getType();
      $praticien = $consult->_ref_praticien;

      $this->_ref_praticiens[$praticien->_id] = $praticien;
      $praticien->loadRefFunction()->loadRefGroup();

      foreach ($consult->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->_ref_consultation = $consult;
        $this->_total_docs += $_dossier_anesth->countDocItems($permType);
      }

      // Grossesse
      if ($maternite_active && $consult->grossesse_id) {
        $result = ceil((CMbDT::daysRelative($this->_ref_grossesses[$consult->grossesse_id]->_date_fecondation, $consult->_date))/7);
        $consult->_semaine_grossesse = $result;
        $this->_ref_grossesses[$consult->grossesse_id]->_ref_consultations[$consult->_id] = $consult;
      }

      // Permission
      $consult->canDo();
    }

    // Sejours
    foreach ($this->_ref_sejours as $_sejour) {
      // Permission
      $_sejour->canDo();

      //
      $_sejour->loadNDA();
      $_sejour->loadRefsAffectations();

      // Praticien
      $praticien = $_sejour->loadRefPraticien(1);
      $this->_ref_praticiens[$praticien->_id] = $praticien;

      $_sejour->countDocItems($permType);
      if ($maternite_active && $_sejour->grossesse_id) {
        $this->_ref_grossesses[$_sejour->grossesse_id]->_ref_sejours[$_sejour->_id] = $_sejour;
      }

      $_sejour->loadRefsOperations();
      foreach ($_sejour->_ref_operations as $_operation) {
        $_operation->canDo();

        // Praticien
        $praticien = $_operation->loadRefPraticien(1);
        $praticien->loadRefFunction();
        $this->_ref_praticiens[$praticien->_id] = $praticien;

        // Autres
        $_operation->loadRefPlageOp(1);
        $this->_total_docs += $_operation->countDocItems($permType);

        // Consultation d'anesth�sie
        $consult_anesth = $_operation->loadRefsConsultAnesth();
        $this->_total_docs += $consult_anesth->countDocItems($permType);

        $consultation = $consult_anesth->loadRefConsultation();
        $this->_total_docs += $consultation->countDocItems($permType);
        $consultation->canRead();
        $consultation->canEdit();
      }

      // RPU
      $rpu = $_sejour->loadRefRPU();
      if ($rpu && $rpu->_id) {
        $this->_total_docs += $rpu->countDocItems($permType);
      }

      $_sejour->loadRefsConsultations();
      foreach ($_sejour->_ref_consultations as $_consult) {
        $_consult->loadRefConsultAnesth();
        $_consult->loadRefsFichesExamen();
        $_consult->loadRefsExamsComp();
        $this->_total_docs += $_consult->countDocItems($permType);

        $_consult->loadRefsFwd(1);
        $_consult->_ref_sejour = $_sejour;
        $_consult->getType();
        $_consult->_ref_chir->loadRefFunction();
        $_consult->_ref_chir->_ref_function->loadRefGroup();
        $_consult->canDo();
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

  // Pr�venir - Confiance - Employeur
  function loadRefsCorrespondantsPatient() {
    $this->_ref_correspondants_patient = $this->loadBackRefs("correspondants_patient");

    $correspondant = new CCorrespondantPatient();
    $this->_ref_cp_by_relation = array();
    foreach (explode("|", $correspondant->_specs["relation"]->list) as $_relation) {
      $this->_ref_cp_by_relation[$_relation] = array();
    }

    foreach ($this->_ref_correspondants_patient as $_correspondant) {
      $this->_ref_cp_by_relation[$_correspondant->relation][$_correspondant->_id] = $_correspondant;
    }

    return $this->_ref_correspondants_patient;
  }

  /**
   * Load the INS of the patient
   *
   * @return CINSPatient[]|null
   */
  function loadRefsINS() {
    return $this->_refs_ins = $this->loadBackRefs("ins_patient", "date DESC");
  }

  /**
   * Load the last INS of the patient
   *
   * @return CINSPatient|null
   */
  function loadLastINS() {
    $ins = null;
    $array = $this->loadBackRefs("ins_patient", "date DESC", 1);
    if ($array) {
      $ins = current($array);
    }
    return $this->_ref_last_ins = $ins;
  }

  /**
   * Construit le tag IPP en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger l'IPP pour un �tablissement donn� si non null
   *
   * @return string|null
   */
  static function getTagIPP($group_id = null) {
    $context = array(__METHOD__, func_get_args());
    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }

    // Gestion du tag IPP par son domaine d'identification
    if (CAppUI::conf("eai use_domain")) {
      return CFunctionCache::set($context, CDomain::getMasterDomain("CPatient", $group_id)->tag);
    }

    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return CFunctionCache::set($context, null);
    }

    // Permettre des IPP en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    // Si on est dans le cas d'un �tablissement g�rant la num�rotation
    $group->loadConfigValues();
    if ($group->_configs["sip_idex_generator"]) {
      $tag_ipp = CAppUI::conf("sip tag_ipp");
    }

    // Pr�f�rer un identifiant externe de l'�tablissement
    if ($tag_group_idex = CAppUI::conf("dPpatients CPatient tag_ipp_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }

    return CFunctionCache::set($context, str_replace('$g', $group_id, $tag_ipp));
  }

  /**
   * Charge l'IPP du patient pour l'�tablissement courant
   *
   * @param int $group_id Permet de charger l'IPP pour un �tablissement donn� si non null
   *
   * @return void
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

    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_ipp);

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
    $idex = CIdSante400::getMatch('CPatient', $tag_ipp, $this->_IPP);

    $this->load($idex->object_id);
  }

  /**
   * Trash IPP
   *
   * @param CIdSante400 $IPP IPP
   *
   * @return string
   */
  function trashIPP(CIdSante400 $IPP) {
    $IPP->tag         = CAppUI::conf("dPpatients CPatient tag_ipp_trash").$IPP->tag;
    $IPP->last_update = CMbDT::dateTime();

    return $IPP->store();
  }


  static function massCountPhotoIdentite($patients) {
    CFile::massCountNamed($patients, "identite.jpg");
  }

  function loadRefPhotoIdentite() {
    $file = CFile::loadNamed($this, "identite.jpg");
    $this->_can_see_photo = 1;
    if ($file->_id) {
      $author = $file->loadRefAuthor();
      global $can;
      $this->_can_see_photo = $can->admin || CAppUI::$user->function_id == $author->function_id;
    }

    return $this->_ref_photo_identite = $file;
  }

  function loadLastGrossesse() {
    $grossesse = new CGrossesse;
    $grossesse->parturiente_id = $this->_id;
    $grossesse->loadMatchingObject("terme_prevu desc");
    return $this->_ref_last_grossesse = $grossesse;
  }

  function getTemplateClasses(){
    $tab = array();
    $tab['CPatient'] = $this->_id;
    $tab['CSejour'] = 0;
    $tab['COperation'] = 0;

    return $tab;
  }

  function getFirstConstantes() {
    return $this->_ref_first_constantes = $this->loadFirstBackRef("constantes", "datetime ASC");
  }

  function fillLimitedTemplate(&$template) {
    CDestinataire::makeAllFor($this);

    $destinataires = CDestinataire::$destByClass;

    foreach ($destinataires as $_destinataires_by_class) {
      foreach ($_destinataires_by_class as $_destinataire) {
        if (!isset($_destinataire->nom) || strlen($_destinataire->nom) == 0 || $_destinataire->nom === " ") {
          continue;
        }

        $template->destinataires[] = array(
          "nom"   => $_destinataire->nom,
          "email" => $_destinataire->email,
          "tag"   => $_destinataire->tag,
        );
      }
    }

    $this->loadRefsFwd();
    $this->loadRefConstantesMedicales(null, array(), null, false);
    $this->loadIPP();

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Patient - article"           , $this->_civilite  );
    $template->addProperty("Patient - article long"      , ucfirst($this->_civilite_long));
    $template->addProperty("Patient - article long (minuscule)", strtolower($this->_civilite_long));
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - nom jeune fille"   , $this->nom_jeune_fille);
    $template->addProperty("Patient - pr�nom"            , $this->prenom     );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - ann�es"            , $this->_annees    );
    $template->addProperty("Patient - �ge"               , $this->_age       );
    $template->addProperty("Patient - date de naissance" , $this->getFormattedValue("naissance"));
    $template->addProperty("Patient - lieu de naissance" , $this->lieu_naissance);
    $template->addProperty("Patient - sexe"              , strtolower($this->getFormattedValue("sexe")));
    $template->addProperty("Patient - sexe court"        , substr(strtolower($this->getFormattedValue("sexe")), 0, 1));
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
    $template->addBarcode("Patient - Code barre ID"     , $this->_id   );
    $template->addBarcode("Patient - Code barre IPP"    , $this->_IPP  );

    if ($this->sexe === "m") {
      $template->addProperty("Patient - il/elle"         , "il"              );
      $template->addProperty("Patient - Il/Elle (majuscule)", "Il"           );
      $template->addProperty("Patient - le/la"           , "le"              );
      $template->addProperty("Patient - Le/La (majuscule)", "Le"             );
      $template->addProperty("Patient - du/de la"        , "du"              );
      $template->addProperty("Patient - au/� la"         , "au"              );
      $template->addProperty("Patient - accord genre"    , ""                );
    }
    else {
      $template->addProperty("Patient - il/elle"         , "elle"            );
      $template->addProperty("Patient - Il/Elle (majuscule)", "Elle"         );
      $template->addProperty("Patient - le/la"           , "la"              );
      $template->addProperty("Patient - Le/La (majuscule)", "La"             );
      $template->addProperty("Patient - du/de la"        , "de la"           );
      $template->addProperty("Patient - au/� la"         , "� la"            );
      $template->addProperty("Patient - accord genre"    , "e"               );
    }

    if ($this->medecin_traitant) {
      $medecin = $this->_ref_medecin_traitant;
      $template->addProperty("Patient - m�decin traitant"          , "$medecin->nom $medecin->prenom");
      $template->addProperty("Patient - m�decin traitant - adresse", "$medecin->adresse \n $medecin->cp $medecin->ville");
      $template->addProperty("Patient - m�decin traitant - voie"   , $medecin->adresse);
      $template->addProperty("Patient - m�decin traitant - cp"     , $medecin->cp);
      $template->addProperty("Patient - m�decin traitant - ville"  , $medecin->ville);
    }
    else {
      $template->addProperty("Patient - m�decin traitant");
      $template->addProperty("Patient - m�decin traitant - adresse");
      $template->addProperty("Patient - m�decin traitant - voie");
      $template->addProperty("Patient - m�decin traitant - cp");
      $template->addProperty("Patient - m�decin traitant - ville");
    }

    // Employeur
    $this->loadRefsCorrespondantsPatient();
    $correspondants = $this->_ref_cp_by_relation;

    foreach ($correspondants as $relation => $_correspondants) {
      $_correspondant = @reset($_correspondants);

      // Dans le cas d'un mod�le, cr�ation d'un correspondant pour chaque type de relation
      if (!count($_correspondants)) {
        $_correspondant = new CCorrespondantPatient;
        $_correspondant->relation = $relation;
      }

      switch ($_correspondant->relation) {
        case "employeur" :
          $template->addProperty("Patient - employeur - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - employeur - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - employeur - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - employeur - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - employeur - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - employeur - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - employeur - urssaf" , $_correspondant->urssaf);
          break;
        case "prevenir":
          $template->addProperty("Patient - pr�venir - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - pr�venir - pr�nom" , $_correspondant->prenom);
          $template->addProperty("Patient - pr�venir - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - pr�venir - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - pr�venir - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - pr�venir - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - pr�venir - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - pr�venir - parente", $_correspondant->parente);
          break;
        case "confiance":
          $template->addProperty("Patient - confiance - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - confiance - nom de jeune fille" , $_correspondant->nom_jeune_fille);
          $template->addProperty("Patient - confiance - pr�nom" , $_correspondant->prenom);
          $template->addProperty("Patient - confiance - date de naissance" , $_correspondant->getFormattedValue("naissance"));
          $template->addProperty("Patient - confiance - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - confiance - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - confiance - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - confiance - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - confiance - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - confiance - parente", $_correspondant->parente);
      }
    }

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
      $template->addProperty("Patient - m�decin correspondant $i - sp�cialit�", CMbString::htmlEntities($medecin->disciplines));
    }

    $template->addProperty("Patient - m�decins correspondants", implode(" - ", $noms));

    //Liste des s�jours du patient
    $this->loadRefsSejours();

    if (is_array($this->_ref_sejours)) {
      foreach ($this->_ref_sejours as $_sejour) {
        $_sejour->loadRefPraticien();
      }
      $smarty = new CSmartyDP("modules/dPpatients");
      $smarty->assign("sejours", $this->_ref_sejours);
      $sejours = $smarty->fetch("print_closed_sejours.tpl", '', '', 0);
      $sejours = preg_replace('`([\\n\\r])`', '', $sejours);
    }
    else {
      $sejours = CAppUI::tr("CSejour.none");
    }
    $template->addProperty("Patient - liste des s�jours", $sejours, '', false);

    $const_med = $this->_ref_constantes_medicales;

    $grid_complet = CConstantesMedicales::buildGrid(array($const_med), true);
    $grid_minimal = CConstantesMedicales::buildGrid(array($const_med), false);
    $grid_valued  = CConstantesMedicales::buildGrid(array($const_med), false, true);

    $smarty = new CSmartyDP("modules/dPpatients");

    // Horizontal
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_horiz = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_complet_horiz = preg_replace('`([\\n\\r])`', '', $constantes_complet_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_horiz = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_minimal_horiz = preg_replace('`([\\n\\r])`', '', $constantes_minimal_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_horiz  = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_valued_horiz  = preg_replace('`([\\n\\r])`', '', $constantes_valued_horiz);

    // Vertical
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_vert  = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_complet_vert  = preg_replace('`([\\n\\r])`', '', $constantes_complet_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_vert  = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_minimal_vert  = preg_replace('`([\\n\\r])`', '', $constantes_minimal_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_vert   = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_valued_vert   = preg_replace('`([\\n\\r])`', '', $constantes_valued_vert);

    // Liste des fichiers
    $this->loadRefsFiles();
    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Patient - Liste des fichiers", $list);

    // Identit�
    $identite = $this->loadNamedFile("identite.jpg");
    $template->addImageProperty("Patient - Photo d'identite", $identite->_id);

    $template->addProperty("Patient - Constantes - mode complet horizontal", $constantes_complet_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode minimal horizontal", $constantes_minimal_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode avec valeurs horizontal", $constantes_valued_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode complet vertical"  , $constantes_complet_vert, '', false);
    $template->addProperty("Patient - Constantes - mode minimal vertical"  , $constantes_minimal_vert, '', false);
    $template->addProperty("Patient - Constantes - mode avec valeurs vertical"  , $constantes_valued_vert, '', false);
    $template->addProperty("Patient - poids",  "$const_med->poids kg");
    $template->addProperty("Patient - taille", "$const_med->taille cm");
    $template->addProperty("Patient - Pouls",  $const_med->pouls);
    $template->addProperty("Patient - IMC",    $const_med->_imc);
    $template->addProperty("Patient - VST",    $const_med->_vst);
    $template->addProperty("Patient - temp�rature", $const_med->temperature."�");
    $template->addProperty("Patient - TA",     ($const_med->ta ? "$const_med->_ta_systole / $const_med->_ta_diastole" : ""));

    // Assur� social
    $template->addProperty("Patient - Assur� social - nom", $this->assure_nom);
    $template->addProperty("Patient - Assur� social - nom jeune fille", $this->assure_nom_jeune_fille);
    $template->addProperty("Patient - Assur� social - pr�nom", $this->assure_prenom);
    $template->addProperty("Patient - Assur� social - date de naissance", $this->getFormattedValue("assure_naissance"));
    $template->addProperty("Patient - Assur� social - article", $this->_assure_civilite);
    $template->addProperty("Patient - Assur� social - article long", $this->_assure_civilite_long);
    $template->addProperty("Patient - Assur� social - adresse", $this->assure_adresse);
    $template->addProperty("Patient - Assur� social - ville", $this->assure_ville);
    $template->addProperty("Patient - Assur� social - cp", $this->assure_cp);
    $template->addProperty("Patient - Assur� social - pays", $this->assure_pays);
    $template->addProperty("Patient - Assur� social - t�l�phone", $this->assure_tel);
    $template->addProperty("Patient - Assur� social - mobile", $this->assure_tel2);
    $template->addProperty("Patient - Assur� social - cp naissance", $this->assure_cp_naissance);
    $template->addProperty("Patient - Assur� social - lieu de naissance", $this->assure_lieu_naissance);
    $template->addProperty("Patient - Assur� social - profession", $this->assure_profession);

    // B�n�ficiaire de soins
    $template->addProperty("Patient - B�n�ficiaire de soin - code r�gime", $this->code_regime);
    $template->addProperty("Patient - B�n�ficiaire de soin - caisse gest", $this->caisse_gest);
    $template->addProperty("Patient - B�n�ficiaire de soin - centre gest", $this->centre_gest);
    $template->addProperty("Patient - B�n�ficiaire de soin - code gest"  , $this->code_gestion);
    $template->addProperty("Patient - B�n�ficiaire de soin - r�gime sant�", $this->regime_sante);
    $template->addDateProperty("Patient - B�n�ficiaire de soin - d�but p�riode", $this->deb_amo);
    $template->addDateProperty("Patient - B�n�ficiaire de soin - fin p�riode", $this->fin_amo);
    $template->addProperty("Patient - B�n�ficiaire de soin - r�gime am"  , $this->getFormattedValue("regime_am"));
    $template->addProperty("Patient - B�n�ficiaire de soin - ald"        , $this->getFormattedValue("ald"));
    $template->addProperty("Patient - B�n�ficiaire de soin - incapable majeur", $this->getFormattedValue("incapable_majeur"));
    $template->addProperty("Patient - B�n�ficiaire de soin - cmu"        , $this->getFormattedValue("cmu"));
    $template->addProperty("Patient - B�n�ficiaire de soin - ATNC"       , $this->getFormattedValue("ATNC"));
    $template->addDateProperty("Patient - B�n�ficiaire de soin - validit� vitale", $this->fin_validite_vitale);
    $template->addProperty("Patient - B�n�ficiaire de soin - m�decin traitant d�clar�", $this->getFormattedValue("medecin_traitant_declare"));
    $template->addProperty("Patient - B�n�ficiaire de soin - types contrat mutuelle", addslashes($this->mutuelle_types_contrat));
    $template->addProperty("Patient - B�n�ficiaire de soin - notes amo"  , addslashes($this->notes_amo));
    $template->addProperty("Patient - B�n�ficiaire de soin - libell� exo", addslashes($this->libelle_exo));
    $template->addProperty("Patient - B�n�ficiaire de soin - notes amc"  , addslashes($this->notes_amc));

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Patient");
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);

    // Dossier m�dical
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->fillTemplate($template);
  }

  function getLabelTable() {
    return array (
      "[NOM]"        => $this->nom,
      "[PRENOM]"     => $this->prenom,
      "[SEXE]"       => $this->sexe,
      "[NOM JF]"     => $this->nom_jeune_fille,
      "[DATE NAISS]" => $this->naissance,
      "[NUM SECU]"   => $this->matricule,
    );
  }

  function updateNomPaysInsee() {
    $pays = new CPaysInsee();
    if ($this->pays_naissance_insee) {
      $where = array(
        "numerique" => $pays->_spec->ds->prepare("= %", $this->pays_naissance_insee),
      );
      $pays->loadObject($where);
      $this->_pays_naissance_insee = $pays->nom_fr;
    }
    if ($this->assure_pays_naissance_insee) {
      $where = array(
        "numerique" => $pays->_spec->ds->prepare("= %", $this->assure_pays_naissance_insee),
      );
      $pays->loadObject($where);
      $this->_assure_pays_naissance_insee = $pays->nom_fr;
    }
  }

  function updatePatNomPays($pays_insee) {
    $pays = new CPaysInsee();
    $where = array(
      "numerique" => $pays->_spec->ds->prepare("= %", $pays_insee),
    );
    $pays->loadObject($where);

    return $pays->nom_fr;
  }

  function getCSPName() {
    // Query
    $select = "SELECT LIBELLE FROM categorie_socioprofessionnelle";
    $where  = "WHERE CODE = '$this->csp'";
    $query  = "$select $where";

    $ds = CSQLDataSource::get("INSEE");

    return $ds->loadResult($query);
  }

  function updatePatNumPaysInsee($nomPays) {
    $pays = new CPaysInsee();
    $pays->nom_fr = $this->_spec->ds->escape($nomPays);
    $pays->loadMatchingObject();

    if (!$pays->_id) {
      return "000";
    }

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
      return null;
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

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefsCorrespondants();
  }

  function countNbEnfants() {
    $this->_nb_enfants = 0;
    foreach ($this->loadRefsGrossesses() as $_grossesse) {
      $this->_nb_enfants += $_grossesse->countBackRefs("naissances");
    }
    return $this->_nb_enfants;
  }

  function completeLabelFields(&$fields) {
    $this->loadIPP();
    $medecin_traitant = new CMedecin();
    $medecin_traitant->load($this->medecin_traitant);
    $this->loadRefsCorrespondantsPatient();
    $prevenir = new CCorrespondantPatient();

    if (count($this->_ref_cp_by_relation["prevenir"])) {
      $prevenir = reset($this->_ref_cp_by_relation["prevenir"]);
    }

    $fields = array_merge(
      $fields,
      array(
        "DATE NAISS"      => CMbDT::dateToLocale($this->naissance), "IPP"    => $this->_IPP,
        "LIEU NAISSANCE"  => $this->lieu_naissance,
        "NOM"             => $this->nom,
        "NOM JF"          => $this->nom_jeune_fille,
        "FORMULE NOM JF"  => $this->sexe == "f" && $this->nom_jeune_fille ? "n�e $this->nom_jeune_fille" : "",
        "PRENOM"          => $this->prenom,
        "SEXE"            => strtoupper($this->sexe),
        "CIVILITE"        => $this->civilite,
        "CIVILITE LONGUE" => $this->_civilite_long,
        "ACCORD GENRE"    => $this->sexe == "f" ? "e" : "",
        "CODE BARRE IPP"  => "@BARCODE_" . $this->_IPP."@",
        "ADRESSE"         => "$this->adresse \n$this->cp $this->ville",
        "MED. TRAITANT"   => "Dr $medecin_traitant->nom $medecin_traitant->prenom",
        "TEL"             => $this->getFormattedValue("tel"),
        "TEL PORTABLE"    => $this->getFormattedValue("tel2"),
        "TEL ETRANGER"    => $this->getFormattedValue("tel_autre"),
        "PAYS"            => $this->getFormattedValue("pays"),
        "PREVENIR - NOM"  => $prevenir->nom,
        "PREVENIR - PRENOM" => $prevenir->prenom,
        "PREVENIR - ADRESSE" => $prevenir->adresse,
        "PREVENIR - TEL"  => $prevenir->getFormattedValue("tel"),
        "PREVENIR - PORTABLE"  => $prevenir->getFormattedValue("mob"),
        "PREVENIR - CP VILLE" => "$prevenir->cp $prevenir->ville",
      )
    );
    switch (CAppUI::conf("ref_pays")) {
      case 1:
        $fields["NUM SECU"] = $this->matricule;
        break;
      case 2:
        $fields["AVS"] = $this->getFormattedValue("avs");
    }
  }

  /**
   * Calculation the INSC (Use the data of the vital card (clean the data before!!!))
   *
   * @param String $nir        nir certified
   * @param String $nir_key    key nir
   * @param String $first_name firstname
   * @param String $birth_date birth date
   *
   * @return null|string
   */
  static function calculInsc($nir, $nir_key, $first_name = " ", $birth_date = "000000") {
    $nir_complet = $nir.$nir_key;

    //on v�rifie que le nir est valide
    if (CCodeSpec::checkInsee($nir_complet)) {
      return null;
    }

    //on v�rifie que le nir n'est pas un nir temporaire
    if (!preg_match("/^([12][0-9]{2}[0-9]{2}[0-9][0-9ab][0-9]{3}[0-9]{3})([0-9]{2})$/i", $nir_complet, $matches)) {
      return null;
    }

    if (empty($first_name)) {
      $first_name = " ";
    }

    if (empty ($birth_date)) {
      $birth_date = "000000";
    }

    $first_name = str_replace(" ", "", $first_name);

    if (strlen($first_name) > 10) {
      $first_name = mb_strimwidth($first_name, 0, 10);
    }
    else {
      $first_name = str_pad($first_name, 10);
    }

    $birth_date_length = strlen($birth_date);

    switch ($birth_date_length) {
      case 6:
        list($year, $month, $day) = str_split($birth_date, 2);
        break;
      case 8:
        list($day, $month, $year2, $year) = str_split($birth_date, 2);
        $birth_date = $year.$month.$day;
        break;
      default:
        return null;
    }

    if (!checkdate($month, $day, $year) && $birth_date !== "000000" && strlen($birth_date) !== 6) {
      return null;
    }

    $seed = $first_name.$birth_date.$nir;

    $sha256 = hash("SHA256", $seed);
    $sha256_hex = substr($sha256, 0, 16);
    $insc = self::bchexdec($sha256_hex);

    if (strlen($insc) < 20) {
      $insc = str_pad($insc, 20, 0, STR_PAD_LEFT);
    }

    $insc_key = 97 - bcmod($insc, 97);
    $insc_key = str_pad($insc_key, 2, 0, STR_PAD_LEFT);

    return $insc.$insc_key;
  }

  /**
   * Transform the hexadecimal to decimal
   *
   * @param String $hex String
   *
   * @return int|string
   */
  static function bchexdec($hex) {
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
      $dec = bcadd($dec, bcmul(hexdec($hex[$i - 1]), bcpow('16', $len - $i)));
    }
    if (strpos($dec, ".") !== false) {
      $array = explode(".", $dec);
      $dec = $array[0];
    }
    return $dec;
  }

  /**
   * Apply the mode of identito vigilance
   *
   * @param String $string    String
   * @param Bool   $firstname Apply the lower and the capitalize
   *
   * @return string
   */
  static function applyModeIdentitoVigilance($string, $firstname = false) {
    switch (CAppUI::conf("dPpatients CPatient mode_identito_vigilance", CGroups::loadCurrent())) {
      case "medium":
        $result = CMbString::removeBanCharacter($string);
        $result = $firstname ? CMbString::capitalize(CMbString::lower($result)) : CMbString::upper($result);
        break;
      case "strict":
        $result = CMbString::upper(CMbString::removeBanCharacter($string));
        break;
      default:
        $result = $firstname ? CMbString::capitalize(CMbString::lower($string)) : CMbString::upper($string);
    }

    return $result;
  }

  function getIncrementVars() {
    return array();
  }

  /**
   * Return idex type if it's special (e.g. IPP/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
    // L'identifiant externe est l'IPP
    if ($idex->tag == self::getTagIPP()) {
      return "IPP";
    }

    if (CModule::getActive("mvsante")) {
      return CMVSante::getSpecialIdex($idex);
    }

    return null;
  }

  /**
   * Map the class variable with CPerson variable
   *
   * @return void
   */
  function mapPerson() {
    $this->_p_city                = $this->ville;
    $this->_p_postal_code         = $this->cp;
    $this->_p_street_address      = $this->adresse;
    $this->_p_country             = $this->pays;
    $this->_p_phone_number        = $this->tel;
    $this->_p_mobile_phone_number = $this->tel2;
    $this->_p_email               = $this->email;
    $this->_p_first_name          = $this->prenom;
    $this->_p_last_name           = $this->nom;
    $this->_p_birth_date          = $this->naissance;
    $this->_p_maiden_name         = $this->nom_jeune_fille;
  }
}

CPatient::$fields_etiq[] = CAppUI::conf("ref_pays") == 1 ? "NUM SECU" : "AVS";
