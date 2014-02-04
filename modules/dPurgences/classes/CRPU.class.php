<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CRPU class
 * R�sum� de Passage aux Urgences
 */
class CRPU extends CMbObject {
  // DB Table key
  public $rpu_id;

  static $orientation_value = array("HDT", "HO", "SC", "SI", "REA", "UHCD", "MED",
                                    "CHIR", "OBST", "FUGUE", "SCAM", "PSA", "REO");

  // DB Fields
  public $sejour_id;
  public $motif_entree;
  public $diag_infirmier;
  public $pec_transport;
  public $pec_douleur;
  public $motif;
  public $motif_sfmu;
  public $ccmu;
  public $gemsa;
  public $orientation;
  public $radio_debut;
  public $radio_fin;
  public $bio_depart;
  public $bio_retour;
  public $specia_att;
  public $specia_arr;
  public $mutation_sejour_id;
  public $box_id;
  public $sortie_autorisee;
  public $date_at;
  public $circonstance;
  public $regule_par;
  public $code_diag;

  // Legacy Sherpa fields
  public $type_pathologie; // Should be $urtype
  public $urprov;
  public $urmuta;
  public $urtrau;

  // Form fields
  public $_libelle_circonstance;

  // Distant Fields
  public $_attente;
  public $_presence;
  public $_can_leave;
  public $_can_leave_since;
  public $_can_leave_about;
  public $_can_leave_level;

  // Patient
  public $_patient_id;
  public $_cp;
  public $_ville;
  public $_naissance;
  public $_sexe;

  // Sejour
  public $_responsable_id;
  public $_annule;
  public $_entree;
  public $_DP;
  public $_ref_actes_ccam;
  public $_service_id;
  public $_UHCD;
  public $_etablissement_sortie_id;
  public $_etablissement_entree_id;
  public $_service_entree_id;
  public $_service_sortie_id;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CConsultation */
  public $_ref_consult;

  /** @var CSejour */
  public $_ref_sejour_mutation;

  /** @var CMotif */
  public $_ref_motif;

  /** @var CMotifSFMU */
  public $_ref_motif_sfmu;

  /** @var CLit */
  public $_ref_box;

  /** @var  CCirconstance */
  public $_ref_circonstance;

  // Behaviour fields
  public $_bind_sejour;
  public $_sortie;
  public $_mode_entree;
  public $_mode_sortie;
  public $_date_at;
  public $_provenance;
  public $_destination;
  public $_transport;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rpu';
    $spec->key   = 'rpu_id';
    $spec->measureable = true;
    $spec->events = array(
      "pec" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
    );
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $impose_degre_urgence  = CAppUI::conf("dPurgences CRPU impose_degre_urgence", CGroups::loadCurrent()) == 1;
    $impose_diag_infirmier = CAppUI::conf("dPurgences CRPU impose_diag_infirmier", CGroups::loadCurrent()) == 1;
    $impose_motif          = CAppUI::conf("dPurgences CRPU impose_motif", CGroups::loadCurrent()) == 1;
    $impose_motif_sfmu     = CAppUI::conf("dPurgences gestion_motif_sfmu");

