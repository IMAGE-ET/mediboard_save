<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * S�jour d'un patient dans un �tablissement
 */
class CSejour extends CFacturable implements IPatientRelated {

  //static lists
  static $types = array("comp", "ambu",  "exte", "seances", "ssr", "psy" ,"urg", "consult");
  static $fields_etiq = array(
    "NDOS", "NRA", "DATE ENT", "HEURE ENT", "DATE SORTIE", "HEURE SORTIE",
    "PRAT RESPONSABLE", "CODE BARRE NDOS", "CHAMBRE COURANTE"
  );

  static $destination_values = array("1","2","3","4","6","7");

  // DB Table key
  public $sejour_id;

  // Cl�ture des actes
  public $cloture_activite_1;
  public $cloture_activite_4;

  // DB R�ference
  public $patient_id;
  public $praticien_id;
  public $group_id;
  public $grossesse_id;

  public $uf_hebergement_id; // UF de responsabilit� d'h�bergement
  public $uf_medicale_id; // UF de responsabilit� m�dicale
  public $uf_soins_id; // UF de responsabilit� de soins

  public $etablissement_entree_id;
  public $etablissement_sortie_id;
  public $service_entree_id; // Service d' entr�e de mutation
  public $service_sortie_id; // Service de sortie de mutation

  // DB Fields
  public $type;
  public $charge_id;
  public $modalite;
  public $annule;
  public $recuse;
  public $chambre_seule;
  public $reanimation;
  public $UHCD;
  public $service_id;

  public $entree_prevue;
  public $sortie_prevue;
  public $entree_reelle;
  public $sortie_reelle;
  public $entree;
  public $sortie;

  public $entree_preparee;
  public $sortie_preparee;
  public $entree_modifiee;
  public $sortie_modifiee;

  public $DP;
  public $DR;
  public $pathologie;
  public $septique;
  public $convalescence;

  public $provenance;
  public $destination;
  public $transport; /* @todo Passer en $transport_entree */
  public $transport_sortie;
  public $rques_transport_sortie;

  public $rques;
  public $ATNC;
  public $consult_accomp;
  public $hormone_croissance;
  public $lit_accompagnant;
  public $isolement;
  public $isolement_date;
  public $isolement_fin;
  public $raison_medicale;
  public $television;
  public $repas_diabete;
  public $repas_sans_sel;
  public $repas_sans_residu;
  public $repas_sans_porc;

  public $mode_entree;
  public $mode_entree_id;
  public $mode_sortie;
  public $mode_sortie_id;

  public $confirme;
  public $prestation_id;
  public $facturable;
  public $adresse_par_prat_id;
  public $libelle;
  public $forfait_se;
  public $forfait_sd;
  public $commentaires_sortie;
  public $discipline_id;
  public $ald;
  public $type_pec;

  public $date_accident;
  public $nature_accident;

  // Form Fields
  /** @deprecated */
  public $_entree;
  /** @deprecated */
  public $_sortie;

  public $_duree_prevue;
  public $_duree_prevue_heure;
  public $_duree_reelle;
  public $_duree;
  public $_date_entree_prevue;
  public $_date_sortie_prevue;
  public $_time_entree_prevue;
  public $_time_sortie_prevue;
  public $_hour_entree_prevue;
  public $_hour_sortie_prevue;
  public $_min_entree_prevue;
  public $_min_sortie_prevue;
  public $_guess_NDA;
  public $_at_midnight;
  public $_couvert_cmu;
  public $_couvert_ald;
  public $_curr_op_id;
  public $_curr_op_date;
  public $_protocole_prescription_anesth_id;
  public $_protocole_prescription_chir_id;
  public $_adresse_par;
  public $_adresse_par_prat;
  public $_etat;
  public $_entree_relative;
  public $_sortie_relative;
  public $_not_collides = array ("urg", "consult", "seances", "exte"); // S�jour dont on ne test pas la collision
  public $_is_proche;
  public $_motif_complet;
  public $_grossesse;
  public $_nb_printers;
  public $_sejours_enfants_ids = array();
  public $_date_deces;
  public $_envoi_mail;
  public $_naissance;
  public $_isolement_date;
  public $_count_modeles_etiq;
  public $_count_tasks;
  public $_count_pending_tasks;
  public $_count_prescriptions;
  public $_count_evenements_ssr;
  public $_count_evenements_ssr_week;
  public $_collisions = array();
  public $_rques_sejour;

  // Behaviour fields
  public $_check_bounds  = true;
  public $_en_mutation;
  public $_unique_lit_id;
  public $_no_synchro;
  public $_generate_NDA            = true;
  public $_skip_date_consistencies = false; // On ne check pas la coh�rence des dates des consults/intervs

  // EAI Fields
  public $_eai_initiateur_group_id; // group initiateur du message EAI

  //Fields for bill
  public $_assurance_maladie;
  public $_rques_assurance_maladie;
  public $_assurance_accident;
  public $_rques_assurance_accident;
  public $_type_sejour;
  public $_statut_pro;
  public $_dialyse;
  public $_cession_creance;

  // References
  /** @var COperation */
  public $_ref_first_operation;
  /** @var COperation */
  public $_ref_last_operation;
  /** @var CAffectation[] */
  public $_ref_affectations;
  /** @var CAffectation */
  public $_ref_first_affectation;
  /** @var CAffectation */
  public $_ref_last_affectation;
  /** @var CAffectation */
  public $_ref_curr_affectation;
  /** @var CAffectation */
  public $_ref_prev_affectation;
  /** @var CAffectation */
  public $_ref_next_affectation;
  /** @var CGHM */
  public $_ref_GHM;
  /** @var CGroups */
  public $_ref_group;
  /** @var CEtabExterne */
  public $_ref_etablissement_transfert;
  /** @var CEtabExterne */
  public $_ref_etablissement_provenance;
  /** @var CService */
  public $_ref_service_mutation;
  /** @var CDossierMedical */
  public $_ref_dossier_medical;
  /** @var CRPU */
  public $_ref_rpu;
  /** @var CBilanSSR */
  public $_ref_bilan_ssr;
  /** @var CFicheAutonomie */
  public $_ref_fiche_autonomie;
  /** @var CConsultAnesth */
  public $_ref_consult_anesth;
  /** @var CConsultation */
  public $_ref_consult_atu;
  /** @var CPrescription */
  public $_ref_last_prescription;
  /** @var CMedecin */
  public $_ref_adresse_par_prat;
  /** @var CIdSante400 */
  public $_ref_NDA;
  /** @var CIdSante400 */
  public $_ref_NPA;
  /** @var CIdSante400 */
  public $_ref_NRA;
  /** @var CReplacement */
  public $_ref_replacement;
  /** @var CMovement */
  public $_ref_hl7_movement;
  /** @var CAffectation */
  public $_ref_hl7_affectation;
  /** @var CGrossesse */
  public $_ref_grossesse;
  /** @var COperation */
  public $_ref_curr_operation;
  /** @var CChargePriceIndicator */
  public $_ref_charge_price_indicator;
  /** @var CModeEntreeSejour */
  public $_ref_mode_entree;
  /** @var CModeSortieSejour */
  public $_ref_mode_sortie;
  /** @var CFactureEtablissement */
  public $_ref_last_facture;
  /** @var CPrestation */
  public $_ref_prestation;
  /** @var CEchangeHprim */
  public $_ref_echange_hprim;
  /** @var CConsultation */
  public $_ref_obs_entree;

  // Collections
  /** @var COperation[] */
  public $_ref_operations;
  /** @var CConsultation[] */
  public $_ref_consultations;
  /** @var CPrescription[] */
  public $_ref_prescriptions;
  /** @var CMediusers[] */
  public $_ref_prescripteurs;
  /** @var CPrescription */
  public $_ref_prescription_sejour;
  /** @var CReplacement[] */
  public $_ref_replacements;
  /** @var CSejourTask[] */
  public $_ref_tasks;
  /** @var CPrescriptionLineElement[] */
  public $_ref_tasks_not_created;
  /** @var CTransmissionMedicale[] */
  public $_ref_transmissions;
  /** @var CObservationMedicale[] */
  public $_ref_observations;
  /** @var COperation[] */
  public $_ref_curr_operations;
  /** @var CExamIgs[] */
  public $_ref_exams_igs;
  /** @var CMovement[] */
  public $_ref_movements;
  /** @var CFactureEtablissement[] */
  public $_ref_factures;
  /** @var CMbObject[] */
  public $_ref_suivi_medical;
  /** @var CItemPrestation[] */
  public $_ref_prestations;
  /** @var CNaissance */
  public $_ref_naissances;
  /** @var CUniteFonctionnelle */
  public $_ref_uf_hebergement;
  /** @var CUniteFonctionnelle */
  public $_ref_uf_soins;
  /** @var CUniteFonctionnelle */
  public $_ref_uf_medicale;

  // External objects
  /** @var CCodeCIM10 */
  public $_ext_diagnostic_principal;
  /** @var CCodeCIM10 */
  public $_ext_diagnostic_relie;

  // Distant fields
  public $_dates_operations;
  public $_dates_consultations;
  public $_codes_ccam_operations;
  public $_NDA; // Num�ro Dossier Administratif
  public $_NDA_view; // Vue du NDA
  public $_NPA; // Num�ro Pr�-Admission
  public $_list_constantes_medicales;
  public $_cancel_alerts;
  public $_diagnostics_associes;
  public $_liaisons_for_prestation;
  public $_first_liaison_for_prestation;

  // Filter Fields
  public $_date_min;
  public $_date_max;
  public $_date_entree;
  public $_date_sortie;
  public $_horodatage;
  public $_admission;
  public $_service;
  public $_type_admission;
  public $_specialite;
  public $_date_min_stat;
  public $_date_max_stat;
  public $_filter_type;
  public $_ccam_libelle;
  public $_coordonnees;

  // Object tool field
  public $_modifier_sortie;
  public $_modifier_entree;

  /**
   * Standard constructor
   */
  function __construct() {
    parent::__construct();

    // Conf cache
    static $conf_locked;
    if (null === $conf_locked) {
      $conf_locked = $this->conf("locked");
    }

    $this->_locked = $conf_locked;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour';
    $spec->key   = 'sejour_id';
    $spec->measureable = true;
    $spec->events = array(
      "suivi_clinique" => array(
        "reference1" => array("CMediusers", "praticien_id"),
        "reference2" => array("CPatient",   "patient_id"),
      ),
    );
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations"]          = "CAffectation sejour_id";
    $backProps["bilan_ssr"]             = "CBilanSSR sejour_id";
    $backProps["consultations_anesths"] = "CConsultAnesth sejour_id";
    $backProps["consultations"]         = "CConsultation sejour_id";
    $backProps["fiche_autonomie"]       = "CFicheAutonomie sejour_id";
    $backProps["GHM"]                   = "CGHM sejour_id";
    $backProps["hprim21_sejours"]       = "CHprim21Sejour sejour_id";
    $backProps["observations"]          = "CObservationMedicale sejour_id";
    $backProps["operations"]            = "COperation sejour_id";
    $backProps["prescriptions"]         = "CPrescription object_id";
    $backProps["rpu"]                   = "CRPU sejour_id";
    $backProps["rpu_mute"]              = "CRPU mutation_sejour_id";
    $backProps["transmissions"]         = "CTransmissionMedicale sejour_id";
    $backProps["dossier_medical"]       = "CDossierMedical object_id";
    $backProps["ghm"]                   = "CGHM sejour_id";
    $backProps["planifications"]        = "CPlanificationSysteme sejour_id";
    $backProps["rhss"]                  = "CRHS sejour_id";
    $backProps["evenements_ssr"]        = "CEvenementSSR sejour_id";
    $backProps["replacements"]          = "CReplacement sejour_id";
    $backProps["echanges_any"]          = "CExchangeAny object_id";
    $backProps["echanges_hprim"]        = "CEchangeHprim object_id";
    $backProps["echanges_hprim21"]      = "CEchangeHprim21 object_id";
    $backProps["echanges_hl7v2"]        = "CExchangeHL7v2 object_id";
    $backProps["echanges_hl7v3"]        = "CExchangeHL7v3 object_id";
    $backProps["echanges_dmp"]          = "CExchangeDMP object_id";
    $backProps["echanges_mvsante"]      = "CExchangeMVSante object_id";
    $backProps["tasks"]                 = "CSejourTask sejour_id";
    $backProps["sejour_brancard"]       = "CBrancardage sejour_id";
    $backProps["naissance"]             = "CNaissance sejour_enfant_id";
    $backProps["naissances"]            = "CNaissance sejour_maman_id";
    $backProps["movements"]             = "CMovement sejour_id";
    $backProps["items_liaisons"]        = "CItemLiaison sejour_id";
    $backProps["exams_igs"]             = "CExamIgs sejour_id";
    $backProps["ufs"]                   = "CAffectationUniteFonctionnelle object_id";
    $backProps["actes_cdarr"]           = "CActeCdARR sejour_id";
    $backProps["actes_csarr"]           = "CActeCsARR sejour_id";
    $backProps["poses_disp_vasc"]       = "CPoseDispositifVasculaire sejour_id";
    $backProps["deliveries"]            = "CProductDelivery sejour_id";
    $backProps["stock_sejour"]          = "CStockSejour sejour_id";
    $backProps["refus_dispensation"]    = "CRefusDispensation sejour_id";
    $backProps["contextes_constante"]    = "CConstantesMedicales context_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $service_id_notNull = CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1;
    $props = parent::getProps();
    $props["patient_id"]               = "ref notNull class|CPatient seekable";
    $props["praticien_id"]             = "ref notNull class|CMediusers seekable autocomplete|nom";
    $props["group_id"]                 = "ref notNull class|CGroups";
    $props["grossesse_id"]             = "ref class|CGrossesse unlink";
    $props["uf_hebergement_id"]        = "ref class|CUniteFonctionnelle seekable";
    $props["uf_medicale_id"]           = "ref class|CUniteFonctionnelle seekable";
    $props["uf_soins_id"]              = "ref class|CUniteFonctionnelle seekable";
    $props["type"]                     = "enum notNull list|".implode("|", self::$types)." default|ambu";
    $props["charge_id"]                = "ref class|CChargePriceIndicator autocomplete|libelle show|0";
    $props["modalite"]                 = "enum notNull list|office|libre|tiers default|libre show|0";
    $props["annule"]                   = "bool show|0";
    $props["recuse"]                   = "enum list|-1|0|1 default|0 show|0";
    $props["chambre_seule"]            = "bool notNull show|0 default|".(CGroups::loadCurrent()->chambre_particuliere ? 1 : 0);
    $props["reanimation"]              = "bool default|0";
    $props["UHCD"]                     = "bool default|0";
    $props["service_id"]               = "ref".($service_id_notNull ? ' notNull' : '')." class|CService seekable";
    $props["entree_prevue"]            = "dateTime notNull show|0";
    $props["sortie_prevue"]            = "dateTime notNull moreEquals|entree_prevue show|0";
    $props["entree_reelle"]            = "dateTime show|0";
    $props["sortie_reelle"]            = "dateTime moreEquals|entree_reelle show|0";
    $props["entree"]                   = "dateTime derived show|0";
    $props["sortie"]                   = "dateTime moreEquals|entree derived show|0";
    $props["entree_preparee"]          = "bool";
    $props["sortie_preparee"]          = "bool";
    $props["entree_modifiee"]          = "bool";
    $props["sortie_modifiee"]          = "bool";
    $props["DP"]                       = "code cim10 show|0";
    $props["DR"]                       = "code cim10 show|0";
    $props["pathologie"]               = "str length|3 show|0";
    $props["septique"]                 = "bool show|0";
    $props["convalescence"]            = "text helped";
    $props["rques"]                    = "text helped";
    $props["ATNC"]                     = "bool show|0";
    $props["consult_accomp"]           = "enum list|oui|non|nc default|nc";
    $props["hormone_croissance"]       = "bool";
    $props["lit_accompagnant"]         = "bool";
    $props["isolement"]                = "bool";
    $props["isolement_date"]           = "dateTime";
    $props["isolement_fin"]            = "dateTime";
    $props["raison_medicale"]          = "text helped";
    $props["television"]               = "bool";

    $props["repas_diabete"]            = "bool";
    $props["repas_sans_sel"]           = "bool";
    $props["repas_sans_residu"]        = "bool";
    $props["repas_sans_porc"]          = "bool";

    $props["mode_entree"]              = "enum list|8|7|6";
    $props["mode_entree_id"]           = "ref class|CModeEntreeSejour autocomplete|libelle|true";
    $props["mode_sortie"]              = "enum list|normal|transfert|mutation|deces";
    $props["mode_sortie_id"]           = "ref class|CModeSortieSejour autocomplete|libelle|true";

    $props["confirme"]                 = "bool";
    $props["prestation_id"]            = "ref class|CPrestation";
    $props["facturable"]               = "bool notNull default|1 show|0";
    $props["etablissement_sortie_id"]  = "ref class|CEtabExterne autocomplete|nom";
    $props["etablissement_entree_id"]  = "ref class|CEtabExterne autocomplete|nom";
    $props["service_entree_id"]        = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["service_sortie_id"]        = "ref class|CService autocomplete|nom dependsOn|group_id|cancelled";
    $props["adresse_par_prat_id"]      = "ref class|CMedecin";
    $props["libelle"]                  = "str seekable autocomplete dependsOn|praticien_id";
    $props["facture"]                  = "bool default|0";
    $props["forfait_se"]               = "bool default|0";
    $props["forfait_sd"]               = "bool default|0";
    $props["commentaires_sortie"]      = "text helped";
    $props["discipline_id"]            = "ref class|CDisciplineTarifaire autocomplete|description show|0";
    $props["ald"]                      = "bool default|0";

    $props["provenance"]               = "enum list|1|2|3|4|5|6|7|8";
    $props["destination"]              = "enum list|".implode("|", self::$destination_values);
    $props["transport"]                = "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo";
    $props["transport_sortie"]         = "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo";
    $props["rques_transport_sortie"]   = "text";
    $props["type_pec"]                 = "enum list|M|C|O";

    $props["date_accident"]            = "date";
    $props["nature_accident"]          = "enum list|P|T|D|S|J|C|L|B|U";

    // Cl�ture des actes
    $props["cloture_activite_1"]    = "bool default|0";
    $props["cloture_activite_4"]    = "bool default|0";

    $props["_rques_assurance_maladie"]  = "text helped";
    $props["_rques_assurance_accident"] = "text helped";
    $props["_assurance_maladie"]        = "ref class|CCorrespondantPatient";
    $props["_assurance_accident"]       = "ref class|CCorrespondantPatient";
    $props["_type_sejour"]              = "enum list|maladie|accident|esthetique default|maladie";
    $props["_dialyse"]                  = "bool default|0";
    $props["_cession_creance"]          = "bool default|0";
    $props["_statut_pro"]               = "enum list|chomeur|etudiant|non_travailleur|independant|".
                                                    "invalide|militaire|retraite|salarie_fr|salarie_sw|sans_emploi";

    $props["_time_entree_prevue"] = "time";
    $props["_time_sortie_prevue"] = "time";

    $props["_entree"]           = "dateTime show";
    $props["_sortie"]           = "dateTime show";
    $props["_date_entree"]      = "date";
    $props["_date_sortie"]      = "date";
    $props["_date_min"]         = "dateTime";
    $props["_date_max"]         = "dateTime moreEquals|_date_min";
    $props["_horodatage"]       = "enum list|entree_prevue|entree_reelle|sortie_prevue|sortie_reelle";
    $props["_admission"]        = "text";
    $props["_service"]          = "text";
    $props["_type_admission"]   = "enum notNull list|ambucomp|comp|ambu|exte|seances|ssr|psy|urg|consult default|ambu";
    $props["_specialite"]       = "text";
    $props["_date_min_stat"]    = "date";
    $props["_date_max_stat"]    = "date moreEquals|_date_min_stat";
    $props["_filter_type"]      = "enum list|comp|ambu|exte|seances|ssr|psy|urg|consult";
    $props["_NDA"]              = "str show|1";
    $props["_ccam_libelle"]     = "bool default|0";
    $props["_coordonnees"]      = "bool default|0";
    $props["_adresse_par"]      = "bool";
    $props["_adresse_par_prat"] = "str";
    $props["_etat"]             = "enum list|preadmission|encours|cloture";

    $props["_duree_prevue"]                     = "num";
    $props["_duree_prevue_heure"]               = "num";
    $props["_duree_reelle"]                     = "num";
    $props["_duree"]                            = "num";
    $props["_date_entree_prevue"]               = "date";
    $props["_date_sortie_prevue"]               = "date moreEquals|_date_entree_prevue";
    $props["_protocole_prescription_anesth_id"] = "str";
    $props["_protocole_prescription_chir_id"]   = "str";
    $props["_motif_complet"]                    = "str";
    $props["_unique_lit_id"]    = "ref class|CLit";
    $props["_date_deces"]       = "date progressive";
    $props["_isolement_date"]   = "dateTime";
    return $props;
  }

