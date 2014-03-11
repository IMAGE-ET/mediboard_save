<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Le dossier d'anesth�sie est une liaison entre une intervention et une consultation pr�-anesth�sique
 * Le dossier contient toutes les informations n�cessaires � l'impression de fiches d'anesth�sie pour le bloc
 *
 * @todo Renommer en CDossierAnesthesie
 */
class CConsultAnesth extends CMbObject implements IPatientRelated {
  // DB Table key
  public $consultation_anesth_id = null;

  // DB References
  public $consultation_id;
  public $operation_id;
  public $sejour_id;
  public $chir_id;

  // DB fields
  public $date_interv;
  public $libelle_interv;

  // @todo A supprimer
  public $antecedents;
  public $traitements;
  public $tabac;
  public $oenolisme;

  // Intubation
  public $mallampati;
  public $bouche;
  public $distThyro;
  public $etatBucco;
  public $intub_difficile;

  // Criteres de ventilation
  public $plus_de_55_ans;
  public $imc_sup_26;
  public $edentation;
  public $ronflements;
  public $barbe;

  // Examen clinique
  public $examenCardio;
  public $examenPulmo;
  public $examenDigest;
  public $examenAutre;

  public $conclusion;
  public $premedication;
  public $prepa_preop;
  public $date_analyse;

  public $rai;
  public $hb;
  public $tp;
  public $tca;
  public $tca_temoin;
  public $creatinine;
  public $fibrinogene;
  public $na;
  public $k;
  public $tsivy;
  public $plaquettes;
  public $ecbu;
  public $ht;
  public $ht_final;
  public $result_ecg;
  public $result_rp;
  public $result_autre;

  // Check sur les codes cim10 de pr�fixe pour non-fumeur:
  //  F17 - T652 - Z720 - Z864 - Z587
  public $apfel_femme;
  public $apfel_non_fumeur;
  public $apfel_atcd_nvp;
  public $apfel_morphine;
  
  // Form fields
  public $_date_consult;
  public $_date_op;
  public $_sec_tsivy;
  public $_min_tsivy;
  public $_sec_tca;
  public $_min_tca;
  public $_intub_difficile;
  public $_clairance;
  public $_psa;
  public $_score_apfel;
  public $_docitems_from_consult;
  
  // Object References
  /** @var  CConsultation */
  public $_ref_consultation;
  /** @var  CMediusers */
  public $_ref_chir;
  /** @var  CTechniqueComp[] */
  public $_ref_techniques;
  /** @var  CConsultAnesth */
  public $_ref_last_consultanesth;
  /** @var  COperation */
  public $_ref_operation;
  /** @var  CSejour */
  public $_ref_sejour;
  /** @var  CPlageconsult */
  public $_ref_plageconsult;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_anesth';
    $spec->key   = 'consultation_anesth_id';
    $spec->events = array(
      "examen" => array(
        "reference1" => array("COperation", "operation_id"),
        "reference2" => array("CSejour",    "sejour_id"),
      ),
    );
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["techniques"] = "CTechniqueComp consultation_anesth_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["consultation_id"]  = "ref notNull class|CConsultation cascade seekable show|0";
    $props["operation_id"]     = "ref class|COperation seekable";
    $props["sejour_id"]        = "ref class|CSejour seekable";
    $props["chir_id"]          = "ref class|CMediusers";

    $props["date_interv"]      = "date";
    $props["libelle_interv"]   = "str";
    
    // @todo: Supprimer ces quatre champs
    $props["antecedents"]      = "text confidential";
    $props["traitements"]      = "text confidential";
    $props["tabac"]            = "text helped";
    $props["oenolisme"]        = "text helped";