    $props = parent::getProps();
    $props["sejour_id"]                = "ref notNull class|CSejour cascade";
    $props["motif_entree"]             = "text helped";
    $props["diag_infirmier"]           = "text ".($impose_diag_infirmier ? 'notNull ' : '')."helped";
    $props["pec_douleur"]              = "text helped";
    $props["pec_transport"]            = "enum list|med|paramed|aucun";
    $props["motif"]                    = "text ".($impose_motif ? 'notNull ' : '')."helped";
    $props["motif_sfmu"]               = "ref ".($impose_motif_sfmu ? 'notNull ' : '')."class|CMotifSFMU autocomplete|libelle";
    $props["ccmu"]                     = "enum ".($impose_degre_urgence ? 'notNull ' : '')."list|1|P|2|3|4|5|D";
    $props["gemsa"]                    = "enum list|1|2|3|4|5|6";
    $props["type_pathologie"]          = "enum list|C|E|M|P|T";
    $props["orientation"]              = "enum list|".implode("|", self::$orientation_value);
    $props["radio_debut"]              = "dateTime";
    $props["radio_fin"]                = "dateTime";
    $props["bio_depart"]               = "dateTime";
    $props["bio_retour"]               = "dateTime";
    $props["specia_att"]               = "dateTime";
    $props["specia_arr"]               = "dateTime";
    $props["mutation_sejour_id"]       = "ref class|CSejour";
    $props["box_id"]                   = "ref class|CLit";
    $props["sortie_autorisee"]         = "bool";
    $props["date_at"]                  = "date";
    $props["circonstance"]             = "ref class|CCirconstance autocomplete|libelle dependsOn|actif";
    $props["regule_par"]               = "enum list|centre_15|medecin";
    $props["code_diag"]                = "num";

    $props["_DP"]                      = "code cim10 show|1";
    $props["_provenance"]              = "enum list|1|2|3|4|5|6|7|8";
    $props["_destination"]             = "enum list|".implode("|", CSejour::$destination_values);
    $props["_transport"]               = "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo notNull";
    $props["_mode_entree"]             = "enum list|6|7|8 notNull";
    $props["_mode_sortie"]             = "enum list|6|7|8|9 default|8";
    $props["_sortie"]                  = "dateTime";
    $props["_patient_id"]              = "ref notNull class|CPatient";
    $props["_responsable_id"]          = "ref notNull class|CMediusers";
    $props["_service_id"]              = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService";
    $props["_UHCD"]                    = "bool";
    $props["_entree"]                  = "dateTime";
    $props["_etablissement_sortie_id"] = "ref class|CEtabExterne autocomplete|nom";
    $props["_etablissement_entree_id"] = "ref class|CEtabExterne autocomplete|nom";
    $props["_service_entree_id"]       = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["_service_sortie_id"]       = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["_attente"]                 = "time";
    $props["_presence"]                = "time";
    $props["_can_leave"]               = "time";
    $props["_can_leave_about"]         = "bool";
    $props["_can_leave_since"]         = "bool";
    $props["_can_leave_level"]         = "enum list|ok|warning|error";

    $props["urprov"] = "";
    $props["urmuta"] = "";
    $props["urtrau"] = "";

    // Legacy Sherpa fields
    if (CModule::getActive("sherpa")) {
      $urgDro = new CSpUrgDro();
      $props["urprov"] = $urgDro->_props["urprov"] . " notNull";
      $props["urmuta"] = $urgDro->_props["urmuta"];
      $props["urtrau"] = $urgDro->_props["urtrau"];
    }

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passages"] = "CRPUPassage rpu_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    // @todo: A supprimer du updateFormFields
    $sejour = $this->loadRefSejour();

    $this->_responsable_id = $sejour->praticien_id;
    $this->_entree         = $sejour->_entree;
    $this->_DP             = $sejour->DP;
    $this->_annule         = $sejour->annule;
    $this->_UHCD           = $sejour->UHCD;

    $patient =& $sejour->_ref_patient;

    $this->_patient_id = $patient->_id;
    $this->_cp         = $patient->cp;
    $this->_ville      = $patient->ville;
    $this->_naissance  = $patient->naissance;
    $this->_sexe       = $patient->sexe;
    $this->_view       = "RPU du " . CMbDT::dateToLocale(CMbDT::date($this->_entree)). " pour $patient->_view";

    // Calcul des valeurs de _mode_sortie
    if ($sejour->mode_sortie == "mutation") {
      $this->_mode_sortie = 6;
    }

    if ($sejour->mode_sortie == "transfert") {
      $this->_mode_sortie = 7;
    }

    if ($sejour->mode_sortie == "normal") {
      $this->_mode_sortie = 8;
    }

    if ($sejour->mode_sortie == "deces") {
      $this->_mode_sortie = 9;
    }