  /**
   * @see parent::getRelatedObjectOfClass()
   *
   * @param string $class
   *
   * @return CRPU|null
   */
  function getRelatedObjectOfClass($class) {
    switch ($class) {
      case "CRPU":
        $rpu = $this->loadRefRPU();
        if ($rpu->_id) {
          return $rpu;
        }
    }

    return null;
  }

  /**
   * @see parent::loadRelPatient()
   */
  function loadRelPatient() {
    return $this->loadRefPatient();
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Has to be done first to check and repair fields before further checking
    if ($msg = parent::check()) {
      return $msg;
    }

    $pathos = new CDiscipline();

    // Test de la pathologies
    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_specs["categorie"]->_list))) {
      return "Pathologie non disponible";
    }

    // Test de coherence de date avec les interventions
    if ($this->_check_bounds) {
      $this->completeField("entree_prevue");
      $this->completeField("sortie_prevue");
      $entree = $this->entree_prevue;
      $sortie = $this->sortie_prevue;

      if ($entree !== null && $sortie !== null  && !$this->_skip_date_consistencies) {
        $entree = CMbDT::date($entree);
        $sortie = CMbDT::date($sortie);
        $this->makeDatesOperations();
        if (!$this->entree_reelle) {
          foreach ($this->_dates_operations as $operation_id => $date_operation) {
            if ($this->_curr_op_id == $operation_id) {
              $date_operation = $this->_curr_op_date;
            }

            if (!CMbRange::in($date_operation, $entree, $sortie)) {
               return "Intervention du '$date_operation' en dehors des nouvelles dates du s�jour du '$entree' au '$sortie'";
            }
          }
        }


        if (!$this->entree_reelle && $this->type == "consult") {
          $this->makeDatesConsultations();
          foreach ($this->_dates_consultations as $date_consultation) {
            if (!CMbRange::in($date_consultation, $entree, $sortie)) {
              return "Consultations en dehors des nouvelles dates du s�jour.";
            }
          }
        }
      }

      $this->completeField("entree_reelle", "annule");
      if ($this->fieldModified("annule", "1")) {
        $max_cancel_time = CAppUI::conf("dPplanningOp CSejour max_cancel_time");
        if ((CMbDT::dateTime("+ $max_cancel_time HOUR", $this->entree_reelle) < CMbDT::dateTime())) {
           return "Impossible d'annuler un dossier ayant une entree r�elle depuis plus de $max_cancel_time heures.<br />";
        }
      }

      if (!$this->_merging && !$this->_forwardRefMerging) {
        foreach ($this->getCollisions() as $collision) {
          return "Collision avec le s�jour du '$collision->entree' au '$collision->sortie'";
        }
      }
    }

    return null;
  }

  /**
   * Cherche les diff�rentes collisions au s�jour courant
   *
   * @return array|CSejour
   */
  function getCollisions() {
    $collisions = array();

    // Ne concerne pas les annul�s
    $this->completeField("annule", "type", "group_id", "patient_id");
    if ($this->annule || in_array($this->type, $this->_not_collides)) {
      return $collisions;
    }

    // Donn�es incompl�tes
    if (!$this->entree || !$this->sortie) {
      return $collisions;
    }

    // Test de colision avec un autre sejour
    $patient = new CPatient;
    $patient->load($this->patient_id);
    if (!$patient->_id) {
      return $collisions;
    }

    // Chargement des autres s�jours
    $where["annule"] = " = '0'";
    $where["group_id"] = " = '".$this->group_id."'";
    foreach ($this->_not_collides as $_type_not_collides) {
      $where[] = "type != '$_type_not_collides'";
    }

    $patient->loadRefsSejours($where);
    $sejours = $patient->_ref_sejours;

    // Collision sur chacun des autres s�jours
    foreach ($sejours as $sejour) {
      if ($sejour->_id != $this->_id && $this->collides($sejour)) {
        $collisions[$sejour->_id] = $sejour;
      }
    }

    return $this->_collisions = $collisions;
  }

  /**
   * Cherche des s�jours les dates d'entr�e ou sortie sont proches,
   * pour le m�me patient dans le m�me �tablissement
   *
   * @param int  $tolerance Tol�rance en heures
   * @param bool $use_type  Matche sur le type de s�jour aussi
   *
   * @return CSejour[]
   */
  function getSiblings($tolerance = 1, $use_type = false) {
    $sejour = new CSejour;
    $sejour->patient_id = $this->patient_id;
    $sejour->group_id   = $this->group_id;

    // Si on veut rechercher pour un type de s�jour donn�
    if ($use_type) {
      $sejour->type   = $this->type;
    }

    /** @var CSejour[] $siblings */
    $siblings = $sejour->loadMatchingList();

    $this->updateFormFields();

    // Entree et sortie ne sont pas forc�ment stored
    $entree = $this->entree_reelle ? $this->entree_reelle : $this->entree_prevue;
    $sortie = $this->sortie_reelle ? $this->sortie_reelle : $this->sortie_prevue;

    foreach ($siblings as $_sibling) {
      if ($_sibling->_id == $this->_id) {
        unset($siblings[$_sibling->_id]);
        continue;
      }

      $entree_relative = abs(CMbDT::hoursRelative($entree, $_sibling->entree));
      $sortie_relative = abs(CMbDT::hoursRelative($sortie, $_sibling->sortie));
      if ($entree_relative > $tolerance && $sortie_relative > $tolerance) {
        unset($siblings[$_sibling->_id]);
      }
    }

    return $siblings;
  }

  /**
   * Check if the object collides another
   *
   * @param CSejour $sejour                 Sejour
   * @param bool    $collides_update_sejour Launch updateFormFields
   *
   * @return boolean
   */
  function collides(CSejour $sejour, $collides_update_sejour = true) {

    if ($this->_id && $sejour->_id && $this->_id == $sejour->_id) {
      return false;
    }

    if ($this->annule || $sejour->annule) {
      return false;
    }

    if (in_array($this->type, $this->_not_collides) || in_array($sejour->type, $this->_not_collides)) {
      return false;
    }

    if ($this->group_id != $sejour->group_id) {
      return false;
    }

    if ($collides_update_sejour) {
      $this->updateFormFields();
    }

    switch ($this->conf("check_collisions")) {
      case "no":
        return false;

      case "date":
        $lower1 = CMbDT::date($this->entree);
        $upper1 = CMbDT::date($this->sortie);
        $lower2 = CMbDT::date($sejour->entree);
        $upper2 = CMbDT::date($sejour->sortie);
        break;

      default;
      case "datetime":
        $lower1 = $this->entree;
        $upper1 = $this->sortie;
        $lower2 = $sejour->entree;
        $upper2 = $sejour->sortie;
        break;
    }

    return CMbRange::collides($lower1, $upper1, $lower2, $upper2, false);
  }

  /**
   * Apply a prescription protocol
   *
   * @param int $operation_id Operation ID
   *
   * @return null|string
   */
  function applyProtocolesPrescription($operation_id = null) {
    if (!$this->_protocole_prescription_chir_id) {
      return null;
    }

    // Application du protocole de prescription
    $prescription = new CPrescription;
    $prescription->object_class = $this->_class;
    $prescription->object_id = $this->_id;
    $prescription->type = "sejour";
    if ($msg = $prescription->store()) {
      return $msg;
    }

    /*
    if ($this->_protocole_prescription_anesth_id) {
      $prescription->applyPackOrProtocole(
        $this->_protocole_prescription_anesth_id,
        $this->praticien_id,
        CMbDT::date(),
        null,
        $operation_id
      );
    }
    */
    if ($this->_protocole_prescription_chir_id) {
      $prescription->_dhe_mode = true;
      $prescription->applyPackOrProtocole(
        $this->_protocole_prescription_chir_id,
        $this->praticien_id,
        CMbDT::date(),
        null,
        $operation_id,
        null
      );
    }

    return null;
  }

  /**
   * check for a sectorisation rules to find a service_id
   * if 0 rules, no work
   * if 1 rule, => service_id of the rule
   * if more than rules are found, if they all lead to the same service, we use this service
   *
   * @return bool
   */
  function getServiceFromSectorisationRules() {
    if (!CAppUI::conf("dPplanningOp CRegleSectorisation use_sectorisation") || $this->service_id) {
      return false;
    }
    $this->completeField("type", "praticien_id", "entree", "sortie", "group_id", "type_pec");
    $this->updatePlainFields(); // make sure entree & sortie well defined
    $praticien = $this->loadRefPraticien();

    $where = array();
    $where["type_admission"] = " = '$this->type' OR `type_admission` IS NULL";
    $where["praticien_id"] = " = '$this->praticien_id' OR `praticien_id` IS NULL";
    $where["function_id"] = " = '$praticien->function_id' OR `function_id` IS NULL";
    $where["date_min"] = " <= '$this->entree' OR `date_min` IS NULL";
    $where["date_max"] = " >= '$this->entree' OR `date_max` IS NULL";
    $where["group_id"] = " = '$this->group_id'";

    if ($this->type_pec) {
      $where["type_pec"] = " = '$this->type_pec' OR `type_pec` IS NULL";
    }

    $duree = CMbDT::daysRelative($this->entree, $this->sortie);
    $where["duree_min"] = " <= '$duree' OR `duree_min` IS NULL";
    $where["duree_max"] = " >= '$duree' OR `duree_max` IS NULL";

    $regle = new CRegleSectorisation();

    /** @var CRegleSectorisation[] $regles */
    $regles = $regle->loadList($where);
    $count  = count($regles);

    // one or more rules, lets do the work
    if ($count > 0) {
      //check if all result = same service
      $first = reset($regles);
      $result = 0;
      foreach ($regles as $_regle) {
        if ($_regle->service_id == $first->service_id) {
          $result++;
        }
      }

      //too much results & differents result
      if ($result != $count) {
        CAppUI::setMsg("CRegleSectorisation-number%d-rules-unable-to-resolve", UI_MSG_WARNING, $count);
        return false;
      }

      $first->loadRefService();
      $this->service_id = $first->service_id;
      CAppUI::setMsg("CRegleSectorisation-rule%d-rule%s", UI_MSG_OK, $count, $first->_ref_service->nom);
      return true;
    }

    //no result, no work
    CAppUI::setMsg("CRegleSectorisation-no-rules-found", UI_MSG_WARNING);
    return false;

  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("entree_reelle", "entree", "patient_id", "type_pec", "grossesse_id");

    // Sectorisation Rules
    $this->getServiceFromSectorisationRules();

    // V�rification de la validit� des codes CIM
    if ($this->DP != null) {
      $dp = CCodeCIM10::get($this->DP);
      if (!$dp->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DP = "";
      }
    }
    if ($this->DR != null) {
      $dr = CCodeCIM10::get($this->DR);
      if (!$dr->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DR = "";
      }
    }

    // Mode de sortie normal par d�faut si l'autorisation de sortie est r�alis�e
    if ($this->conf("specified_output_mode") && !$this->mode_sortie && $this->fieldModified("confirme")) {
      $this->mode_sortie = "normal";
    }

    // Annulation de l'�tablissement de transfert si le mode de sortie n'est pas transfert
    if (null !== $this->mode_sortie) {
      if ("transfert" != $this->mode_sortie) {
        $this->etablissement_sortie_id = "";
      }
      if ("mutation" != $this->mode_sortie) {
        $this->service_sortie_id = "";
      }
    }

    // Mise � jour du type PEC si vide
    if (!$this->_id && !$this->type_pec) {
      $this->type_pec = ($this->grossesse_id ? "O" : "M");
    }

    // Annulation de la sortie r�elle si on annule le mode de sortie
    if ($this->mode_sortie === "") {
      $this->sortie_reelle = "";
    }

    // Annulation de l'�tablissement de provenance si le mode d'entr�e n'est pas transfert
    if ($this->fieldModified("mode_entree")) {
      if ("7" != $this->mode_entree) {
        $this->etablissement_entree_id = "";
      }

      if ("6" != $this->mode_entree) {
        $this->service_entree_id = "";
      }
    }

    // Passage au mode transfert si on value un �tablissement de provenance
    if ($this->fieldModified("etablissement_entree_id")) {
      if ($this->etablissement_entree_id != null) {
        $this->mode_entree = 7;
      }
    }

    // Passage au mode mutation si on value un service de provenance
    if ($this->fieldModified("service_entree_id")) {
      if ($this->service_entree_id != null) {
        $this->mode_entree = 6;
      }
    }

    $patient_modified = $this->fieldModified("patient_id");

    // Si le patient est modifi� et qu'il y a plus d'une consult dans le sejour, on empeche le store
    if (!$this->_forwardRefMerging && $this->sejour_id && $patient_modified) {

      $consultations = $this->countBackRefs("consultations");
      if ($consultations > 1) {
        return "D'autres consultations sont pr�vues dans ce s�jour, impossible de changer le patient.";
      }
    }

    // Pour un s�jour non annul�, mise � jour de la date de d�c�s du patient
    // suivant le mode de sortie
    if (!$this->annule) {
      $patient = new CPatient;
      $patient->load($this->patient_id);

      if ("deces" === $this->mode_sortie) {
        $patient->deces = $this->_date_deces;
      }
      else {
        $patient->deces = "";
      }

      // On verifie que le champ a �t� modifi� pour faire le store (sinon probleme lors de la fusion de patients)
      if ($patient->fieldModified("deces")) {
        // Ne pas faire de return $msg ici, car ce n'est pas "bloquant"
        $patient->store();
      }
    }

    // Si annulation possible que par le chef de bloc
    if (
        CAppUI::conf("dPplanningOp COperation cancel_only_for_resp_bloc") &&
        $this->fieldModified("annule", 1) &&
        $this->entree_reelle &&
        !CModule::getCanDo("dPbloc")->edit
    ) {
      foreach ($this->loadRefsOperations() as $_operation) {
        if ($_operation->rank) {
          CAppUI::setMsg(
            "Impossible de sauvegarder : une des interventions du s�jour est valid�e.\nContactez le responsable de bloc",
            UI_MSG_ERROR
          );
          return null;
        }
      }
    }

    // On fixe la r�cusation si pas d�finie pour un nouveau s�jour
    if (!$this->_id && ($this->recuse === "" || $this->recuse === null)) {
      $this->recuse = CAppUI::conf("dPplanningOp CSejour use_recuse") ? -1 : 0;
    }

    // no matter of config, if sejour is "urgence" type: recusation 0
    if ($this->type == "urg") {
      $this->recuse = 0;
    }

    // Si gestion en mode expert de l'isolement
    if (CAppUI::conf("dPplanningOp CSejour systeme_isolement") == "expert") {
      $this->isolement_date =
        $this->_isolement_date !== $this->entree && $this->isolement ?
        $this->_isolement_date : "";
      if (!$this->isolement) {
        $this->isolement_fin = "";
      }
    }

    $facture = null;
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      // Cr�ation de la facture de sejour
      $this->loadRefsFactureEtablissement();
      $facture = $this->_ref_last_facture;
      if (!$facture->_id) {
        $facture->ouverture         = CMbDT::date();
      }
      if (CAppUI::conf("dPfacturation CFactureEtablissement use_temporary_bill")) {
        $facture->temporaire = 1;
      }
      $facture->group_id            = $this->group_id;
      $facture->patient_id          = $this->patient_id;
      $facture->praticien_id        = $this->praticien_id;
      $facture->type_facture        = $this->_type_sejour;
      $facture->dialyse             = $this->_dialyse;
      $facture->cession_creance     = $this->_cession_creance;
      $facture->statut_pro          = $this->_statut_pro;
      $facture->assurance_maladie   = $this->_assurance_maladie;
      $facture->assurance_accident  = $this->_assurance_accident;
      $facture->rques_assurance_accident = $this->_rques_assurance_accident;
      $facture->rques_assurance_maladie  = $this->_rques_assurance_maladie;

      //Store de la facture
      if ($msg = $facture->store()) {
        return $msg;
      }
    }

    $this->completeField("mode_entree_id");
    if ($this->mode_entree_id) {
      /** @var CModeEntreeSejour $mode */
      $mode = $this->loadFwdRef("mode_entree_id");
      $this->mode_entree = $mode->mode;
    }

    $this->completeField("mode_sortie_id");
    if ($this->mode_sortie_id) {
      /** @var CModeSortieSejour $mode */
      $mode = $this->loadFwdRef("mode_sortie_id");
      $this->mode_sortie = $mode->mode;
    }

    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id) {
      if ($msg = $this->bindTarif()) {
        return $msg;
      }
    }

    // Si on change la grossesse d'un s�jour, il faut remapper les naissances �ventuelles
    $change_grossesse = $this->fieldModified("grossesse_id");
    /** @var CNaissance[] $naissances */
    $naissances = array();
    if ($change_grossesse) {
      /** @var CSejour $old */
      $old =  $this->loadOldObject();
      $naissances = $old->loadRefGrossesse()->loadRefsNaissances();
    }

    // On fait le store du s�jour
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($change_grossesse) {
      foreach ($naissances as $_naissance) {
        $_naissance->grossesse_id = $this->grossesse_id;
        if ($msg = $_naissance->store()) {
          return $msg;
        }
      }
    }

    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      if (count($this->_ref_factures) ==0) {
        $liaison = new CFactureLiaison();
        $liaison->object_id     = $this->_id;
        $liaison->object_class  = $this->_class;
        $liaison->facture_id    = $facture->_id;
        $liaison->facture_class = "CFactureEtablissement";
        //Store de la table de liaison entre facture et s�jour
        if ($msg = $liaison->store()) {
          return $msg;
        }
      }
    }

    if ($patient_modified) {
      $list_backrefs = array("contextes_constante", "deliveries", "consultations");
      foreach ($list_backrefs as $_backname) {
        /** @var CConstantesMedicales[]|CProductDelivery[]|CConsultation[] $backobjects */
        $backobjects = $this->loadBackRefs($_backname);
        if (!$backobjects) {
          continue;
        }
        foreach ($backobjects as $_object) {
          if ($_object->patient_id == $this->patient_id) {
            continue;
          }
          $_object->patient_id = $this->patient_id;
          if ($_object instanceof CConsultation) {
            $_object->_skip_count = true;
          }
          if ($msg = $_object->store()) {
            CAppUI::setMsg($msg, UI_MSG_WARNING);
          }
        }
      }
    }

    // Cas d'une annulation de s�jour
    if ($this->annule) {
      if ($msg = $this->delAffectations()) {
        return $msg;
      }
      if ($msg = $this->cancelOperations()) {
        return $msg;
      }
    }

    // Synchronisation des affectations
    if (!$this->_no_synchro && !($this->type == "seances")) {
      $this->loadRefsAffectations();
      $firstAff =& $this->_ref_first_affectation;
      $lastAff =& $this->_ref_last_affectation;

      // Cas o� on a une premiere affectation diff�rente de l'heure d'admission
      if ($firstAff->_id && ($firstAff->entree != $this->_entree)) {
        $firstAff->entree = $this->_entree;
        $firstAff->_no_synchro = 1;
        $firstAff->store();
      }

      // Cas o� on a une derni�re affectation diff�rente de l'heure de sortie
      if ($lastAff->_id && ($lastAff->sortie != $this->_sortie)) {
        $lastAff->sortie = $this->_sortie;
        $lastAff->_no_synchro = 1;
        $lastAff->store();
      }

      //si le sejour a une sortie ==> compl�ter le champ effectue de la derniere affectation
      if ($lastAff->_id) {
        $this->_ref_last_affectation->effectue = $this->sortie_reelle ? 1 : 0;
        $this->_ref_last_affectation->store();
      }
    }

    // Unique affectation de lit
    if ($this->_unique_lit_id) {
      // Une affectation maximum
      if (count($this->_ref_affectations) > 1) {
        foreach ($this->_ref_affectations as $_affectation) {
          if ($msg = $_affectation->delete()) {
            return "Impossible de supprimer une ancienne affectation: $msg";
          }
        }
      }

      // Affectation unique sur le lit
      $this->loadRefsAffectations();
      $unique = $this->_ref_first_affectation;
      $unique->sejour_id = $this->_id;
      $unique->entree = $this->_entree;
      $unique->sortie = $this->_sortie;
      $unique->lit_id = $this->_unique_lit_id;
      if ($msg = $unique->store()) {
        return "Impossible d'affecter un lit unique: $msg";
      }
    }

    // G�n�ration du NDA ?
    if ($this->_generate_NDA) {
      // On ne synchronise pas un s�jour d'urgences qui est un reliquat
      $rpu = $this->loadRefRPU();
      if ($rpu && $rpu->mutation_sejour_id && ($rpu->sejour_id != $rpu->mutation_sejour_id)) {
        return null;
      }

      if ($msg = $this->generateNDA()) {
        return $msg;
      }
    }

    return null;
  }

  /**
   *  R�cup�ration des factures du s�jour
   *
   * @return null|CFactureEtablissement[]
   */
  function loadRefsFactureEtablissement(){
    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      $ljoin = array();
      $ljoin["facture_liaison"] = "facture_liaison.facture_id = facture_etablissement.facture_id";
      $where = array();
      $where["facture_liaison.facture_class"]    = " = 'CFactureEtablissement'";
      $where["facture_liaison.object_class"] = " = 'CSejour'";
      $where["facture_liaison.object_id"]    = " = '$this->_id'";

      $facture = new CFactureEtablissement();
      $this->_ref_factures = $facture->loadList($where, "ouverture ASC", null, "facture_id", $ljoin);
      if (count($this->_ref_factures) > 0) {
        $this->_ref_last_facture = end($this->_ref_factures);
        $this->_ref_last_facture->loadRefsReglements();
        foreach ($this->_ref_factures as $_facture) {
          /* @var CFacture $_facture*/
          $_facture->loadRefAssurance();
        }
      }
      else {
        $this->_ref_last_facture = new CFactureEtablissement();
      }
      return $this->_ref_factures;
    }

    return null;
  }

  /**
   * Generate NDA
   *
   * @return null|string Error message if not null
   */
  function generateNDA() {
    $group = CGroups::loadCurrent();
    if (!$group->isNDASupplier()) {
      return null;
    }

    $this->loadNDA($group->_id);
    if ($this->_NDA) {
      return null;
    }

    if (!$NDA = CIncrementer::generateIdex($this, self::getTagNDA($group->_id), $group->_id)) {
      return CAppUI::tr("CIncrementer_undefined");
    }

    return null;
  }

  /**
   * Delete affectations
   *
   * @return null|string Store-like message
   */
  function delAffectations() {
    $this->loadRefsAffectations();

    $msg = null;
    // Module might not be active
    if ($this->_ref_affectations) {
      foreach ($this->_ref_affectations as $key => $value) {
        $msg .= $this->_ref_affectations[$key]->deleteOne();
      }
    }

    return $msg;
  }

  /**
   * Cancel all ope
   *
   * @return null|string
   */
  function cancelOperations() {
    $this->loadRefsOperations();

    $msg = null;
    foreach ($this->_ref_operations as $key => $value) {
      $value->annulee = 1;
      $msg .= $this->_ref_operations[$key]->store();
    }

    return $msg;
  }

  /**
   * @see parent::getActeExecution()
   */
  function getActeExecution() {
    $this->updateFormFields();
  }

  /**
   * Update mixed entree and sortie
   *
   * @deprecated
   * @return void
   */
  function updateEntreeSortie() {
    $this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
  }

  /**
   * Update date isolement within sejour bounds
   *
   * @return void
   */
  function updateIsolement() {
    $this->_isolement_date = CValue::first($this->isolement_date, $this->_entree);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->updateEntreeSortie();

    if (CAppUI::conf("dPplanningOp CSejour systeme_isolement") == "expert") {
      $this->updateIsolement();
    }

    // Dur�es
    if (CMbDT::date($this->entree_prevue) == CMbDT::date($this->sortie_prevue)) {
      $this->_duree_prevue = 0;
    }
    else {
      $this->_duree_prevue       = CMbDT::daysRelative($this->entree_prevue, $this->sortie_prevue);
    }
    if ($this->_duree_prevue_heure) {
      $this->_duree_prevue_heure = CMbDT::timeRelative(CMbDT::time($this->entree_prevue), CMbDT::time($this->sortie_prevue), "%02d");
    }
    $this->_duree_reelle       = CMbDT::daysRelative($this->entree_reelle, $this->sortie_reelle);
    $this->_duree              = CMbDT::daysRelative($this->_entree, $this->_sortie);

    // Dates
    $this->_date_entree_prevue = CMbDT::date(null, $this->entree_prevue);
    $this->_date_sortie_prevue = CMbDT::date(null, $this->sortie_prevue);

    // Horaires
    // @todo: A supprimer
    $this->_time_entree_prevue = CMbDT::format($this->entree_prevue, "%H:%M:00");
    $this->_time_sortie_prevue = CMbDT::format($this->sortie_prevue, "%H:%M:00");
    $this->_hour_entree_prevue = CMbDT::format($this->entree_prevue, "%H");
    $this->_hour_sortie_prevue = CMbDT::format($this->sortie_prevue, "%H");
    $this->_min_entree_prevue  = CMbDT::format($this->entree_prevue, "%M");
    $this->_min_sortie_prevue  = CMbDT::format($this->sortie_prevue, "%M");

    switch (CAppUI::conf("dPpmsi systeme_facturation")) {
      case "siemens" :
        $this->_guess_NDA = CMbDT::format($this->entree_prevue, "%y");
        $this->_guess_NDA .=
          $this->type == "exte" ? "5" :
          $this->type == "ambu" ? "4" : "0";
        $this->_guess_NDA .="xxxxx";
        break;
      default:
        $this->_guess_NDA = "-";
    }
    $this->_at_midnight = ($this->_date_entree_prevue != $this->_date_sortie_prevue);

    if ($this->entree_prevue && $this->sortie_prevue) {
      $this->_view      = "S�jour du " . CMbDT::format($this->_entree, CAppUI::conf("date"));
      $this->_shortview = "Du "        . CMbDT::format($this->_entree, CAppUI::conf("date"));
      if (CMbDT::format($this->_entree, CAppUI::conf("date")) != CMbDT::format($this->_sortie, CAppUI::conf("date"))) {
        $this->_view      .= " au " . CMbDT::format($this->_sortie, CAppUI::conf("date"));
        $this->_shortview .= " au " . CMbDT::format($this->_sortie, CAppUI::conf("date"));
      }
    }
    $this->_acte_execution = CMbDT::dateTime($this->entree_prevue);

    $this->_praticien_id = $this->praticien_id;

    $this->_adresse_par = ($this->etablissement_entree_id || $this->adresse_par_prat_id);

    if ($this->_adresse_par) {
      $medecin_adresse_par = new CMedecin();
      $medecin_adresse_par->load($this->adresse_par_prat_id);
      $this->_adresse_par_prat = $medecin_adresse_par->_view;

      $etab = new CEtabExterne();
      $etab->load($this->etablissement_entree_id);
      $this->_ref_etablissement_provenance = $etab->_view;
    }

    // Etat d'un sejour : encours, cl�tur� ou preadmission
    $this->_etat = "preadmission";
    if ($this->entree_reelle) {
      $this->_etat = "encours";
    }
    if ($this->sortie_reelle) {
      $this->_etat = "cloture";
    }

    // Motif complet du s�jour
    $this->_motif_complet .= $this->libelle;
    $this->_motif_complet = "";
    if ($this->recuse == -1) {
      $this->_motif_complet .= "[Att] ";
    }
    $this->_motif_complet .= $this->libelle;

    if (!$this->annule && $this->recuse == -1) {
      $this->_view = "[Att] " . $this->_view;
    }

    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      $this->loadRefsFactureEtablissement();
      if ($this->_ref_last_facture) {
        $facture = $this->_ref_last_facture;
        $this->_type_sejour     = $facture->type_facture;
        $this->_statut_pro      = $facture->statut_pro;
        $this->_dialyse         = $facture->dialyse;
        $this->_cession_creance = $facture->cession_creance;
        $this->_assurance_maladie         = $facture->assurance_maladie;
        $this->_assurance_accident        = $facture->assurance_accident;
        $this->_rques_assurance_maladie   = $facture->rques_assurance_maladie;
        $this->_rques_assurance_accident  = $facture->rques_assurance_accident;
      }
    }
  }

  function checkDaysRelative($date) {
    if ($this->_entree && $this->_sortie) {
      $this->_entree_relative = CMbDT::daysRelative($date, CMbDT::date($this->_entree));
      $this->_sortie_relative = CMbDT::daysRelative($date, CMbDT::date($this->_sortie));
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    // Annulation / R�cusation
    $this->completeField("annule", "recuse");
    $annule = $this->annule;
    if ($this->fieldModified("recuse", "1")) {
      $annule = "1";
    }
    if ($this->fieldModified("recuse", "0")) {
      $annule = "0";
    }
    if ($this->fieldModified("recuse", "-1")) {
      $annule = "0";
    }
    $this->annule = $annule;

    // D�tail d'horaire d'entr�e, ne pas comparer la date_entree_prevue � null
    // @todo Passer au TimePicker
    if ($this->_date_entree_prevue && $this->_hour_entree_prevue !== null && $this->_min_entree_prevue !== null) {
      $this->entree_prevue = "$this->_date_entree_prevue";
      $this->entree_prevue.= " ".str_pad($this->_hour_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":".str_pad($this->_min_entree_prevue , 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":00";
    }

    // D�tail d'horaire de sortie, ne pas comparer la date_sortie_prevue � null
    // @todo Passer au TimePicker
    if ($this->_date_sortie_prevue && $this->_hour_sortie_prevue !== null && $this->_min_sortie_prevue !== null) {
      $this->sortie_prevue = "$this->_date_sortie_prevue";
      $this->sortie_prevue.= " ".str_pad($this->_hour_sortie_prevue, 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":".str_pad($this->_min_sortie_prevue , 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":00";
    }

    $this->completeField('entree_prevue', 'sortie_prevue', 'entree_reelle', 'sortie_reelle', 'type');

    // Signaler l'action de validation de la sortie
    if ($this->_modifier_sortie === '1') {
      $this->sortie_reelle = CMbDT::dateTime();
    }

    if ($this->_modifier_sortie === '0') {
      $this->sortie_reelle = "";
    }

    // Signaler l'action de validation de l'entr�e
    if ($this->_modifier_entree === '1') {
      $this->entree_reelle = CMbDT::dateTime();
    }

    if ($this->_modifier_entree === '0') {
      $this->entree_reelle = "";
    }

    // Affectation de la date d'entr�e pr�vue si on a la date d'entr�e r�elle
    if ($this->entree_reelle && !$this->entree_prevue) {
      $this->entree_prevue = $this->entree_reelle;
    }

    // Affectation de la date de sortie pr�vue si on a la date de sortie r�elle
    if ($this->sortie_reelle && !$this->sortie_prevue) {
      $this->sortie_prevue = $this->sortie_reelle;
    }

    // Nouveau s�jour reli� � une grossesse
    // Si l'entr�e pr�vue est � l'heure courante, alors on value �galement l'entr�e r�elle
    if (CModule::getActive("maternite") && !$this->_id && $this->grossesse_id) {
      $current_hour = CMbDT::transform(CMbDT::time(), null, "%H");
      $hour_sejour = $this->_hour_entree_prevue;

      if (CMbDT::date() == $this->_date_entree_prevue && $current_hour == $hour_sejour) {
        $this->entree_reelle = CMbDT::dateTime();
      }
    }

    //@TODO : mieux g�rer les current et now dans l'updatePlainFields et le store
    $entree_reelle = ($this->entree_reelle === 'current'|| $this->entree_reelle ===  'now') ? CMbDT::dateTime() : $this->entree_reelle;
    if ($entree_reelle && ($this->sortie_prevue < $entree_reelle)) {
      $this->sortie_prevue = $this->type == "comp" ? CMbDT::dateTime("+1 DAY", $entree_reelle) : $entree_reelle;
    }

    // Synchro dur�e d'hospi / type d'hospi
    $this->_at_midnight = (CMbDT::date(null, $this->entree_prevue) != CMbDT::date(null, $this->sortie_prevue));
    if ($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    }
    elseif (!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
    }

    // Has to be donne once entree / sortie - reelle / prevue is not modified
    $this->entree = $this->entree_reelle ? $this->entree_reelle : $this->entree_prevue;
    $this->sortie = $this->sortie_reelle ? $this->sortie_reelle : $this->sortie_prevue;
  }

  /**
   * Count sejours including a specific date
   *
   * @param string $date     Date to check for inclusion
   * @param array  $where    Array of additional where clauses
   * @param array  $leftjoin Array of left join clauses
   *
   * @return int Count null if module is not installed
   */
  static function countForDate($date, $where = null, $leftjoin = null) {
    $where[] = "sejour.entree <= '$date 23:59:59'";
    $where[] = "sejour.sortie >= '$date 00:00:00'";
    $sejour = new CSejour;
    return $sejour->countList($where, null, $leftjoin);
  }

  /**
   * Count sejours including a specific date
   *
   * @param string $datetime Date to check for inclusion
   * @param array  $where    Array of additional where clauses
   * @param array  $leftjoin Array of left join clauses
   *
   * @return int Count null if module is not installed
   */
  static function countForDateTime($datetime, $where = null, $leftjoin = null) {
    $where[] = "sejour.entree <= '$datetime'";
    $where[] = "sejour.sortie >= '$datetime'";
    $sejour = new CSejour;
    return $sejour->countList($where, null, $leftjoin);
  }

  /**
   * Load sejours including a specific date
   *
   * @param date   $date  Date to check for inclusion
   * @param array  $where Array of additional where clauses
   * @param array  $order Array of order fields
   * @param string $limit MySQL limit clause
   * @param array  $group Array of group by clauses
   * @param array  $ljoin Array of left join clauses
   *
   * @return self[] List of found sejour, null if module is not installed
   */
  static function loadListForDate($date, $where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    $where[] = "sejour.entree <= '$date 23:59:59'";
    $where[] = "sejour.sortie >= '$date 00:00:00'";
    $sejour = new CSejour;
    return $sejour->loadList($where, $order, $limit, $group, $ljoin);
  }

  /**
   * Load sejours including a specific datetime
   *
   * @param datetime $datetime Datetime to check for inclusion
   * @param array    $where    Array of additional where clauses
   * @param array    $order    Array of order fields
   * @param string   $limit    MySQL limit clause
   * @param array    $group    Array of group by clauses
   * @param array    $ljoin    Array of left join clauses
   *
   * @return self[] List of found sejour, null if module is not installed
   */
  static function loadListForDateTime($datetime, $where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    $where[] = "sejour.entree <= '$datetime'";
    $where[] = "sejour.sortie >= '$datetime'";
    $sejour = new CSejour;
    return $sejour->loadList($where, $order, $limit, $group, $ljoin);
  }

  /**
   * @see parent::getTemplateClasses()
   */
  function getTemplateClasses() {
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects li�s au s�jour
    $tab['CSejour'] = $this->_id;
    $tab['CPatient'] = $this->_ref_patient->_id;

    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;
    $tab['COperation'] = 0;

    return $tab;
  }

  /**
   * Calcul des droits CMU pour la duree totale du sejour
   *
   * @return void
   */
  function getDroitsCMU() {
    if ($this->_date_sortie_prevue <= $this->_ref_patient->fin_amo && $this->_ref_patient->cmu) {
      $this->_couvert_cmu = 1;
    }
    else {
      $this->_couvert_cmu = 0;
    }
    if ($this->_date_sortie_prevue <= $this->_ref_patient->fin_amo && $this->_ref_patient->ald) {
      $this->_couvert_ald = 1;
    }
    else {
      $this->_couvert_ald = 0;
    }
  }

  /**
   * @see parent::loadRefSejour()
   */
  function loadRefSejour() {
    return $this->_ref_sejour =& $this;
  }

  /**
   * Load current affectation relative to a date
   *
   * @param datetime $datetime   Reference datetime, now if null
   * @param ref      $service_id Service filter
   *
   * @return CAffectation
   */
  function loadRefCurrAffectation($datetime = null, $service_id = null) {
    if (!$datetime) {
      $datetime = CMbDT::dateTime();
    }

    $affectation = new CAffectation();
    $where = array();
    $where["sejour_id"] = " = '$this->_id'";
    if ($service_id) {
      $where["service_id"] = " = '$service_id'";
    }
    $where[] = "'$datetime' BETWEEN entree AND sortie";
    $affectation->loadObject($where);
    $affectation->loadRefLit()->loadCompleteView();

    return  $this->_ref_curr_affectation = $affectation;
  }


  /**
   * Load surrounding affectations
   *
   * @param string $date $date Current date, now if null
   *
   * @return CAffectation[] Affectations array with curr, prev and next keys
   */
  function loadSurrAffectations($date = null) {
    if (!$date) {
      $date = CMbDT::dateTime();
    }

    // Current affectation
    $affectations = array();
    $affectations["curr"] = $this->loadRefCurrAffectation($date);

    // Previous affection
    $affectation = new CAffectation();
    $where = array();
    $where["sortie"] = " < '$date'";
    $where["sejour_id"] = " = '$this->_id'";
    $affectation->loadObject($where);
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $affectations["prev"] = $this->_ref_prev_affectation = $affectation;

    // Next affectation
    $affectation = new CAffectation();
    $where = array();
    $where["entree"] = "> '$date'";
    $where["sejour_id"] = " = '$this->_id'";
    $affectation->loadObject($where);
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $affectations["next"] = $this->_ref_next_affectation = $affectation;

    return $affectations;
  }

  /**
   * Charge le dossier m�dical
   *
   * @return CDossierMedical
   */
  function loadRefDossierMedical() {
    return $this->_ref_dossier_medical = $this->loadUniqueBackRef("dossier_medical");
  }

  /**
   * Charge l'�tablissement externe de provenance
   *
   * @return CEtabExterne
   */
  function loadRefEtablissementProvenance() {
    return $this->_ref_etablissement_provenance = $this->loadFwdRef("etablissement_entree_id", true);
  }

  /**
   * Charge l'�tablissement externe de transfert
   *
   * @return CEtabExterne
   */
  function loadRefEtablissementTransfert() {
    return $this->_ref_etablissement_transfert = $this->loadFwdRef("etablissement_sortie_id", true);
  }

  /**
   * Charge le service de mutation
   *
   * @return CService
   */
  function loadRefServiceMutation() {
    return $this->_ref_service_mutation = $this->loadFwdRef("service_sortie_id", true);
  }

  /**
   * Charge l'indicateur de prix
   *
   * @return CChargePriceIndicator
   */
  function loadRefChargePriceIndicator() {
    return $this->_ref_charge_price_indicator = $this->loadFwdRef("charge_id", true);
  }

  /**
   * Charge le mode d'entr�e
   *
   * @return CModeEntreeSejour
   */
  function loadRefModeEntree() {
    return $this->_ref_mode_entree = $this->loadFwdRef("mode_entree_id", true);
  }

  /**
   * Charge le mode de sortie
   *
   * @return CModeSortieSejour
   */
  function loadRefModeSortie() {
    return $this->_ref_mode_sortie = $this->loadFwdRef("mode_sortie_id", true);
  }

  /**
   * Compte les observations de visite du praticien responsable
   *
   * @param date $date A une date donn�e, maintenant si null
   *
   * @return int
   */
  function countNotificationVisite($date = null) {
    if (!$date) {
      $date = CMbDT::date();
    }

    $this->completeField("praticien_id");
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->_id'";
    $where["user_id"]  = " = '$this->praticien_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    return $observation->countList($where);
  }

  /**
   * Charge le patient
   *
   * @param bool $cache Utilise le cache
   *
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
    $this->getDroitsCMU();

    // View
    if (substr($this->_view, 0, 9) == "S�jour du") {
      $this->_view = $this->_ref_patient->_view . " - " . $this->_view;
    }

    return $this->_ref_patient;
  }

  /**
   * Charge le praticien responsable
   *
   * @param bool $cache Utiliser le cache
   *
   * @return CMediusers
   */
  function loadRefPraticien($cache = true) {
    /** @var CMediusers $praticien */
    $praticien = $this->loadFwdRef("praticien_id", $cache);
    $this->_ref_executant = $praticien;
    $praticien->loadRefFunction();
    return $this->_ref_praticien = $praticien;
  }

  /**
   * Charge les diagnostics CIM principal et reli�
   *
   * @return void
   */
  function loadExtDiagnostics() {
    $this->_ext_diagnostic_principal = $this->DP ? CCodeCIM10::get($this->DP) : null;
    $this->_ext_diagnostic_relie     = $this->DR ? CCodeCIM10::get($this->DR) : null;
  }

  /**
   * Charge les diagnostics CIM associ�s
   *
   * @param bool $split Notation fran�aise avec le point s�parateur apr�s trois caract�res
   *
   * @return string[] Codes CIM
   */
  function loadDiagnosticsAssocies($split = true) {
    $this->_diagnostics_associes = array();
    if ($this->_ref_dossier_medical->_id) {
      foreach ($this->_ref_dossier_medical->_codes_cim as $code) {
        if ($split && strlen($code) >= 4) {
          $this->_diagnostics_associes[] = substr($code, 0, 3).".".substr($code, 3);
        }
        else {
          $this->_diagnostics_associes[] = $code;
        }
      }
    }

    return $this->_diagnostics_associes;
  }

  /**
   * Charge le niveau de prestation principal
   *
   * @return CPrestation
   */
  function loadRefPrestation() {
    return $this->_ref_prestation = $this->loadFwdRef("prestation_id", true);
  }

  /**
   * Charge les transmissions du s�jour
   *
   * @param bool $cible_importante Filtrer sur les cibles importantes
   * @param bool $important        Filtrer sur le degr� important
   * @param bool $macro_cible      N'utiliser que les macrocible (uniquement pour les cibles importantes)
   * @param null $limit            Limite SQL
   *
   * @return array|CStoredObject[]|null
   */
  function loadRefsTransmissions($cible_importante = false, $important = false, $macro_cible = false, $limit = null) {
    $this->_ref_transmissions = array();

    // Chargement des dernieres transmissions des cibles importantes
    if ($cible_importante) {
      $transmission = new CTransmissionMedicale();
      $ljoin = array();
      $ljoin["category_prescription"] = "category_prescription.category_prescription_id = transmission_medicale.object_id";

      $where = array();
      $where["object_class"] = " = 'CCategoryPrescription'";
      $where["sejour_id"] = " = '$this->_id'";
      $where["category_prescription.cible_importante"] = " = '1'";

      if ($macro_cible) {
        $where["category_prescription.only_cible"] = " = '1'";
      }

      $order = "date DESC";
      $this->_ref_transmissions = $transmission->loadList($where, $order, $limit, null, $ljoin);
    }

    // Chargement des transmissions de degr� important
    if ($important) {
      $transmission = new CTransmissionMedicale;
      $where = array();
      $where["sejour_id"] =  "= '$this->_id'";
      $order = "date DESC";
      $where["degre"] = " = 'high'";

      $this->_ref_transmissions = $this->_ref_transmissions + $transmission->loadList($where, $order, $limit);
    }

    if (!$cible_importante && !$important) {
      $this->_ref_transmissions = $this->loadBackRefs("transmissions");
    }

    return $this->_ref_transmissions;
  }

  /**
   * Charge les observations du s�jour
   *
   * @param bool $important Filtrer les observations importantes
   *
   * @return CObservationMedicale[]
   */
  function loadRefsObservations($important = false) {
    $order = "date DESC";

    if ($important) {
      $obs = new CObservationMedicale;
      $where = array();
      $where["sejour_id"] = " = '$this->_id'";
      $where["degre"]     = " = 'high'";
      return $this->_ref_observations = $obs->loadList($where, $order);
    }

    return $this->_ref_observations = $this->loadBackRefs("observations", $order);
  }

  /**
   * Comptes les t�ches en court et r�alis�es
   *
   * @return int|null
   */
  function countTasks() {
    $where["realise"] = "!= '1'";
    $this->_count_pending_tasks =  $this->countBackRefs("tasks", $where);
    return $this->_count_tasks = $this->countBackRefs("tasks");
  }

  /**
   * Charge les t�ches d'un s�jour
   *
   * @return CSejourTask[]
   */
  function loadRefsTasks() {
    $this->_ref_tasks = $this->loadBackRefs("tasks", 'sejour_id ASC');

    return $this->_ref_tasks;
  }

  /**
   * Charge les examens IGS
   *
   * @return CExamIgs[]
   */
  function loadRefsExamsIGS() {
    return $this->_ref_exams_igs = $this->loadBackRefs("exams_igs");
  }

  /**
   * Charge tout le suivi m�dical, compos� d'observations, transmissions, consultations et prescriptions
   *
   * @param date $datetime_min Date de r�f�rence � partir de laquelle filtrer
   *
   * @return array
   */
  function loadSuiviMedical($datetime_min = null) {
    if ($datetime_min) {
      $trans = new CTransmissionMedicale();
      $whereTrans = array();
      $whereTrans[] = "(degre = 'high' AND (date_max IS NULL OR date_max >= '$datetime_min')) OR (date >= '$datetime_min')";
      $whereTrans["sejour_id"] = " = '$this->_id'";
      $this->_back["transmissions"] = $trans->loadList($whereTrans);

      $obs = new CObservationMedicale();
      $whereObs = array();
      $whereObs[] = "(degre = 'high') OR (date >= '$datetime_min')";
      $whereObs["sejour_id"] = " = '$this->_id'";
      $this->_back["observations"] = $obs->loadList($whereObs);
    }
    else {
      $this->loadBackRefs("observations");
      $this->loadBackRefs("transmissions");
    }

    $consultations = $this->loadRefsConsultations();
    $consultations_patient = $this->loadRefPatient()->loadRefsConsultations();

    $this->_ref_suivi_medical = array();

    if (isset($this->_back["observations"])) {
      foreach ($this->_back["observations"] as $curr_obs) {
        /** @var CObservationMedicale $curr_obs */
        $curr_obs->loadRefsFwd();
        $curr_obs->_ref_user->loadRefFunction();
        $this->_ref_suivi_medical[$curr_obs->date.$curr_obs->_id."obs"] = $curr_obs;
      }
    }

    if (isset($this->_back["transmissions"])) {
      foreach ($this->_back["transmissions"] as $curr_trans) {
        /** @var CTransmissionMedicale $curr_trans */
        $curr_trans->loadRefsFwd();
        if ($curr_trans->_ref_object instanceof CAdministration) {
          $curr_trans->_ref_object->loadRefsFwd();
        }

        $this->_ref_suivi_medical[$curr_trans->date.$curr_trans->_id."trans"][] = $curr_trans;
      }
    }

    foreach ($consultations as $_consultation) {
      $_consultation->canEdit();
      $_consultation->loadRefConsultAnesth();
      foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
        $_dossier_anesth->loadRefsTechniques();
      }
      $_consultation->loadRefPlageConsult();
      $_consultation->loadRefPraticien()->loadRefFunction();
      $this->_ref_suivi_medical[$_consultation->_datetime] = $_consultation;
    }

    // Ajout des consultations d'anesth�sie hors s�jour
    foreach ($consultations_patient as $_consultation) {
      $_consultation->loadRefConsultAnesth();
      if (!count($_consultation->_refs_dossiers_anesth)) {
        continue;
      }
      foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
        $_dossier_anesth->loadRefOperation();
        $_dossier_anesth->loadRefsTechniques();
      }
      $_consultation->loadRefPlageConsult();
      $_consultation->loadRefPraticien()->loadRefFunction();
      $this->_ref_suivi_medical[$_consultation->_datetime] = $_consultation;
    }

    if (
        CModule::getActive("dPprescription") &&
        $this->type == "urg" &&
        CAppUI::conf("dPprescription CPrescription prescription_suivi_soins", CGroups::loadCurrent())
    ) {
      $this->loadRefPrescriptionSejour();
      $prescription = $this->_ref_prescription_sejour;

      // Chargement des lignes de prescriptions d'elements
      $prescription->loadRefsLinesElement();
      $prescription->loadRefsLinesAllComments();

      foreach ($prescription->_ref_prescription_lines_all_comments as $_comment) {
        $_comment->canEdit();
        $_comment->countBackRefs("transmissions");
        $this->_ref_suivi_medical["$_comment->debut $_comment->time_debut $_comment->_guid"] = $_comment;
      }

      // Ajout des lignes de prescription dans la liste du suivi de soins
      foreach ($prescription->_ref_prescription_lines_element as $_line_element) {
        $_line_element->canEdit();
        $_line_element->countBackRefs("transmissions");
        $this->_ref_suivi_medical["$_line_element->debut $_line_element->time_debut $_line_element->_guid"] = $_line_element;
      }
    }

    krsort($this->_ref_suivi_medical);
    return $this->_ref_suivi_medical;
  }

  /**
   * Charge toutes les constantes m�dicales et l'ajoute au suivi m�dical
   *
   * @param ref $user_id Filtrer sur les cr�ateur de la ligne
   *
   * @return CMbObject[]
   */
  function loadRefConstantes($user_id = null) {
    /** @var CConstantesMedicales[] $constantes */
    $constantes = $this->loadListConstantesMedicales();
    foreach ($constantes as $_const) {
      $_const->loadRefUser();
      if ($_const->context_class != "CSejour" || $_const->context_id != $this->_id) {
        unset($constantes[$_const->_id]);
      }
      if ($user_id) {
        $first_log = $_const->loadFirstLog();
        $first_log->loadRefUser();
        if ($first_log->_ref_user->_id != $user_id) {
          unset($constantes[$_const->_id]);
        }
      }
    }

    if (!$this->_ref_suivi_medical) {
      $this->_ref_suivi_medical = array();
    }

    $this->_ref_suivi_medical = array_merge($constantes, $this->_ref_suivi_medical);
  }

  /**
   * Load associated Group
   *
   * @return CGroups
   */
  function loadRefEtablissement() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * Load associated RPU
   *
   * @return CRPU
   */
  function loadRefRPU() {
    return $this->_ref_rpu = $this->loadUniqueBackRef("rpu");
  }

  /**
   * Load associated BilanSSR
   *
   * @return CBilanSSR
   */
  function loadRefBilanSSR() {
    return $this->_ref_bilan_ssr = $this->loadUniqueBackRef("bilan_ssr");
  }

  /**
   * Charge la fiche d'autonomie associ�
   *
   * @return CFicheAutonomie
   */
  function loadRefFicheAutonomie() {
    return $this->_ref_fiche_autonomie = $this->loadUniqueBackRef("fiche_autonomie");
  }

  /**
   * Charge le praticien adressant
   *
   * @return CMedecin
   */
  function loadRefAdresseParPraticien() {
    return $this->_ref_adresse_par_prat = $this->loadFwdRef("adresse_par_prat_id", true);
  }

  /**
   * Charge le dossier d'anesth�sie associ� au s�jour
   *
   * @return CConsultAnesth
   */
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return $this->_ref_consult_anesth;
    }

    return $this->_ref_consult_anesth = $this->loadFirstBackRef("consultations_anesths", "consultation_anesth_id ASC");
  }

  /**
   * Charge les consultations, en particulier l'ATU dans le cas UPATOU
   *
   * @return CConsultation[]
   */
  function loadRefsConsultations() {
    $this->_ref_consultations = $this->loadBackRefs("consultations");

    $this->_ref_consult_atu = new CConsultation();

    foreach ($this->_ref_consultations as $_consult) {
      /** @var CConsultation $_consult */
      $praticien = $_consult->loadRefPraticien();
      $praticien->loadRefFunction();
      $_consult->canDo();
      if ($praticien->isUrgentiste() && ($this->countBackRefs("rpu") > 0 || !CAppUI::conf("dPurgences create_sejour_hospit"))) {
        $this->_ref_consult_atu = $_consult;
        $this->_ref_consult_atu->countDocItems();
        break;
      }
    }

    return $this->_ref_consultations;
  }

  /**
   * Chargement de toutes les prescriptions li�es au sejour (object_class CSejour)
   *
   * @return CPrescription[]
   */
  function loadRefsPrescriptions() {
    $prescriptions = $this->loadBackRefs("prescriptions");
    // Si $prescriptions n'est pas un tableau, module non install�
    if (!is_array($prescriptions)) {
      $this->_ref_last_prescription = null;
      return null;
    }
    $this->_count_prescriptions = count($prescriptions);
    $this->_ref_prescriptions["pre_admission"] = new CPrescription();
    $this->_ref_prescriptions["sejour"] = new CPrescription();
    $this->_ref_prescriptions["sortie"] = new CPrescription();

    // Stockage des prescriptions par type
    foreach ($prescriptions as $_prescription) {
      $this->_ref_prescriptions[$_prescription->type] = $_prescription;
    }

    return $this->_ref_prescriptions;
  }

  /**
   * Chargement de la prescription d'hospitalisation
   *
   * @return CPrescription
   */
  function loadRefPrescriptionSejour() {
    if (!CModule::getActive("dPprescription")) {
      return null;
    }

    $this->_ref_prescription_sejour = new CPrescription();
    $this->_ref_prescription_sejour->object_class = "CSejour";
    $this->_ref_prescription_sejour->object_id = $this->_id;
    $this->_ref_prescription_sejour->type = "sejour";
    $this->_ref_prescription_sejour->loadMatchingObject();

    return $this->_ref_prescription_sejour;
  }

  /**
   * Chargement de l'ensemble des prescripteurs
   *
   * @return CMediusers[]
   */
  function loadRefsPrescripteurs() {
    $this->_ref_prescripteurs = array();
    $this->loadRefsPrescriptions();
    foreach ($this->_ref_prescriptions as $_prescription) {
      $_prescription->getPraticiens();
      if (is_array($_prescription->_praticiens)) {
        foreach ($_prescription->_praticiens as $_praticien_id => $_praticien_view) {
          if (!is_array($this->_ref_prescripteurs) || !array_key_exists($_praticien_id, $this->_ref_prescripteurs)) {
            $praticien = new CMediusers();
            $this->_ref_prescripteurs[$_praticien_id] = $praticien->load($_praticien_id);
          }
        }
      }
    }

    return $this->_ref_prescripteurs;
  }

  /**
   * Chargement des remplacements pour ce s�jour
   *
   * @return CReplacement[]
   */
  function loadRefReplacements(){
    return $this->_ref_replacements = $this->loadBackRefs("replacements");
  }

  /**
   * Chargement du remplacement
   *
   * @param int $conge_id le cong�
   *
   * @return CReplacement
   */
  function loadRefReplacement($conge_id) {
    $this->_ref_replacement = new CReplacement;
    $this->_ref_replacement->sejour_id = $this->_id;
    $this->_ref_replacement->conge_id = $conge_id;
    $this->_ref_replacement->loadMatchingObject();
    return $this->_ref_replacement;
  }

  /**
   * Chargement de la grossesse associ�e
   *
   * @return CGrossesse
   */
  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id", true);
  }

  /**
   * Cherche si utilisateur est remplacant pour le s�jour
   *
   * @param ref $replacer_id Filtre sur l'utilisateur
   *
   * @return int Nombre de remplacement
   */
  function isReplacer($replacer_id) {
    $replacement = new CReplacement;
    $replacement->sejour_id   = $this->_id;
    $replacement->replacer_id = $replacer_id;
    return $replacement->countMatchingList();
  }

  /**
   * Chargement de constantes m�dicales
   *
   * @param array $where Clauses where
   *
   * @return CConstantesMedicales[]
   */
  function loadListConstantesMedicales($where = array()) {
    if ($this->_list_constantes_medicales) {
      return $this->_list_constantes_medicales;
    }

    $constantes = new CConstantesMedicales();
    $where['context_class'] = " = '$this->_class'";
    $where['context_id']    = " = '$this->_id'";
    $where['patient_id']    = " = '$this->patient_id'";

    return $this->_list_constantes_medicales = $constantes->loadList($where, 'datetime ASC');
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd($cache = true) {
    $this->loadRefPatient($cache);
    $this->loadRefPraticien($cache);
    $this->loadRefEtablissement();
    $this->loadRefEtablissementTransfert();
    $this->loadRefServiceMutation();
    $this->loadExtCodesCCAM();
    $this->loadRefsFactureEtablissement();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_operations as &$operation) {
      $operation->loadRefsFwd();
      $operation->loadBrancardage();
      $operation->_ref_chir->loadRefFunction();
      $operation->_ref_chir->loadRefSpecCPAM();
      $operation->_ref_chir->loadRefDiscipline();
    }
    foreach ($this->_ref_affectations as &$affectation) {
      $affectation->loadRefLit();
      $affectation->_ref_lit->loadCompleteView();
    }

    if ($this->_ref_actes_ccam) {
      foreach ($this->_ref_actes_ccam as &$acte_ccam) {
        $acte_ccam->loadRefsFwd();
      }
    }
    $this->loadExtDiagnostics();

    // Chargement du RPU dans le cas des urgences
    $this->loadRefRPU();
    if ($this->_ref_rpu) {
      $this->_ref_rpu->loadRefSejour();
    }

    $this->loadNDA();

    // Chargement de la consultation anesth pour l'affichage de la fiche d'anesthesie
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();

    $this->loadSuiviMedical();
    $this->_ref_patient->loadRefPhotoIdentite();
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadRefPatient()->loadRefPhotoIdentite();
    $this->loadRefEtablissement();
    $affectations = $this->loadRefsAffectations();

    foreach ($this->loadRefsOperations() as $_operation) {
      $_operation->loadRefChir();
      $_operation->loadRefPlageOp();
    }

    if (is_array($affectations) && count($affectations)) {
      foreach ($affectations as $_affectation) {
        /** @var CAffectation $_affectation */
        if (!$_affectation->lit_id) {
          $_affectation->_view = $_affectation->loadRefService()->_view;
        }
        else {
          $_affectation->loadRefLit()->loadCompleteView();
          $_affectation->_view = $_affectation->_ref_lit->_view;
        }

        $_affectation->loadRefParentAffectation();
      }
    }

    $this->loadNDA();

    if (CModule::getActive("printing")) {
      // Compter les imprimantes pour l'impression d'�tiquettes
      $user_printers = CMediusers::get();
      $function      = $user_printers->loadRefFunction();
      $this->_nb_printers = $function->countBackRefs("printers");
    }

    // On compte les mod�les d'�tiquettes pour :
    // - stream si un seul
    // - modale de choix si plusieurs
    $modele_etiquette = new CModeleEtiquette();
    $modele_etiquette->object_class = "CSejour";
    $modele_etiquette->group_id = $this->group_id;
    $this->_count_modeles_etiq = $modele_etiquette->countMatchingList();
  }

  /**
   * Charge le sejour ayant les traits suivants :
   * - Meme patient
   * - Meme praticien si praticien connu
   * - Date d'entree �quivalente
   *
   * @param bool $strict    Le s�jour this exclu
   * @param bool $notCancel Seulement les non annul�s
   * @param bool $useSortie Filtrer aussi sur la date de sortie
   *
   * @return int|void Nombre d'occurences trouv�es
   */
  function loadMatchingSejour($strict = false, $notCancel = false, $useSortie = true) {
    if ($strict && $this->_id) {
      $where["sejour_id"] = " != '$this->_id'";
    }
    $where["patient_id"] = " = '$this->patient_id'";

    $this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    if ($useSortie) {
      $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
    }

    if (!$this->_entree) {
      return null;
    }

    if ($this->_entree) {
      $date_entree = CMbDT::date($this->_entree);
      $where[] = "DATE(entree_prevue) = '$date_entree' OR DATE(entree_reelle) = '$date_entree'";
    }
    if ($useSortie) {
      if ($this->_sortie) {
        $date_sortie = CMbDT::date($this->_sortie);
        $where[] = "DATE(sortie_prevue) = '$date_sortie' OR DATE(sortie_reelle) = '$date_sortie'";
      }
    }

    if ($notCancel) {
      $where["annule"] = " = '0'";
    }

    if ($this->type) {
      $where["type"] = " = '$this->type'";
    }

    $this->loadObject($where);
    return $this->countList($where);
  }

  /**
   * Construit le tag NDA en fonction des variables de configuration
   *
   * @param int    $group_id Permet de charger le NDA pour un �tablissement donn� si non null
   * @param string $type_tag Permet de sp�cifier le type de tag
   *
   * @return string|void
   */
  static function getTagNDA($group_id = null, $type_tag = "tag_dossier") {
    $context = array(__METHOD__, func_get_args());
    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }

    // Gestion du tag NDA par son domaine d'identification
    if (CAppUI::conf("eai use_domain")) {
      $tag_NDA = CDomain::getTagMasterDomain("CSejour", $group_id);

      if ($type_tag != "tag_dossier") {
        $tag_NDA = CAppUI::conf("dPplanningOp CSejour $type_tag") . $tag_NDA;
      }

      return CFunctionCache::set($context, $tag_NDA);
    }

    $tag_NDA = CAppUI::conf("dPplanningOp CSejour tag_dossier");

    if ($type_tag != "tag_dossier") {
      $tag_NDA = CAppUI::conf("dPplanningOp CSejour $type_tag") . $tag_NDA;
    }

    // Permettre des IPP en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    // Si on est dans le cas d'un �tablissement g�rant la num�rotation
    $group->loadConfigValues();
    if ($group->_configs["smp_idex_generator"]) {
      $tag_NDA = CAppUI::conf("smp tag_nda");
    }

    // Pas de tag Num dossier
    if (null == $tag_NDA) {
      return CFunctionCache::set($context, null);
    }

    // Pr�f�rer un identifiant externe de l'�tablissement
    if ($tag_group_idex = CAppUI::conf("dPplanningOp CSejour tag_dossier_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }

    return CFunctionCache::set($context, str_replace('$g', $group_id, $tag_NDA));
  }

  /**
   * Construit le tag NPA (pr�ad) en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger le NPA pour un �tablissement donn� si non null
   *
   * @return string
   */
  static function getTagNPA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_pa");
  }

  /**
   * Construit le tag NTA (trash) en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger le NTA pour un �tablissement donn� si non null
   *
   * @return string
   */
  static function getTagNTA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_trash");
  }

  /**
   * Construit le tag NRA (rang) en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger le NRA pour un �tablissement donn� si non null
   *
   * @return string
   */
  static function getTagNRA($group_id = null) {
    return self::getTagNDA($group_id, "tag_dossier_rang");
  }

  /**
   * Charge le NDA du s�jour pour l'�tablissement courant
   *
   * @param int $group_id Permet de charger le NDA pour un �tablissement donn� si non null
   *
   * @return void|string
   */
  function loadNDA($group_id = null) {
    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration de num�ro de dossier
    if (null == $tag_NDA = $this->getTagNDA($group_id)) {
      $this->_NDA_view = $this->_NDA = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return null;
    }


    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NDA);

    // Stockage de la valeur de l'id400
    $this->_ref_NDA  = $idex;
    $this->_NDA_view = $this->_NDA = $idex->id400;

    // Cas de l'utilisation du rang
    $this->loadNRA($group_id);

    return null;
  }

  /**
   * Charge le Num�ro de pr�-admission du s�jour pour l'�tablissement courant
   *
   * @param int $group_id Permet de charger le NPA pour un �tablissement donn� si non null
   *
   * @return void|string
   */
  function loadNPA($group_id = null) {
    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration de num�ro de dossier
    if (null == $tag_NPA = $this->getTagNDA($group_id, "tag_dossier_pa")) {
      $this->_NPA = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return null;
    }

    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NPA);

    // Stockage de la valeur de l'id400
    $this->_ref_NPA = $idex;
    $this->_NPA     = $idex->id400;

    return null;
  }

  /**
   * Charge le Num�ro de rang du s�jour pour l'�tablissement courant
   *
   * @param int $group_id Permet de charger le NRA pour un �tablissement donn� si non null
   *
   * @return void|string
   */
  function loadNRA($group_id = null) {
    // Utilise t-on le rang pour le dossier
    if (!CAppUI::conf("dPplanningOp CSejour use_dossier_rang")) {
      return null;
    }

    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }

    // Aucune configuration du numero de rang
    if (null == $tag_NRA = $this->getTagNRA($group_id)) {
      return null;
    }

    // Recuperation de la valeur de l'id400
    $idex = CIdSante400::getMatchFor($this, $tag_NRA);

    // Stockage de la valeur de l'id400
    $this->_ref_NRA = $idex;
    $NRA            = $idex->_id ? $idex->id400 : "-";

    // R�cup�ration de l'IPP du patient
    $this->loadRefPatient();
    $this->_ref_patient->loadIPP();

    $this->_NDA_view = $this->_ref_patient->_IPP."/".$NRA;

    return null;
  }

  /**
   * Charge le s�jour depuis son NDA
   *
   * @param string $nda NDA du s�jour
   *
   * @return void
   */
  function loadFromNDA($nda) {
    // Aucune configuration de num�ro de dossier
    if (null == $tag_NDA = $this->getTagNDA()) {
      return;
    }

    $idDossier               = new CIdSante400();
    $idDossier->id400        = $nda;
    $idDossier->tag          = $tag_NDA;
    $idDossier->object_class = $this->_class;
    $idDossier->loadMatchingObject();

    if ($idDossier->_id) {
      $this->load($idDossier->object_id);
      $this->_NDA = $idDossier->id400;
    }
  }

  /**
   * @see parent::getExecutantId()
   */
  function getExecutantId($code_activite) {
      return $this->praticien_id;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_praticien) {
      $this->loadRefPraticien();
    }
    if (!$this->_ref_group) {
      $this->loadRefEtablissement();
    }
    return (
      $this->_ref_group->getPerm($permType) && $this->_ref_praticien->getPerm($permType) && parent::getPerm($permType)
    );
  }

  /**
   * Charge l'affectation courante
   *
   * @param datetime $dateTime Permet de sp�cifier un horaire de r�f�rences, maintenant si null
   *
   * @todo A d�doublonner avec loadRefCurrAffectation
   * @return CAffectation
   */
  function getCurrAffectation($dateTime = null) {
    if (!$dateTime) {
      $dateTime = CMbDT::dateTime();
    }

    $ds = $this->_spec->ds;

    $where = array();
    $where["sejour_id"] = $ds->prepare("= %", $this->sejour_id);

    if (CMbDT::time(null, $dateTime) == "00:00:00") {
      $where["entree"] = $ds->prepare("<= %", CMbDT::date(null, $dateTime)." 23:59:59");
      $where["sortie"] = $ds->prepare(">= %", CMbDT::date(null, $dateTime)." 00:00:01");
    }
    else {
      $where["entree"] = $ds->prepare("<= %", $dateTime);
      $where["sortie"] = $ds->prepare(">= %", $dateTime);
    }

    $order = "entree";

    $curr_affectation = new CAffectation();
    $curr_affectation->loadObject($where, $order);

    return $curr_affectation;
  }

  /**
   * Chargements des affectations
   *
   * @param string $order order
   *
   * @return CAffectation[]
   */
  function loadRefsAffectations($order = "sortie DESC") {
    $affectations = $this->loadBackRefs("affectations", $order);

    if (count($affectations) > 0) {
      $this->_ref_first_affectation = end($affectations);
      $this->_ref_last_affectation  = reset($affectations);
    }
    else {
      $this->_ref_first_affectation = new CAffectation();
      $this->_ref_last_affectation  = new CAffectation();
    }

    return $this->_ref_affectations = $affectations;
  }

  /**
   * Charge les mouvements du s�jour
   *
   * @return CMovement[]
   */
  function loadRefsMovements() {
    return $this->_ref_movements = $this->loadBackRefs("movements");
  }

  /**
   * Charge la premi�re affectation
   *
   * @return CAffectation
   */
  function loadRefFirstAffectation() {
    if (!$this->_ref_first_affectation) {
      $this->loadRefsAffectations();
    }

    return $this->_ref_first_affectation;
  }

  /**
   * Force la cr�ation d'une affectation (?)
   *
   * @param CAffectation $affectation Affectation concern�e
   *
   * @todo A d�tailler
   * @return CAffectation|null|string|void
   */
  function forceAffectation(CAffectation $affectation) {
    $datetime   = $affectation->entree;
    $lit_id     = $affectation->lit_id;
    $service_id = $affectation->service_id;

    $splitting            = new CAffectation();
    $where["sejour_id"] = "=  '$this->_id'";
    $where["entree"]    = "<= '$datetime'";
    $where["sortie"]    = ">= '$datetime'";
    $splitting->loadObject($where);

    $create = new CAffectation();

    // On retrouve une affectation a spliter
    if ($splitting->_id) {
      // Affecte la sortie de l'affectation a cr�er avec l'ancienne date de sortie
      $create->sortie = $splitting->sortie;

      // On passe � effectuer la split
      $splitting->effectue    = 1;
      $splitting->sortie      = $datetime;
      $splitting->_no_synchro = true;
      if ($msg = $splitting->store()) {
        return $msg;
      }
    }
    // On cr�� une premi�re affectation
    else {
      $create->sortie  = $this->sortie;
    }

    // Cr�� la nouvelle affectation
    $create->sejour_id  = $this->_id;
    $create->entree     = $datetime;
    $create->lit_id     = $lit_id;
    $create->service_id = $service_id;
    if ($msg = $create->store()) {
      return $msg;
    }

    return $create;
  }

  /**
   * Chargement des op�rations
   *
   * @param array $where xhere
   *
   * @return COperation[]
   */
  function loadRefsOperations($where = array()) {
    $where["sejour_id"] = "= '$this->_id'";
    $order = "date ASC";

    $operations = new COperation();
    $this->_ref_operations = $operations->loadList($where, $order);

    // Motif complet
    if (!$this->libelle) {
      $this->_motif_complet = "";
      if ($this->recuse == -1) {
        $this->_motif_complet .= "[Att] ";
      }
      $motif = array();
      foreach ($this->_ref_operations as $_op) {
        /** @var COperation $_op */
        if ($_op->libelle) {
          $motif[] = $_op->libelle;
        }
        else {
           $motif[] = implode("; ", $_op->_codes_ccam);
        }
      }
      $this->_motif_complet .= implode("; ", $motif);
    }

    // Agr�gats des codes CCAM des op�rations
    $this->_codes_ccam_operations = CMbArray::pluck($this->_ref_operations, "codes_ccam");
    CMbArray::removeValue("", $this->_codes_ccam_operations);
    $this->_codes_ccam_operations = implode("|", $this->_codes_ccam_operations);

    if (count($this->_ref_operations) > 0) {
      $this->_ref_last_operation = reset($this->_ref_operations);
    }
    else {
      $this->_ref_last_operation = new COperation();
    }
    return $this->_ref_operations;
  }

  /**
   * Charge la premi�re op�ration d'un s�jour
   *
   * @return COperation
   */
  function loadRefFirstOperation() {
    $operation = new COperation;
    $operation->sejour_id = $this->_id;
    $operation->loadMatchingObject("date ASC");

    return $this->_ref_first_operation = $operation;
  }

  /**
   * Charge la derni�re op�ration d'un s�jour
   *
   * @return COperation
   */
  function loadRefLastOperation() {
    $operation = new COperation;
    $operation->sejour_id = $this->_id;
    $operation->loadMatchingObject("date DESC");

    return $this->_ref_last_operation = $operation;
  }

  /**
   * Charge la premi�re internvention du jour
   *
   * @param datetime $date Datetime de r�f�rence
   *
   * @return COperation
   */
  function loadRefCurrOperation($date) {
    $date = CMbDT::date($date);
    $where["operations.sejour_id"] = "= '$this->_id'";
    $where[] = "plagesop.date = '$date' OR operations.date = '$date'";
    $leftjoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
    $operation = new COperation;
    $operation->loadObject($where, null, null, $leftjoin);
    return $this->_ref_curr_operation = $operation;
  }

  /**
   * Charge toutes les interventions du jour
   *
   * @param datetime $date Datetime de r�f�rence
   *
   * @return COperation[]
   */
  function loadRefCurrOperations($date) {
    $date = CMbDT::date($date);
    $where["operations.sejour_id"] = "= '$this->_id'";
    $where[] = "plagesop.date = '$date' OR operations.date = '$date'";
    $leftjoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
    $operation = new COperation;
    return $this->_ref_curr_operations = $operation->loadList($where, null, null, null, $leftjoin);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
    $this->loadRefsActesCCAM();
  }

  /**
   * Charge le GHM du s�jour
   *
   * @return CGHM
   */
  function loadRefGHM() {
    /** @var CGHM $GHM */
    $GHM = $this->loadUniqueBackRef("ghm");

    if (!$GHM->_id) {
      $GHM->sejour_id = $this->sejour_id;
    }
    $GHM->_ref_sejour = $this;
    $GHM->bindInfos();
    $GHM->getGHM();

    $this->_ref_GHM = $GHM;
  }

  /**
   * Charge l'observation d'entr�e du s�jour
   *
   * @return CConsultation
   */
  function loadRefObsEntree() {
    $consult = new CConsultation();
    if ($this->_id) {
      $consult->sejour_id = $this->_id;
      $consult->type = "entree";
      $consult->annule = 0;
      $consult->loadMatchingObject();
    }

    return $this->_ref_obs_entree = $consult;
  }

  /**
   * @see parent::fillLimitedTemplate()
   */
  function fillLimitedTemplate(&$template) {
    // Ajout du praticien pour les destinataires possibles (dans l'envoi d'un email)
    $chir = $this->loadRefPraticien();
    $template->destinataires[] = array(
      "nom"   => "Dr " . $chir->_user_last_name . " " . $chir->_user_first_name,
      "email" => $chir->_user_email,
      "tag"   => "Praticien"
    );

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addLongDateProperty("Admission - Date longue"  , $this->entree_prevue);
    $template->addDateProperty("Admission - Date"             , $this->entree_prevue);
    $template->addTimeProperty("Admission - Heure"            , $this->entree_prevue);
    $template->addProperty("Admission - Type"                 , $this->getFormattedValue("type"));
    $template->addProperty("Hospitalisation - Dur�e"          , $this->_duree_prevue);
    $template->addDateProperty("Hospitalisation - Date sortie", $this->sortie_prevue);
    $template->addProperty("Hospitalisation - Date sortie longue", $this->getFormattedValue("sortie_prevue"));
    $this->loadNDA();
    $template->addProperty("Sejour - Num�ro de dossier"       , $this->_NDA );
    $template->addBarcode("Sejour - Code barre ID"           , "SID$this->_id");
    $template->addBarcode("Sejour - Code barre NDOS"         , "NDOS$this->_NDA");

    $template->addDateProperty("Sejour - Date entr�e"         , $this->entree);
    $template->addLongDateProperty("Sejour - Date entr�e (longue)", $this->entree);
    $template->addTimeProperty("Sejour - Heure entr�e"        , $this->entree);
    $template->addDateProperty("Sejour - Date sortie"         , $this->sortie);
    $template->addLongDateProperty("Sejour - Date sortie (longue)", $this->sortie);
    $template->addTimeProperty("Sejour - Heure sortie"        , $this->sortie);

    $template->addDateProperty("Sejour - Date entr�e r�elle"  , $this->entree_reelle);
    $template->addTimeProperty("Sejour - Heure entr�e r�elle" , $this->entree_reelle);
    $template->addDateProperty("Sejour - Date sortie r�elle"  , $this->sortie_reelle);
    $template->addTimeProperty("Sejour - Heure sortie r�elle" , $this->sortie_reelle);

    $template->addProperty("Sejour - Mode d'entr�e"           , $this->getFormattedValue("mode_entree"));
    $template->addProperty("Sejour - Mode de sortie"          , $this->getFormattedValue("mode_sortie"));
    $template->addProperty("Sejour - Service de sortie"       , $this->getFormattedValue("service_sortie_id"));
    $template->addProperty("Sejour - Etablissement de sortie" , $this->getFormattedValue("etablissement_sortie_id"));
    $template->addProperty("Sejour - Commentaires de sortie"  , $this->getFormattedValue("commentaires_sortie"));

    $template->addProperty("Sejour - Libelle"                 , $this->getFormattedValue("libelle"));
    $template->addProperty("Sejour - Transport"               , $this->getFormattedValue("transport"));

    /** @var CExamIgs $last_exam_igs */
    $last_exam_igs = $this->loadLastBackRef("exams_igs");
    $template->addProperty("Sejour - Score IGS"               , $last_exam_igs->_id ? $last_exam_igs->scoreIGS : "");

    $consult_anesth = $this->loadRefsConsultAnesth();
    $consult = $consult_anesth->loadRefConsultation();
    $consult->loadRefPlageConsult();

    $cpa_datetime = $consult->_id ? $consult->_datetime : "";
    $template->addDateProperty("Sejour - Consultation anesth�sie - Date", $cpa_datetime);
    $template->addLongDateProperty("Sejour - Consultation anesth�sie - Date (longue)", $cpa_datetime);
    $template->addLongDateProperty("Sejour - Consultation anesth�sie - Date (longue, minuscule)", $cpa_datetime);
    $template->addTimeProperty("Sejour - Consultation anesth�sie - Heure", $cpa_datetime);

    $this->loadRefsFiles();
    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Sejour - Liste des fichiers", $list);

    if (CAppUI::conf("dPurgences old_rpu") == "1") {
      if (CModule::getActive("sherpa")) {
        $rpu = $this->loadRefRPU();
        $template->addProperty("Sejour - Provenance"         , $rpu->_id ? $rpu->getFormattedValue("urprov") : "");
      }
    }
    else {
      $template->addProperty("Sejour - Provenance"           , $this->getFormattedValue("provenance"));
      $template->addProperty("Sejour - Destination"          , $this->getFormattedValue("destination"));
    }

    $this->loadRefPraticien();
    $template->addProperty("Hospitalisation - Praticien"    , "Dr ".$this->_ref_praticien->_view);

    $this->loadRefsAffectations();
    $this->_ref_last_affectation->loadView();
    $template->addProperty("Hospitalisation - Derni�re affectation", $this->_ref_last_affectation->_view);

    // Diagnostics
    $this->loadExtDiagnostics();
    $diag = $this->DP ? "$this->DP: {$this->_ext_diagnostic_principal->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Principal"    , $diag);
    $diag = $this->DR ? "$this->DR: {$this->_ext_diagnostic_relie->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Reli�"        , $diag);
    $template->addProperty("Sejour - Remarques", $this->rques);

    // Chargement du suivi medical (transmissions, observations, prescriptions)
    $this->loadSuiviMedical();

    // Transmissions
    $transmissions = array();
    if (isset($this->_back["transmissions"])) {
      foreach ($this->_back["transmissions"] as $_trans) {
        $datetime = CMbDT::format($_trans->date, CAppUI::conf('datetime'));
        $transmissions["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";
      }
    }

    $template->addListProperty("Sejour - Transmissions", $transmissions);

    $this->loadRefsTransmissions(false, true, false);

    $transmissions_hautes = array();
    foreach ($this->_ref_transmissions as $_trans) {
      $_trans->loadRefUser();
      $datetime = CMbDT::format($_trans->date, CAppUI::conf('datetime'));
      $transmissions_hautes["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";

    }
    $template->addListProperty("Sejour - Transmissions - importance haute", $transmissions_hautes);

    $this->loadRefsTransmissions(true, false, true);
    $transmissions_macro  = array();

    foreach ($this->_ref_transmissions as $_trans) {
      $_trans->loadRefUser();
      $datetime = CMbDT::format($_trans->date, CAppUI::conf('datetime'));
      $transmissions_macro["$_trans->date $_trans->_guid"] = "$_trans->text, le $datetime, {$_trans->_ref_user->_view}";

    }

    $template->addListProperty("Sejour - Transmissions - macrocible", $transmissions_macro);

    // Observations
    $observations = array();
    if (isset($this->_back["observations"])) {
      foreach ($this->_back["observations"] as $_obs) {
        $datetime = CMbDT::format($_obs->date, CAppUI::conf('datetime'));
        $observations["$_obs->date $_obs->_guid"] = "$_obs->text, le $datetime, {$_obs->_ref_user->_view}";
      }
    }
    $template->addListProperty("Sejour - Observations", $observations);

    // Observation d'entr�e
    /** @var CConsultation $obs_entree */
    $obs_entree = $this->loadRefObsEntree();

    $template->addProperty("Sejour - Observation entr�e - Motif"          , $obs_entree->getFormattedValue("motif"));
    $template->addProperty("Sejour - Observation entr�e - Examen clinique", $obs_entree->getFormattedValue("examen"));
    $template->addProperty("Sejour - Observation entr�e - Remarques"      , $obs_entree->getFormattedValue("rques"));
    $template->addProperty("Sejour - Observation entr�e - Traitements"    , $obs_entree->getFormattedValue("traitement"));
    $template->addProperty("Sejour - Observation entr�e - Histoire de la maladie", $obs_entree->getFormattedValue("histoire_maladie"));
    $template->addProperty("Sejour - Observation entr�e - Au total"       , $obs_entree->getFormattedValue("conclusion"));

    // Prescriptions
    $lines = array();
    if (CModule::getActive('dPprescription')) {

      $prescription = $this->loadRefPrescriptionSejour();
      $prescription->loadRefsLinesAllComments();
      $prescription->loadRefsLinesElement();

      if (isset($prescription->_ref_prescription_lines_all_comments)) {
        foreach ($prescription->_ref_prescription_lines_all_comments as $_comment) {
          $datetime = CMbDT::format("$_comment->debut $_comment->time_debut", CAppUI::conf('datetime'));
          $lines["$_comment->debut $_comment->time_debut $_comment->_guid"] =
            "$_comment->_view, $datetime, {$_comment->_ref_praticien->_view}";
        }
      }

      if (isset($prescription->_ref_prescription_lines_element)) {
        foreach ($prescription->_ref_prescription_lines_element as $_line_element) {
          $datetime = CMbDT::format("$_line_element->debut $_line_element->time_debut", CAppUI::conf('datetime'));
          $view = "$_line_element->_view";
          if ($_line_element->commentaire) {
            $view .= " ($_line_element->commentaire)";
          }
          $view .= ", $datetime, ".$_line_element->_ref_praticien->_view;
          $lines["$_line_element->debut $_line_element->time_debut $_line_element->_guid"] = $view;
        }
      }
      krsort($lines);
      $template->addListProperty("Sejour - Prescription light", $lines);
    }

    // Suivi m�dical: transmissions, observations, prescriptions
    $suivi_medical = $transmissions + $observations + $lines;
    krsort($suivi_medical);
    $template->addListProperty("Sejour - Suivi m�dical", $suivi_medical);

    // Interventions
    $operations = array();
    foreach ($this->loadRefsOperations() as $_operation) {
      $_operation->loadRefPlageOp(true);
      $datetime = $_operation->getFormattedValue("_datetime");
      $chir = $_operation->loadRefChir(true);
      $operations[] = "le $datetime, par $chir->_view" . ($_operation->libelle ? " : $_operation->libelle" : "");
    }
    $template->addListProperty("Sejour - Intervention - Liste", $operations);

    // Derni�re intervention
    $this->_ref_last_operation->fillLimitedTemplate($template);

    // R�gime
    $regimes = array();

    if ($this->hormone_croissance) {
      $regimes[] = CAppUI::tr("CSejour-hormone_croissance");
    }

    if ($this->repas_sans_sel) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_sel");
    }

    if ($this->repas_sans_porc) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_porc");
    }

    if ($this->repas_diabete) {
      $regimes[] = CAppUI::tr("CSejour-repas_diabete");
    }

    if ($this->repas_sans_residu) {
      $regimes[] = CAppUI::tr("CSejour-repas_sans_residu");
    }

    if (!count($regimes)) {
      $template->addProperty("Sejour - R�gime", CAppUI::tr("CSejour-no_diet_specified"));
    }
    else {
      $template->addListProperty("Sejour - R�gime", $regimes);
    }

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Sejour");
    }

    if (CModule::getActive("dPfacturation") && CAppUI::conf("dPplanningOp CFactureEtablissement use_facture_etab")) {
      $this->loadRefsFactureEtablissement();
      $this->loadNRA();

      $this->_ref_last_facture->fillLimitedTemplate($template);
      $template->addProperty("Sejour - Nature du s�jour", $this->getFormattedValue("_type_sejour"));
      $template->addProperty("Sejour - Remarques base"  , $this->getFormattedValue("_rques_assurance_maladie"));
      $template->addProperty("Sejour - Remarques compl.", $this->getFormattedValue("_rques_assurance_accident"));
      $template->addProperty("Sejour - Num�ro de cas"   , $this->_ref_NRA && $this->_ref_NRA->_id ? $this->_ref_NRA->id400 : "-");
    }

    if (CModule::getActive("mvsante")) {
      CMVSante::fillLimitedTemplate($template, $this);
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {

    // Chargement du fillTemplate du praticien
    $this->loadRefPraticien()->fillTemplate($template);

    // Ajout d'un fillTemplate du patient
    $this->loadRefPatient()->fillTemplate($template);

    $this->fillLimitedTemplate($template);

    // Dossier m�dical
    $this->loadRefDossierMedical()->fillTemplate($template, "Sejour");

    // Prescription
    if (CModule::getActive('dPprescription')) {
      $prescriptions = $this->loadRefsPrescriptions();
      $prescription = isset($prescriptions["pre_admission"]) ? $prescriptions["pre_admission"] : new CPrescription();
      $prescription->type = "pre_admission";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($prescriptions["sejour"]) ? $prescriptions["sejour"] : new CPrescription();
      $prescription->type = "sejour";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($prescriptions["sortie"]) ? $prescriptions["sortie"] : new CPrescription();
      $prescription->type = "sortie";
      $prescription->fillLimitedTemplate($template);
    }

    // RPU
    $this->loadRefRPU();
    if ($this->_ref_rpu) {
      $this->_ref_rpu->fillLimitedTemplate($template);
    }
  }

  /**
   * Build an array containing surgery dates
   *
   * @return date[]
   */
  function makeDatesOperations() {
    $this->_dates_operations = array();

    // On s'assure d'avoir les op�rations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }

    foreach ($this->_ref_operations as $operation) {
      if ($operation->annulee) {
        continue;
      }

      // On s'assure d'avoir les plages op
      if (!$operation->_ref_plageop) {
        $operation->loadRefPlageOp();
      }

      $this->_dates_operations[$operation->_id] = CMbDT::date($operation->_datetime);
    }

    return $this->_dates_operations;
  }

  /**
   * Builds an array containing consults dates
   *
   * @return date[]
   */
  function makeDatesConsultations() {
    $this->_dates_consultations = array();

    // On s'assure d'avoir les op�rations
    if (!$this->_ref_consultations) {
      $this->loadRefsConsultations();
    }

    foreach ($this->_ref_consultations as &$consultation) {
      if ($consultation->annule) {
        continue;
      }

      // On s'assure d'avoir les plages op
      if (!$consultation->_ref_plageconsult) {
        $consultation->loadRefPlageConsult();
      }

      $this->_dates_consultations[$consultation->_id] = CMbDT::date($consultation->_datetime);
    }

    return $this->_dates_consultations;
  }

  /**
   * Builds an array containing cancel alerts for the sejour
   *
   * @param ref|COperation $excluded_id Exclude given operation
   *
   * @return void Valuate $this->_cancel_alert
   */
  function makeCancelAlerts($excluded_id = null) {
    $this->_cancel_alerts = array(
      "all" => array(),
      "acted" => array(),
    );

    // On s'assure d'avoir les op�rations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }

    if ($this->_ref_operations) {
      foreach ($this->_ref_operations as $_operation ) {
        // Needed for correct view
        $_operation->loadRefPraticien();
        $_operation->loadRefPlageOp();

        // Exclude one
        if ($_operation->_id == $excluded_id) {
          continue;
        }

        if ($_operation->annulee == 0) {
          $operation_view = " le "
            . CMbDT::dateToLocale(CMbDT::date($_operation->_datetime))
            . " par le Dr "
            . $_operation->_ref_chir->_view;
          $_operation->countActes();
          if ($_operation->_count_actes) {
            $this->_cancel_alerts["acted"][$_operation->_id] = $operation_view;
          }

          $this->_cancel_alerts["all"][$_operation->_id] = $operation_view;
        }
      }
    }
  }

  /**
   * Count evenement SSR for a given date
   *
   * @param date $date Date
   *
   * @return void|int
   */
  function countEvenementsSSR($date) {
    if (!$this->_id) {
      return null;
    }

    $evenement = new CEvenementSSR;
    $ljoin = array();
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $where["evenement_ssr.debut"] = "BETWEEN '$date 00:00:00' AND '$date 23:59:59'";
    return $this->_count_evenements_ssr = $evenement->countList($where, null, $ljoin);
  }

  /**
   * Count evenement SSR for a given week and a given kine
   *
   * @param ref  $kine_id  Filtrer sur le kine
   * @param date $date_min Date minimale
   * @param date $date_max Date maximale
   *
   * @return void|int
   */
  function countEvenementsSSRWeek($kine_id, $date_min, $date_max) {
    if (!$this->_id) {
      return null;
    }

    $evenement = new CEvenementSSR();
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $where["evenement_ssr.therapeute_id"] = "= '$kine_id'";
    $this->_count_evenements_ssr      = $evenement->countList($where, null, $ljoin);

    $where["evenement_ssr.debut"] = "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
    return $this->_count_evenements_ssr_week = $evenement->countList($where, null, $ljoin);
  }

  /**
   * D�termine le nombre de jours du planning pour la semaine
   *
   * @param date $date Date de r�f�rence
   *
   * @return int 5, 6 ou 7 jours
   */
  function getNbJourPlanning($date) {
    $sunday = CMbDT::date("next sunday", CMbDT::date("- 1 DAY", $date));
    $saturday = CMbDT::date("-1 DAY", $sunday);

    $_evt = new CEvenementSSR();
    $ljoin = array();
    $ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
    $where = array();
    $where["evenement_ssr.debut"] = "BETWEEN '$sunday 00:00:00' AND '$sunday 23:59:59'";
    $where[] = "(evenement_ssr.sejour_id = '$this->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$this->_id')";
    $count_event_sunday = $_evt->countList($where, null, $ljoin);

    $nb_days = 7;

    // Si aucun evenement le dimanche
    if (!$count_event_sunday) {
      $nb_days = 6;
      $where["evenement_ssr.debut"] = "BETWEEN '$saturday 00:00:00' AND '$saturday 23:59:59'";
      $count_event_saturday= $_evt->countList($where, null, $ljoin);
      // Aucun evenement le samedi et aucun le dimanche
      if (!$count_event_saturday) {
        $nb_days = 5;
      }
    }
    return $nb_days;
  }

  /**
   * @see parent::completeLabelFields()
   */
  function completeLabelFields(&$fields) {
    if (!isset($this->_from_op)) {
      $this->loadRefLastOperation()->_from_sejour = 1;
      $this->_ref_last_operation->completeLabelFields($fields);
    }

    $this->loadRefPatient()->completeLabelFields($fields);
    $this->loadRefPraticien();
    $this->loadNDA();
    $this->loadNRA();
    $now = CMbDT::dateTime();
    $affectation = $this->getCurrAffectation($this->entree > $now ? $this->entree : null);
    $affectation->loadView();

    $fields = array_merge(
      $fields,
      array(
        "DATE ENT"         => CMbDT::dateToLocale(CMbDT::date($this->entree)),
        "HEURE ENT"        => CMbDT::transform($this->entree, null, "%H:%M"),
        "DATE SORTIE"      => CMbDT::dateToLocale(CMbDT::date($this->sortie)),
        "HEURE SORTIE"     => CMbDT::transform($this->sortie, null, "%H:%M"),
        "PRAT RESPONSABLE" => $this->_ref_praticien->_view,
        "NDOS"             => $this->_NDA,
        "NRA"              => $this->_ref_NRA ? $this->_ref_NRA->id400 : "",
        "CODE BARRE NDOS"  => "@BARCODE_".$this->_NDA."@",
        "CHAMBRE COURANTE" => $affectation->_view
      )
    );

    if (CAppUI::conf("ref_pays") == 2) {
      $fields["NATURE SEJOUR"]  = $this->getFormattedValue("_type_sejour");
      $fields["MODE TRT"]       = $this->loadRefChargePriceIndicator()->code;
      $this->loadRefsFactureEtablissement();

      if ($this->_ref_last_facture) {
        $this->_ref_last_facture->loadRefAssurance();
        $fields["ASSUR MALADIE"]  = $this->_ref_last_facture->_ref_assurance_maladie->nom;
        $fields["ASSUR ACCIDENT"] = $this->_ref_last_facture->_ref_assurance_accident->nom;
      }
    }
  }

  /**
   * V�rification de la possibilit� de merger plusieurs s�jours
   *
   * @param CSejour[] $sejours S�jours concern�s
   *
   * @return void|string
   */
  function checkMerge($sejours = array()) {
    if ($msg = parent::checkMerge($sejours)) {
      return $msg;
    }

    // Cas des prescriptions
    $count_prescription = 0;
    foreach ($sejours as $_sejour) {
      $_sejour->loadRefPrescriptionSejour();
      if ($_sejour->_ref_prescription_sejour && $_sejour->_ref_prescription_sejour->_id) {

        // Suppression des prescriptions vide
        $prescription = new CPrescription;
        $prescription->load($_sejour->_ref_prescription_sejour->_id);
        $back_props = $prescription->getBackProps();

        $count_back_props = 0;

        // On retire les logs de la liste des backprops
        unset($back_props["logs"]);

        foreach ($back_props as $back_prop => $object) {
          $count_back_props += $prescription->countBackRefs($back_prop);
        }

        if ($count_back_props == 0) {
          $prescription->delete();
          continue;
        }

        if ($count_prescription == 1) {
          return "Impossible de fusionner des sejours qui comportent chacun des prescriptions de s�jour";
        }

        $count_prescription++;
      }
    }

    // Cas des affectations
    $affectation = new CAffectation();
    $where["sejour_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($sejours, "_id"));

    /** @var CAffectation[] $affectations */
    $affectations = $affectation->loadList($where);

    foreach ($affectations as $_affectation_1) {
      foreach ($affectations as $_affectation_2) {
        if ($_affectation_1->collide($_affectation_2)) {
          return CAppUI::tr("CSejour-merge-warning-affectation-conflict", $_affectation_1->_view, $_affectation_2->_view);
        }
      }
    }

    return null;
  }

  /**
   * D�termine les UFs d'h�bergement, de soins et m�dicaux pour une date donn�e
   * et �ventuellement une affectation donn�e
   *
   * @param null $date           Date de r�f�rence
   * @param null $affectation_id Affectation sp�cifique
   *
   * @return array|CUniteFonctionnelle[]
   */
  function getUFs($date = null, $affectation_id = null) {
    if (!$date) {
      $date = CMbDT::dateTime();
    }

    if ($affectation_id) {
      $affectation = new CAffectation();
      $affectation->load($affectation_id);
    }
    else {
      // Chargement de l'affectation courante
      $affectation = $this->getCurrAffectation($date);

      // Si on n'a pas d'affectation on va essayer de chercher la premi�re
      if (!$affectation->_id) {
        $this->loadSurrAffectations();
        $affectation = $this->_ref_next_affectation;
      }
    }

    if ($affectation->_id) {
      return $affectation->getUFs();
    }

    if ($this->uf_hebergement_id) {
      return array(
        "hebergement" => $this->loadRefUFHebergement(),
        "medicale"    => $this->loadRefUFMedicale(),
        "soins"       => $this->loadRefUFSoins(),
      );
    }

    $affectation_uf = new CAffectationUniteFonctionnelle();

    // Service
    if ($this->service_id) {
      $affectation_uf->object_id    = $this->service_id;
      $affectation_uf->object_class = "CService";
    }
    // Praticien
    else {
      $affectation_uf->object_id    = $this->loadRefPraticien()->_id;
      $affectation_uf->object_class = "CMediusers";
    }

    $affectation_uf->loadMatchingObject();

    return array(
      "hebergement" => $affectation_uf->loadRefUniteFonctionnelle(),
      "medicale"    => $this->loadRefUFMedicale(),
      "soins"       => $this->loadRefUFSoins(),
    );
  }

  /**
   * D�termine les incr�ments
   *
   * @return array
   */
  function getIncrementVars() {
    $group_guid = $this->group_id ? "CGroups-$this->group_id" : CGroups::loadCurrent()->_guid;

    $typeHospi = $this->type ? CAppUI::conf("dPsante400 CIncrementer type_sejour $this->type", $group_guid) : null;

    return array(
      "typeHospi" => $typeHospi
    );
  }

  /**
   * D�termine les types de mouvements en fonction du code de message
   *
   * @param null $code Code du message
   *
   * @return null|string
   */
  function getMovementType($code = null) {
    // Cas d'une pr�-admission
    if ($this->_etat == "preadmission") {
      return "PADM";
    }

    if ($this->_etat == "encours" && ($this->service_entree_id || $code == "A02")) {
      return "MUTA";
    }

    // Cas d'une absence provisoire
    if ($code == "A21") {
      return "AABS";
    }

    // Cas d'un retour d'absence provisoire
    if ($code == "A22") {
      return "RABS";
    }

    // Cas d'une entr�e autoris�e
    if ($code == "A14") {
      return "EATT";
    }

    // Cas d'un transfert autoris�
    if ($code == "A15") {
      return "TATT";
    }

    // Cas d'une sortie autoris�e
    if ($code == "A16") {
      return "SATT";
    }

    // Cas d'une admission
    if ($this->_etat == "encours") {
      return "ADMI";
    }

    // Cas d'une sortie
    if ($this->_etat == "cloture") {
      return "SORT";
    }

    return null;
  }

  /**
   * Charge les items de prestations souhait�s et r�alis�s
   *
   * @return CItemPrestation[]
   */
  function getPrestations() {
    $this->_ref_prestations = array();

    /** @var CItemLiaison[] $items_liaisons */
    $items_liaisons = $this->loadBackRefs("items_liaisons");

    CMbObject::massLoadFwdRef($items_liaisons, "item_souhait_id");
    CMbObject::massLoadFwdRef($items_liaisons, "item_realise_id");

    foreach ($items_liaisons as $_item_liaison) {
      $_item_souhait = $_item_liaison->loadRefItem();
      $_item_realise = $_item_liaison->loadRefItemRealise();

      if ($_item_realise->_id) {
        $this->_ref_prestations[$_item_liaison->date][] = $_item_realise;
        $_item_realise->_quantite = 1;
        $_item_realise->loadRefObject();
      }
      elseif ($_item_souhait->object_class == "CPrestationPonctuelle") {
        $this->_ref_prestations[$_item_liaison->date][] = $_item_souhait;
        $_item_souhait->_quantite = $_item_liaison->quantite;
        $_item_souhait->loadRefObject();
      }
    }

    return $this->_ref_prestations;
  }

  /**
   * Chrage la premi�re liaison de prestation journali�re pour une prestation
   *
   * @param ref $prestation_id Prestation concern�e
   *
   * @return void
   */
  function loadRefFirstLiaisonForPrestation($prestation_id) {
    $this->_first_liaison_for_prestation = new CItemLiaison();
    $where = array();
    $ljoin = array();
    $where["sejour_id"] = "= '$this->_id'";
    $ljoin["item_prestation"] =
      "item_prestation.item_prestation_id = item_liaison.item_realise_id OR
       item_prestation.item_prestation_id = item_liaison.item_souhait_id";

    $where["object_class"] = " = 'CPrestationJournaliere'";
    $where["object_id"] = " = '$prestation_id'";
    $this->_first_liaison_for_prestation->loadObject($where, null, null, $ljoin);
  }

  /**
   * Charge les liaisons de prestations pour une prestation entre deux date
   *
   * @param ref  $prestation_id Prestation de r�f�rence
   * @param null $date_min      Date minimale
   * @param null $date_max      Date maximale
   *
   * @return CStoredObject[]
   */
  function loadLiaisonsForPrestation($prestation_id, $date_min = null, $date_max = null) {
    $item_liaison = new CItemLiaison();
    $where    = array();
    $order    = "date ASC";
    $limit    = null;
    $groupby  = "item_liaison.date";
    $ljoin    = array();

    if ($prestation_id == "all") {
      $prestation_id = null;
      $groupby = "item_prestation_id";
    }

    $where["sejour_id"] = "= '$this->_id'";
    $ljoin["item_prestation"] =
      "  item_prestation.item_prestation_id = item_liaison.item_souhait_id
      OR item_prestation.item_prestation_id = item_liaison.item_realise_id";

    $where["object_class"] = " = 'CPrestationJournaliere'";
    if ($prestation_id) {
      $where["object_id"] = " = '$prestation_id'";
    }

    //min != max
    if ($date_min && $date_max && ($date_min != $date_max)) {
      $where['date'] = "BETWEEN '$date_min' AND '$date_max'";
    }

    //unique date, presta for the day
    if (($date_min && !$date_max) || (($date_min == $date_max) && $date_min)) {
      $where['date'] = "<= '$date_min'";  //get the last prestation for sejour (current day might not be defined)
      $order = "date DESC";
    }

    /** @var  CItemLiaison[] _liaisons_for_prestation */
    $this->_liaisons_for_prestation = $item_liaison->loadList($where, $order, $limit, $groupby, $ljoin);

    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_souhait_id");
    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_realise_id");

    /** @var CItemLiaison $_liaison */
    foreach ($this->_liaisons_for_prestation as $_liaison) {
      $_liaison->loadRefItem();
      $_liaison->loadRefItemRealise();
    }

    return $this->_liaisons_for_prestation;
  }

  /**
   * get prestations for a particular day
   * check for previous prestation to keep only "active" liaisons
   *
   * @param int  $prestation_id prestation
   * @param date $date          date
   *
   * @return CStoredObject[]
   */
  function loadLiaisonsForDay($prestation_id, $date) {
    $maxs = array();
    $item_liaison = new CItemLiaison();
    $where = array();
    $groupby = "item_liaison_id";
    $order = "item_liaison_id DESC";
    $where["sejour_id"] = "= '$this->_id'";
    $where["object_class"] = " = 'CPrestationJournaliere'";

    if ($prestation_id == "all") {
      $prestation_id = null;
    }

    if ($prestation_id) {
      $where["object_id"] = " = '$prestation_id'";
    }

    $ljoin["item_prestation"] =
      "  item_prestation.item_prestation_id = item_liaison.item_souhait_id
      OR item_prestation.item_prestation_id = item_liaison.item_realise_id";

    $where["date"] = "<= '$date'";

    $this->_liaisons_for_prestation = $item_liaison->loadList($where, $order, null, $groupby, $ljoin);

    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_souhait_id");
    CMbObject::massLoadFwdRef($this->_liaisons_for_prestation, "item_realise_id");

    /** @var CItemLiaison $_liaison */
    foreach ($this->_liaisons_for_prestation as $_liaison) {
      $_liaison->loadRefItem();
      $_liaison->loadRefItemRealise();

      //@todo : find a better way to cleanup old prestas
      $cat_id = ($_liaison->_ref_item_realise->_id) ? $_liaison->_ref_item_realise->object_id : $_liaison->_ref_item->object_id;
      $maxs[$_liaison->date][$cat_id] = $_liaison->_id;
      foreach ($maxs as $date => $data) {
        if ($date > $_liaison->date) {
          foreach ($data as $cat => $id) {
            if ($cat == $cat_id) {
              unset($this->_liaisons_for_prestation[$_liaison->_id]);
            }
          }
        }
      }
    }

    return $this->_liaisons_for_prestation;
  }

  /**
   * Compte les prestations souhait�es
   *
   * @return int
   */
  function countPrestationsSouhaitees() {
    $where["item_souhait_id"] = "IS NOT NULL";
    return $this->countBackRefs("items_liaisons", $where);
  }

  /**
   * Comptage de masse des prestations souhait�es pour une collection de s�jours
   *
   * @param CSejour[] $sejours Collection
   *
   * @return void
   */
  static function massCountPrestationSouhaitees($sejours) {
    $where["item_souhait_id"] = "IS NOT NULL";
    CStoredObject::massCountBackRefs($sejours, "items_liaisons", $where);
  }

  /**
   * Chargement de naissances
   *
   * @return CNaissance
   */
  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }

  /**
   * Chargement de l'UF d'h�bergement
   *
   * @return CUniteFonctionnelle
   */
  function loadRefUFHebergement() {
    return $this->_ref_uf_hebergement = $this->loadFwdRef("uf_hebergement_id", true);
  }

  /**
   * Chargement de l'UF m�dicale
   *
   * @return CUniteFonctionnelle
   */
  function loadRefUFMedicale() {
    return $this->_ref_uf_medicale = $this->loadFwdRef("uf_medicale_id", true);
  }

  /**
   * Chargement de l'UF de soins
   *
   * @return CUniteFonctionnelle
   */
  function loadRefUFSoins() {
    return $this->_ref_uf_soins = $this->loadFwdRef("uf_soins_id", true);
  }

  /**
   * Return idex type if it's special (e.g. NDA/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
    // L'identifiant externe est le NDA
    if ($idex->tag == self::getTagNDA()) {
      return "NDA";
    }

    if (CModule::getActive("mvsante")) {
      return CMVSante::getSpecialIdex($idex);
    }

    return null;
  }
}

if (CAppUI::conf("ref_pays") == 2) {
  CSejour::$fields_etiq[] = "NATURE SEJOUR";
  CSejour::$fields_etiq[] = "MODE TRT";
  CSejour::$fields_etiq[] = "ASSUR MALADIE";
  CSejour::$fields_etiq[] = "ASSUR ACCIDENT";
}