    // Donn�es examens complementaires
    $props["rai"]              = "enum list|?|NEG|POS default|? show|0";
    $props["hb"]               = "float min|0 show|0";
    $props["tp"]               = "float min|0 max|140 show|0";
    $props["tca"]              = "numchar maxLength|2 show|0";
    $props["tca_temoin"]       = "numchar maxLength|2 show|0";
    $props["creatinine"]       = "float show|0";
    $props["fibrinogene"]      = "float show|0";
    $props["na"]               = "float min|0 show|0";
    $props["k"]                = "float min|0 show|0";
    $props["tsivy"]            = "time show|0";
    $props["plaquettes"]       = "numchar maxLength|4 pos show|0";
    $props["ecbu"]             = "enum list|?|NEG|POS default|? show|0";
    $props["ht"]               = "float min|0 max|140 show|0";
    $props["ht_final"]         = "float min|0 max|140 show|0";
    $props["result_ecg"]       = "text helped";
    $props["result_rp"]        = "text helped";
    $props["result_autre"]     = "text helped";
    $props["premedication"]    = "text helped";
    $props["prepa_preop"]      = "text helped";
    $props["date_analyse"]     = "date show|0";
    $props["apfel_femme"]      = "bool show|0";
    $props["apfel_non_fumeur"] = "bool show|0";
    $props["apfel_atcd_nvp"]   = "bool show|0";
    $props["apfel_morphine"]   = "bool show|0";
    
    // Champs pour les conditions d'intubation
    $props["mallampati"]       = "enum list|classe1|classe2|classe3|classe4";
    $props["bouche"]           = "enum list|m20|m35|p35";
    $props["distThyro"]        = "enum list|m65|p65";
    $props["etatBucco"]        = "text helped";
    $props["intub_difficile"]  = "bool";
    $props["plus_de_55_ans"]   = "bool";
    $props["imc_sup_26"]       = "bool";
    $props["edentation"]       = "bool";
    $props["ronflements"]      = "bool";
    $props["barbe"]            = "bool";
    $props["examenCardio"]     = "text helped";
    $props["examenPulmo"]      = "text helped";
    $props["examenDigest"]     = "text helped";
    $props["examenAutre"]      = "text helped";
    
    $props["conclusion"]       = "text helped seekable";

    // Champs d�riv�s
    $props["_intub_difficile"] = "";
    $props["_clairance"]       = "";
    $props["_psa"]             = "";
    $props["_score_apfel"]     = "";
    
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    // V�rification si intubation difficile
    $this->_intub_difficile =
       $this->intub_difficile == '1' || 
       (($this->mallampati === "classe3" || $this->mallampati === "classe4" || 
       $this->bouche === "m20" || $this->bouche === "m35" || 
       $this->distThyro === "m65") && $this->intub_difficile != '0');
     
    $this->_sec_tsivy = intval(substr($this->tsivy, 6, 2));
    $this->_min_tsivy = intval(substr($this->tsivy, 3, 2));
    
    $this->_score_apfel = $this->apfel_femme + $this->apfel_non_fumeur + $this->apfel_atcd_nvp + $this->apfel_morphine;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if ($this->_min_tsivy !== null && $this->_sec_tsivy !== null) {
      $this->tsivy  = '00:'.($this->_min_tsivy ? sprintf("%02d", $this->_min_tsivy):'00').':';
      $this->tsivy .=       ($this->_sec_tsivy ? sprintf("%02d", $this->_sec_tsivy):'00');
    }