    $this->_mode_entree             = $sejour->mode_entree;
    $this->_sortie                  = $sejour->sortie_reelle;
    $this->_provenance              = $sejour->provenance;
    $this->_transport               = $sejour->transport;
    $this->_destination             = $sejour->destination;
    $this->_etablissement_sortie_id = $sejour->etablissement_sortie_id;
    $this->_etablissement_entree_id = $sejour->etablissement_entree_id;
    $this->_service_entree_id       = $sejour->service_entree_id;
    $this->_service_sortie_id       = $sejour->service_sortie_id;

    // @todo: A supprimer du updateFormFields
    $this->loadRefConsult();
    if ($this->_ref_consult->_id) {
      $this->_ref_consult->countDocItems();
    }

    // R�cup�ration du libell� de la circonstance si actif dans la configuration
    if (CAppUI::conf("dPurgences gerer_circonstance")) {
      $this->getCirconstance();
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
  }

  /**
   * Chargement du s�jour
   *
   * @return CSejour
   */
  function loadRefSejour() {
    /** @var CSejour $sejour */
    $sejour = $this->loadFwdRef("sejour_id", true);
    $sejour->loadRefsFwd();

    // Calcul des temps d'attente et pr�sence
    $entree = CMbDT::time($sejour->_entree);
    $this->_presence = CMbDT::subTime($entree, CMbDT::time());

    if ($sejour->sortie_reelle) {
      $this->_presence = CMbDT::subTime($entree, CMbDT::time($sejour->sortie_reelle));
    }

    return $this->_ref_sejour = $sejour;
  }

  /**
   * Load ref consult
   *
   * @return void
   */
  function loadRefConsult() {
    // Chargement de la consultation ATU
    if (!$this->_ref_sejour) {
      $this->loadRefSejour();
    }

    $sejour =& $this->_ref_sejour;
    $sejour->loadRefsConsultations();

    if (!CAppUI::conf("dPurgences create_sejour_hospit") && $this->mutation_sejour_id) {
      $this->loadRefSejourMutation()->loadRefsConsultations();
      $this->_ref_consult = $this->_ref_sejour_mutation->_ref_consult_atu;
    }
    else {
      $this->_ref_consult = $this->_ref_sejour->_ref_consult_atu;
    }

    // Calcul du l'attente
    $this->_attente  = $this->_presence;
    if ($this->_ref_consult->_id) {
      $entree = CMbDT::time($this->_ref_sejour->_entree);
      $this->_attente  = CMbDT::subTime(
        CMbDT::transform($entree, null, "%H:%M:00"), CMbDT::transform(CMbDT::time($this->_ref_consult->heure), null, "%H:%M:00")
      );
    }

    $this->_can_leave_level = $sejour->sortie_reelle ? "" : "ok";
    if (!$sejour->sortie_reelle) {
      if (!$this->_ref_consult->_id) {
        $this->_can_leave_level = "warning";
      }

      // En consultation
      if ($this->_ref_consult->chrono != 64) {
        $this->_can_leave = -1;
        $this->_can_leave_level = "warning";
      }
      else {
        if (CMbDT::time($sejour->sortie_prevue) > CMbDT::time()) {
          $this->_can_leave_since = true;
          $this->_can_leave = CMbDT::timeRelative(CMbDT::time(), CMbDT::time($sejour->sortie_prevue));
        }
        else {
          $this->_can_leave_about = true;
          $this->_can_leave = CMbDT::timeRelative(CMbDT::time($sejour->sortie_prevue), CMbDT::time());
        }

        if (CAppUI::conf("dPurgences rpu_warning_time") < $this->_can_leave) {
          $this->_can_leave_level = "warning";
        }

        if (CAppUI::conf("dPurgences rpu_warning_time") < $this->_can_leave) {
          $this->_can_leave_level = "error";
        }
      }
    }

    return $this->_ref_consult;
  }

  /**
   * Load ref mutation
   *
   * @return CSejour
   */
  function loadRefSejourMutation() {
    /** @var CSejour $sejour */
    $sejour = $this->loadFwdRef("mutation_sejour_id", true);
    $sejour->loadNDA();
    return $this->_ref_sejour_mutation = $sejour;
  }

  /**
   * Bind sejour
   *
   * @return null|string
   */
  function bindSejour() {
    if (!$this->_bind_sejour) {
      return null;
    }

    $this->completeField("sejour_id");

    $this->_bind_sejour = false;

    $this->loadRefsFwd();
    $sejour = $this->_ref_sejour;
    $sejour->patient_id    = $this->_patient_id;
    $sejour->group_id      = CGroups::loadCurrent()->_id;
    $sejour->praticien_id  = $this->_responsable_id;
    $sejour->type          = "urg";
    $sejour->recuse        = CAppUI::conf("dPplanningOp CSejour use_recuse") ? -1 : 0;
    $sejour->entree_prevue = $this->_entree;
    $sejour->entree_reelle = $this->_entree;
    $sejour->sortie_prevue = (CAppUI::conf("dPurgences sortie_prevue") == "h24") ?
      CMbDT::dateTime("+1 DAY", $this->_entree) : CMbDT::date(null, $this->_entree)." 23:59:59";
    $sejour->annule        = $this->_annule;
    $sejour->service_id    = $this->_service_id;
    $sejour->etablissement_entree_id = $this->_etablissement_entree_id;
    $sejour->service_entree_id = $this->_service_entree_id;
    $sejour->mode_entree = $this->_mode_entree;
    $sejour->provenance  = $this->_provenance;
    $sejour->destination = $this->_destination;
    $sejour->transport   = $this->_transport;
    $sejour->UHCD        = $this->_UHCD;
    // Le patient est souvent charg� � vide ce qui pose probl�me
    // dans le onAfterStore(). Ne pas supprimer.
    $sejour->_ref_patient = null;

    if ($msg = $sejour->store()) {
      return $msg;
    }

    // Affectation du sejour_id au RPU
    $this->sejour_id = $sejour->_id;

    return null;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Cr�ation du RPU en l'associant � un s�jour existant
    if (!$this->_id && $this->sejour_id) {
      $sejour = $this->loadRefSejour();
      // Si y'a un RPU d�j� existant on alerte d'une erreur
      if ($sejour->countBackRefs("rpu")) {
        return CAppUI::tr("CRPU-already-exists");
      }
    }

    // Cr�ation du RPU ET du s�jour associ�
    if (!$this->_id && !$this->sejour_id) {
      $sejour                = new CSejour();
      $sejour->patient_id    = $this->_patient_id;
      $sejour->type          = "urg";
      $sejour->entree_reelle = $this->_entree;
      $sejour->group_id      = CGroups::loadCurrent()->_id;

      $sortie_prevue         = CAppUI::conf("dPurgences sortie_prevue") == "h24" ?
        CMbDT::dateTime("+1 DAY", $this->_entree) :
        CMbDT::date(null, $this->_entree)." 23:59:59";
      $sejour->sortie_prevue = $this->_sortie ? $this->_sortie : $sortie_prevue;

      // En cas de ressemblance � quelques heures pr�s (cas des urgences), on a affaire au m�me s�jour
      $siblings = $sejour->getSiblings(CAppUI::conf("dPurgences sibling_hours"), $sejour->type);
      if (count($siblings)) {
        $sibling = reset($siblings);
        $this->sejour_id = $sibling->_id;
        $sejour = $this->loadRefSejour();

        // Si y'a un RPU d�j� existant on alerte d'une erreur
        if ($sejour->countBackRefs("rpu")) {
          return CAppUI::tr("CRPU-already-exists");
        }

        $sejour->service_id              = $this->_service_id;
        $sejour->etablissement_entree_id = $this->_etablissement_entree_id;
        $sejour->service_entree_id       = $this->_service_entree_id;
        $sejour->mode_entree             = $this->_mode_entree;
        $sejour->provenance              = $this->_provenance;
        $sejour->destination             = $this->_destination;
        $sejour->transport               = $this->_transport;
        $sejour->UHCD                    = $this->_UHCD;
      }
    }

    // Changement suivant le mode d'entr�e
    switch ($this->_mode_entree) {
      case 6:
        $this->_etablissement_entree_id = "";
        break;
      case 7:
        $this->_service_entree_id = "";
        break;
      case 8:
        $this->_service_entree_id = "";
        $this->_etablissement_entree_id = "";
        break;
    }

    // Bind Sejour
    if ($msg = $this->bindSejour()) {
      return $msg;
    }

    // Synchronisation AT
    $this->loadRefConsult();

    if ($this->_ref_consult->_id && $this->fieldModified("date_at") && !$this->_date_at) {
      $this->_date_at = true;
      $this->_ref_consult->date_at = $this->date_at;
      if ($msg = $this->_ref_consult->store()) {
        return $msg;
      }
    }
    
    if ($this->code_diag) {
      $this->loadRefMotif();
      $this->diag_infirmier = $this->_ref_motif->_ref_chapitre->nom;
      $this->diag_infirmier .= "\n".$this->code_diag.": ".$this->_ref_motif->nom;
      $this->diag_infirmier .= "\n Degr�s d'urgence entre ".$this->_ref_motif->degre_min." et ".$this->_ref_motif->degre_max;
    }

    // Bind affectation
    if (CAppUI::conf("dPurgences create_affectation")) {
      if ($msg = $this->storeAffectation()) {
        return $msg;
      }
    }
    
    // Standard Store
    if ($msg = parent::store()) {
      return $msg;
    }

    // D�clenchement pour avoir les donn�es RPU
    // Pas de sycnhro dans certains cas
    $this->_ref_sejour->_no_synchro = true;
    $this->_ref_sejour->notify("AfterStore");

    return null;
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();

    $this->loadRefSejour()->loadComplete();
  }