    parent::updatePlainFields();
  }
  
  /**
   * @see parent::loadRelPatient()
   */
  function loadRelPatient() {
    return $this->loadRefConsultation()->loadRefPatient();
  }

  /**
   * Charge le patient associ�
   *
   * @return CPatient
   */
  function loadRefPatient(){
    return $this->loadRelPatient();
  }

  /**
   * Charge la consultation associ�e
   *
   * @return CConsultation
   */
  function loadRefConsultation() {
    $consultation = $this->loadFwdRef("consultation_id", true);
    $this->_view = $consultation->_view;
    return $this->_ref_consultation = $consultation;
  }

  /**
   * Charge la chirurgien associ�
   *
   * @return CMediusers
   */
  function loadRefChir() {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }

  /**
   * Charge l'op�ration associ�e
   * Value �galement le s�jour associ�
   *
   * @return COperation
   */
  function loadRefOperation() {
    /** @var COperation $operation */
    $operation = $this->loadFwdRef("operation_id", true);
    
    // Chargement du s�jour associ�
    if ($operation->_id) {
      $operation->loadRefPlageOp();
      $this->_ref_sejour = $operation->loadRefSejour();
    }
    else {
      $this->loadRefSejour();
    }
    
    return $this->_ref_operation = $operation;
  }

  /**
   * Charge le s�jour associ�
   *
   * @return CSejour
   */
  function loadRefSejour() {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
    $this->_ref_sejour->loadRefsFwd(true);
    return $this->_ref_sejour;
  }

  /**
   * @see parent::loadRefsFiles()
   */
  function loadRefsFiles() {
    parent::loadRefsFiles();

    if (!$this->_docitems_from_consult) {
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $this->_ref_consultation->loadRefsFiles();
      $this->_nb_cancelled_files += $this->_ref_consultation->_nb_cancelled_files;
      $this->_ref_files = $this->_ref_files + $this->_ref_consultation->_ref_files;
    }
  }

  /**
   * @see parent::loadRefsDocs()
   */
  function loadRefsDocs(){
    parent::loadRefsDocs();

    if (!$this->_docitems_from_consult) {
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $this->_ref_consultation->loadRefsDocs();
      $this->_ref_documents = $this->_ref_documents + $this->_ref_consultation->_ref_documents;
    }
    return count($this->_ref_documents);
  }

  /**
   * @see parent::countDocs()
   */
  function countDocs(){
    $nbDocs = parent::countDocs();

    if (!$this->_docitems_from_consult) {
      // Ajout des documents des dossiers d'anesth�sie
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $nbDocs += $this->_ref_consultation->countDocs();
    }

    return $this->_nb_docs = $nbDocs;
  }

  /**
   * @see parent::countFiles()
   */
  function countFiles(){
    $nbFiles = parent::countFiles();

    if (!$this->_docitems_from_consult) {
      // Ajout des fichiers des dossiers d'anesth�sie
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $nbFiles += $this->_ref_consultation->countFiles();
    }

    return $this->_nb_files = $nbFiles;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->_ref_consultation = $this->_fwd["consultation_id"];
    $this->_ref_consultation->loadView();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete(){
    parent::loadComplete();
    
    $this->_ref_consultation->loadRefsExamsComp();
    $this->_ref_consultation->loadRefsExamNyha();
    $this->_ref_consultation->loadRefsExamPossum();
    
    $this->loadRefOperation();    
    
    $dossier_medical = $this->_ref_sejour->loadRefDossierMedical();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->loadRefstraitements();

    $patient = $this->loadRefPatient();
    $dossier_medical = $patient->loadRefDossierMedical();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->loadRefstraitements();

    
    // Chargement des actes CCAM
    foreach ($this->_ref_consultation->loadRefsActesCCAM() as $_acte) {
      $_acte->loadRefsFwd();
    }
  }

  /**
   * @deprecated
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefChir();
    
    // Chargement operation/sejour
    $this->loadRefOperation();
    $this->_ref_operation->loadRefsFwd();
    $this->_date_op =& $this->_ref_operation->_datetime;
    
    // Chargement consultation
    $this->loadRefConsultation();
    $this->_ref_consultation->loadRefsFwd();
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->_date_consult =& $this->_ref_consultation->_date;

    // Calcul de la Clairance cr�atinine
    $patient =& $this->_ref_consultation->_ref_patient;
    $patient->loadRefConstantesMedicales(null, null, $this->_ref_consultation, false);
    $const_med = $patient->_ref_constantes_medicales;
    $const_med->updateFormFields();
    $age = intval($patient->_annees);
    if (
        $const_med->poids && $this->creatinine &&
        $age && $age >= 18 && $age <= 110 &&
        $const_med->poids >= 35 && $const_med->poids <= 120 &&
        $this->creatinine >= 6 && $this->creatinine <= 70
    ) {
          $this->_clairance = $const_med->poids * (140-$age) / (7.2 * $this->creatinine);
      if ($patient->sexe == 'm') {
        $this->_clairance *= 1.04;
      }
      else {
        $this->_clairance *= 0.85;
      }
      $this->_clairance = round($this->_clairance, 2);
    }
    
    // Calcul des Pertes Sanguines Acceptables
    if ($this->ht && $this->ht_final && $const_med->_vst) {
      $this->_psa = $const_med->_vst * ($this->ht - $this->ht_final) / 100;
    }
  }

  /**
   * Chargement des techniques compl�mentaires
   *
   * @return CTechniqueComp[]
   */
  function loadRefsTechniques() {
    $techniques = new CTechniqueComp();
    $where = array(
      "consultation_anesth_id" => "= '$this->consultation_anesth_id'"
    );
    return $this->_ref_techniques = $techniques->loadList($where, "technique");
  }

  /**
   * @deprecated
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsTechniques();
  }

  /**
   * @see parent::getTemplateClasses
   */
  function getTemplateClasses(){
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects li�s � l'op�ration
    $tab['CConsultAnesth'] = $this->_id;
    $tab['CConsultation'] = $this->_ref_consultation->_id;
    $tab['CPatient'] = $this->_ref_consultation->_ref_patient->_id;
    $tab['COperation'] = $this->_ref_operation->_id;
    $tab['CSejour'] = $this->_ref_operation->_ref_sejour->_id;

    return $tab;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_consultation) {
      $this->loadRefConsultation();
    }
    
    switch ($permType) {
      case PERM_EDIT :
        return $this->_ref_consultation->getPerm($permType);
      default :
        // Droits sur l'op�ration
        if ($this->operation_id) {
          if (!$this->_ref_operation) {
            $this->loadRefOperation();
          }
          $canOper = $this->_ref_operation->getPerm($permType);
        }
        else {
          $canOper = false;
        }
        // Droits sur le s�jour
        if ($this->sejour_id) {
          if (!$this->_ref_sejour) {
            $this->loadRefSejour();
          }
          $canSej = $this->_ref_sejour->getPerm($permType);
        }
        else {
          $canSej = false;
        }
        return $canOper || $canSej || $this->_ref_consultation->getPerm($permType);	
    }
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_consultation->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    $this->_ref_operation->fillLimitedTemplate($template);
    $this->_ref_sejour->fillLimitedTemplate($template);
  }

  /**
   * @see parent::fillLimitedTemplate
   */
  function fillLimitedTemplate(&$template) {
    global $rootName;
    $this->updateFormFields();

    $this->notify("BeforeFillLimitedTemplate", $template);
    
    $template->addProperty("Anesth�sie - Tabac"                  , $this->tabac);
    $template->addProperty("Anesth�sie - Oenolisme"              , $this->oenolisme);

    $dossier_medical = $this->loadRefPatient()->loadRefDossierMedical();
    $template->addProperty("Anesth�sie - Groupe sanguin"         , $dossier_medical->groupe_sanguin." ".$dossier_medical->rhesus);
    $template->addProperty("Anesth�sie - RAI"                    , $this->rai);
    $template->addProperty("Anesth�sie - Hb"                     , "$this->hb g/dl");
    $template->addProperty("Anesth�sie - Ht"                     , "$this->ht %");
    $template->addProperty("Anesth�sie - Ht final"               , "$this->ht_final %");
    $template->addProperty("Anesth�sie - PSA"                    , "$this->_psa ml/GR");
    $template->addProperty("Anesth�sie - Plaquettes"             , ($this->plaquettes*1000)."/mm3");
    $template->addProperty("Anesth�sie - Cr�atinine"             , "$this->creatinine mg/l");
    $template->addProperty("Anesth�sie - Clairance"              , "$this->_clairance ml/min");
    $template->addProperty("Anesth�sie - Na+"                    , "$this->na mmol/l");
    $template->addProperty("Anesth�sie - K+"                     , "$this->k mmol/l");
    $template->addProperty("Anesth�sie - TP"                     , "$this->tp %");
    $template->addProperty("Anesth�sie - TCA"                    , "$this->tca_temoin s / $this->tca s");
    $template->addProperty("Anesth�sie - TS Ivy"                 , "$this->_min_tsivy min $this->_sec_tsivy s");
    $template->addProperty("Anesth�sie - ECBU"                   , $this->ecbu);
    
    $template->addProperty("Anesth�sie - ASA"                    , $this->_ref_operation->ASA);
    $template->addProperty("Anesth�sie - Pr�paration pr�-op�ratoire", $this->prepa_preop);
    $template->addProperty("Anesth�sie - Pr�m�dication", $this->premedication);
    $template->addProperty("Anesth�sie - Position"     , $this->_ref_operation->getFormattedValue('position'));

    $list = CMbArray::pluck($this->loadRefsTechniques(), 'technique');
    $template->addListProperty("Anesth�sie - Techniques compl�mentaires", $list);
    
    $template->addProperty("Anesth�sie - R�sultats ECG"             , $this->result_ecg);
    $template->addProperty("Anesth�sie - R�sultats Radio pulmonaire", $this->result_rp);
    $template->addProperty("Anesth�sie - Examen cardiovasculaire"   , $this->examenCardio);
    $template->addProperty("Anesth�sie - Examen pulmonaire"         , $this->examenPulmo);
    $template->addProperty("Anesth�sie - Examen digestif"           , $this->examenDigest);
    $template->addProperty("Anesth�sie - Examen autre"              , $this->examenAutre);

    $template->addProperty("Anesth�sie - Ouverture de la bouche"    , $this->getFormattedValue('bouche'), null, false);
    $template->addProperty("Anesth�sie - Intubation"                , CAppUI::tr("CConsultAnesth-_intub_" . ($this->_intub_difficile ? "difficile" : "pas_difficile")));

    $ventilation = $this->plus_de_55_ans ? "Plus de 55 ans, ": "";
    $ventilation.= $this->imc_sup_26 ? "IMC > 26Kg/m�, ":"";
    $ventilation.= $this->edentation ? "Edentation, ": "";
    $ventilation.= $this->ronflements ? "Ronflements, ": "" ;
    $ventilation.= $this->barbe ? "Barbe": "" ;
    $template->addProperty("Anesth�sie - Crit�res de ventilation"   , $ventilation ? $ventilation : "Aucun", null, false);

    $template->addProperty("Anesth�sie - Distance thyro-mentonni�re", $this->getFormattedValue('distThyro'), null, false);
    $template->addProperty("Anesth�sie - Etat bucco-dentaire"       , $this->etatBucco);
    $img = "";
    if ($this->mallampati) {
      $img = $this->mallampati.'<br /> .
        <img src="/'.$rootName.'/images/pictures/'.$this->mallampati.'.png" alt="'.$this->mallampati.'" />';
    }
    $template->addProperty("Anesth�sie - Mallampati", $img, null, false);
    $template->addProperty("Anesth�sie - Mallampati (texte seul)", $this->getFormattedValue("mallampati"));
    $template->addProperty("Anesth�sie - Remarques",  $this->conclusion);
    $template->addProperty("Anesth�sie - Score APFEL", $this->_score_apfel);

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Anesth�sie");
    }
    
    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx() {
    // Date d�pass�e
    $this->completeField("consultation_id");

    $consult = $this->loadRefConsultation();
    $consult->loadRefPlageConsult();

    if ($consult->_ref_plageconsult->date < CMbDT::date() && !$this->_ref_module->canDo()->admin) {
      return "Impossible de supprimer un dossier d'anesth�sie d'une consultation pass�e";
    }

    return parent::canDeleteEx();
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("operation_id");

    if ($this->operation_id && $this->fieldModified("operation_id")) {
      $op = new COperation();
      $op->load($this->operation_id);
      $this->sejour_id = $op->sejour_id;
    }

    return parent::store();
  }
}