  /**
   * Get circonstance
   *
   * @return void
   */
  function getCirconstance() {
    $circonstance = new CCirconstance();
    $circonstance->load($this->circonstance);
    $this->_libelle_circonstance = $circonstance->libelle;
  }

  /**
   * Load the circonstance
   *
   * @return CCirconstance
   */
  function loadRefCirconstance() {
    $circonstance = new CCirconstance();
    $circonstance->load($this->circonstance);
    return $this->_ref_circonstance = $circonstance;
  }

  /**
   * @see parent::fillLimitedTemplate()
   */
  function fillLimitedTemplate(&$template) {
    $this->loadRefConsult();
    $this->_ref_consult->loadRefPraticien();

    $this->notify("BeforeFillLimitedTemplate", $template);

    // Duplication des champs de la consultation
    $template->addProperty("RPU - Consultation - Praticien nom"    , $this->_ref_consult->_ref_praticien->_user_first_name);
    $template->addProperty("RPU - Consultation - Praticien pr�nom" , $this->_ref_consult->_ref_praticien->_user_last_name);
    $template->addProperty("RPU - Consultation - Motif"            , $this->_ref_consult->motif);
    $template->addProperty("RPU - Consultation - Remarques"        , $this->_ref_consult->rques);
    $template->addProperty("RPU - Consultation - Examen"           , $this->_ref_consult->examen);
    $template->addProperty("RPU - Consultation - Traitement"       , $this->_ref_consult->traitement);

    $template->addProperty("RPU - Diagnostic infirmier"         , $this->diag_infirmier);
    $template->addProperty("RPU - Prise en charge douleur"      , $this->pec_douleur);
    $template->addProperty("RPU - PeC Transport"                , $this->getFormattedValue("pec_transport"));
    $template->addProperty("RPU - Motif"                        , $this->motif);
    $template->addProperty("RPU - CCMU"                         , $this->getFormattedValue("ccmu"));
    $template->addProperty("RPU - Code GEMSA"                   , $this->getFormattedValue("gemsa"));
    $template->addDateTimeProperty("RPU - D�part Radio"         , $this->radio_debut);
    $template->addDateTimeProperty("RPU - Retour Radio"         , $this->radio_fin);
    $template->addDateTimeProperty("RPU - D�p�t Biologie"       , $this->bio_depart);
    $template->addDateTimeProperty("RPU - R�ception Biologie"   , $this->bio_retour);
    $template->addDateTimeProperty("RPU - Attente sp�cialiste"  , $this->specia_att);
    $template->addDateTimeProperty("RPU - Arriv�e sp�cialiste"  , $this->specia_arr);
    $template->addProperty("RPU - Accident du travail"          , $this->getFormattedValue("date_at"));
    $libelle_at = $this->date_at ? "Accident du travail du " . $this->getFormattedValue("date_at") : "";
    $template->addProperty("RPU - Libell� accident du travail"  , $libelle_at);
    $template->addProperty("RPU - Sortie autoris�e"             , $this->getFormattedValue("sortie_autorisee"));

    $lit = new CLit;
    if ($this->box_id) {
      $lit->load($this->box_id);
    }
    $template->addProperty("RPU - Box"                          , $lit->_view);

    if (CAppUI::conf("dPurgences old_rpu") == "1") {
      if (CModule::getActive("sherpa")) {
        $template->addProperty("RPU - Soins pour trauma"  , $this->getFormattedValue("urtrau"));
        $template->addProperty("RPU - Cause du transfert" , $this->getFormattedValue("urmuta"));
      }
      $template->addProperty("RPU - Type de pathologie"   , $this->getFormattedValue("type_pathologie"));
    }
    else {
      $template->addProperty("RPU - Orientation"          , $this->getFormattedValue("orientation"));
    }

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "RPU");
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::completeLabelFields()
   */
  function completeLabelFields(&$fields) {
    $sejour = $this->loadRefSejour();
    $sejour->completeLabelFields($fields);

    $patient = $sejour->loadRefPatient();
    $patient->completeLabelFields($fields);
  }
  
  /**
   * Chargement du motif de l'urgence
   * 
   * @return CMotif
   */
  function loadRefMotif() {
    $motif = new CMotif();
    if ($this->code_diag) {
      $motif->code_diag = $this->code_diag;
      $motif->loadMatchingObject();
      $motif->loadRefChapitre();
    }
    return $this->_ref_motif = $motif;
  }

  /**
   * Return the SFMU Motif
   *
   * @return CMotifSFMU
   */
  function loadRefMotifSFMU() {
    $motif_sfmu = new CMotifSFMU();
    $motif_sfmu->load($this->motif_sfmu);
    return $this->_ref_motif_sfmu = $motif_sfmu;
  }

  /**
   * Load box
   *
   * @param bool $cache Use object cache
   *
   * @return CLit
   */
  function loadRefBox($cache = true){
    return $this->_ref_box = $this->loadFwdRef("box_id", $cache);
  }

  /**
   * Store affectation
   *
   * @return null|string
   */
  function storeAffectation() {
    $this->completeField("box_id", "sejour_id");
    $sejour = $this->loadRefSejour();
    $sejour->completeField("service_id");

    if (!$this->_id && (!$this->box_id || !$this->_service_id)) {
      return null;
    }

    if ($this->_id && (!$this->fieldModified("box_id") && !$sejour->fieldModified("service_id"))) {
      return null;
    }

    $affectations = $sejour->loadRefsAffectations();

    $affectation = new CAffectation();
    $affectation->entree     = (count($affectations) == 0) ? $sejour->entree : CMbDT::dateTime();
    $affectation->lit_id     = $this->box_id;
    $affectation->service_id = $this->_service_id;

    $msg = $sejour->forceAffectation($affectation);

    if ($msg instanceof CAffectation) {
      return null;
    }

    return $msg;
  }
}
