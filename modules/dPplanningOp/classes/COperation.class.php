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
 * Op�ration
 */
class COperation extends CCodable implements IPatientRelated {
  // static lists
  static $fields_etiq = array("ANESTH", "LIBELLE", "DATE", "COTE");

  // DB Table key
  public $operation_id;

  // Cl�ture des actes
  public $cloture_activite_1;
  public $cloture_activite_4;

  // DB References
  public $sejour_id;
  public $chir_id;
  public $chir_2_id;
  public $chir_3_id;
  public $chir_4_id;
  public $anesth_id;
  public $sortie_locker_id;
  public $plageop_id;
  public $salle_id;
  public $poste_sspi_id;
  public $examen_operation_id;
  public $graph_pack_id;

  // DB Fields S@nt�.com communication
  public $code_uf;
  public $libelle_uf;

  // DB Fields
  public $date;
  public $libelle;
  public $cote;
  public $temp_operation;
  public $pause;
  public $time_operation;
  public $exam_extempo;
  public $examen;
  public $materiel;
  public $commande_mat;
  public $info;
  public $type_anesth;
  public $rques;
  public $rques_personnel;
  public $rank;
  public $rank_voulu;
  public $anapath;
  public $flacons_anapath;
  public $labo_anapath;
  public $description_anapath;
  public $labo;
  public $flacons_bacterio;
  public $labo_bacterio;
  public $description_bacterio;
  public $prothese;
  public $ASA;
  public $position;

  public $depassement;
  public $conventionne;
  public $forfait;
  public $fournitures;
  public $depassement_anesth;

  public $annulee;

  public $horaire_voulu;
  public $_horaire_voulu;
  public $duree_uscpo;
  public $passage_uscpo;
  public $duree_preop;
  public $presence_preop;
  public $presence_postop;
  public $envoi_mail;

  // Timings enregistr�s
  public $debut_prepa_preop;
  public $fin_prepa_preop;
  public $entree_bloc;
  public $entree_salle;
  public $pose_garrot;
  public $debut_op;
  public $fin_op;
  public $retrait_garrot;
  public $sortie_salle;
  public $remise_chir;
  public $tto;
  public $entree_reveil;
  public $sortie_reveil_possible;
  public $sortie_reveil_reel;
  public $induction_debut;
  public $induction_fin;

  // V�rification du c�t�
  public $cote_admission;
  public $cote_consult_anesth;
  public $cote_hospi;
  public $cote_bloc;

  // Visite de pr�anesth�sie
  public $date_visite_anesth;
  public $prat_visite_anesth_id;
  public $rques_visite_anesth;
  public $autorisation_anesth;

  // Form fields
  public $_time_op;
  public $_time_urgence;
  public $_lu_type_anesth;
  public $_codes_ccam = array();
  public $_fin_prevue;
  public $_duree_interv;
  public $_duree_garrot;
  public $_duree_induction;
  public $_presence_salle;
  public $_duree_sspi;
  public $_deplacee;
  public $_compteur_jour;
  public $_protocole_prescription_anesth_id;
  public $_protocole_prescription_chir_id;
  public $_move;
  public $_reorder_rank_voulu;
  public $_password_visite_anesth;
  public $_patient_id;
  public $_dmi_alert;
  public $_offset_uscpo = array();
  public $_width_uscpo  = array();
  public $_width        = array();
  public $_debut_offset = array();
  public $_fin_offset   = array();
  public $_place_after_interv_id;
  public $_heure_us;
  public $_types_ressources_ids;
  public $_is_urgence;

  // Distant fields
  public $_datetime;
  public $_datetime_reel;
  public $_datetime_reel_fin;
  public $_datetime_best;
  public $_ref_affectation;
  /** @var CBesoinRessource[]  */
  public $_ref_besoins;

  // EAI Fields
  public $_eai_initiateur_group_id; // group initiateur du message EAI

  /** @var CMediusers */
  public $_ref_chir;
  /** @var CMediusers */
  public $_ref_chir_2;
  /** @var CMediusers */
  public $_ref_chir_3;
  /** @var CMediusers */
  public $_ref_chir_4;
  /** @var CPosteSSPI */
  public $_ref_poste;
  /** @var CPlageOp */
  public $_ref_plageop;
  /** @var CSalle */
  public $_ref_salle;
  /** @var CMediusers */
  public $_ref_anesth;
  /** @var CTypeAnesth */
  public $_ref_type_anesth;
  /** @var CConsultAnesth */
  public $_ref_consult_anesth;
  /** @var CMediusers */
  public $_ref_anesth_visite;
  /** @var CConsultation */
  public $_ref_consult_chir;
  /** @var CActeCCAM[] */
  public $_ref_actes_ccam = array();
  /** @var CEchangeHprim */
  public $_ref_echange_hprim;
  /** @var CAnesthPerop[] */
  public $_ref_anesth_perops;
  /** @var CNaissance[] */
  public $_ref_naissances;
  /** @var CPoseDispositifVasculaire[] */
  public $_ref_poses_disp_vasc;
  /** @var  CBloodSalvage */
  public $_ref_blood_salvage;
  /** @var CBrancardage */
  public $_ref_brancardage;
  /** @var CMediusers */
  public $_ref_sortie_locker;
  /** @var CSupervisionGraphPack */
  public $_ref_graph_pack;
  /** @var COperationWorkflow */
  public $_ref_workflow;

  // Filter Fields
  public $_date_min;
  public $_date_max;
  public $_plage;
  public $_service;
  public $_ranking;
  public $_cotation;
  public $_specialite;
  public $_scodes_ccam;
  public $_prat_id;
  public $_bloc_id;
  public $_ccam_libelle;
  public $_planning_perso;
  public $_libelle_interv;
  public $_rques_interv;

  function __construct() {
    parent::__construct();

    static $locked = null;
    if ($locked === null) {
      $locked = CAppUI::conf("planningOp COperation locked");
    }
    $this->_locked = $locked;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'operations';
    $spec->key   = 'operation_id';
    $spec->measureable = true;
    $spec->events = array(
      "dhe" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "checklist" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "perop" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "liaison" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "sortie_reveil" => array(
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
    $protocole = new CProtocole();
    $props = parent::getProps();
    $props["sejour_id"]            = "ref notNull class|CSejour";
    $props["chir_id"]              = "ref notNull class|CMediusers seekable";
    $props["chir_2_id"]            = "ref class|CMediusers seekable";
    $props["chir_3_id"]            = "ref class|CMediusers seekable";
    $props["chir_4_id"]            = "ref class|CMediusers seekable";
    $props["anesth_id"]            = "ref class|CMediusers";
    $props["sortie_locker_id"]     = "ref class|CMediusers";
    $props["plageop_id"]           = "ref class|CPlageOp seekable show|0";
    $props["pause"]                = "time show|0";
    $props["salle_id"]             = "ref class|CSalle";
    $props["poste_sspi_id"]        = "ref class|CPosteSSPI";
    $props["examen_operation_id"]  = "ref class|CExamenOperation";
    $props["graph_pack_id"]        = "ref class|CSupervisionGraphPack";
    $props["date"]                 = "date";
    $props["code_uf"]              = "str length|3";
    $props["libelle_uf"]           = "str maxLength|35";
    $props["libelle"]              = "str seekable autocomplete dependsOn|chir_id";
    $props["cote"]                 = $protocole->_props["cote"] . " notNull default|inconnu";
    $props["temp_operation"]       = "time show|0";
    $props["debut_prepa_preop"]    = "time show|0";
    $props["fin_prepa_preop"]      = "time show|0";
    $props["entree_salle"]         = "time show|0";
    $props["sortie_salle"]         = "time show|0";
    $props["remise_chir"]          = "time show|0";
    $props["tto"]                  = "time show|0";
    $props["time_operation"]       = "time show|0";
    $props["examen"]               = "text helped";
    $props["exam_extempo"]         = "bool";
    $props["materiel"]             = "text helped seekable show|0";
    $props["commande_mat"]         = "bool show|0";
    $props["info"]                 = "bool";
    $props["type_anesth"]          = "ref class|CTypeAnesth";
    $props["rques"]                = "text helped";
    $props["rques_personnel"]      = "text";
    $props["rank"]                 = "num max|255 show|0";
    $props["rank_voulu"]           = "num max|255 show|0";
    $props["depassement"]          = "currency min|0 confidential show|0";
    $props["conventionne"]         = "bool default|1";
    $props["forfait"]              = "currency min|0 confidential show|0";
    $props["fournitures"]          = "currency min|0 confidential show|0";
    $props["depassement_anesth"]   = "currency min|0 confidential show|0";
    $props["annulee"]              = "bool show|0";
    $props["pose_garrot"]          = "time show|0";
    $props["debut_op"]             = "time show|0";
    $props["fin_op"]               = "time show|0";
    $props["retrait_garrot"]       = "time show|0";
    $props["entree_reveil"]        = "time show|0";
    $props["sortie_reveil_possible"] = "time show|0";
    $props["sortie_reveil_reel"]   = "time show|0";
    $props["induction_debut"]      = "time show|0";
    $props["induction_fin"]        = "time show|0";
    $props["entree_bloc"]          = "time show|0";
    $props["anapath"]              = "enum list|1|0|? default|? show|0";
    $props["flacons_anapath"]      = "num max|255 show|0";
    $props["labo_anapath"]         = "str autocomplete";
    $props["description_anapath"]  = "text helped";
    $props["labo"]                 = "enum list|1|0|? default|? show|0";
    $props["flacons_bacterio"]     = "num max|255 show|0";
    $props["labo_bacterio"]        = "str autocomplete";
    $props["description_bacterio"] = "text helped";
    $props["prothese"]             = "enum list|1|0|? default|? show|0";
    $props["position"]             = "enum list|DD|DV|DL|GP|AS|TO|GYN";
    $props["ASA"]                  = "enum list|1|2|3|4|5|6";
    $props["horaire_voulu"]        = "time show|0";
    $props["presence_preop"]       = "time show|0";
    $props["presence_postop"]      = "time show|0";
    $props["envoi_mail"]           = "dateTime show|0";

    // Cl�ture des actes
    $props["cloture_activite_1"]    = "bool default|0";
    $props["cloture_activite_4"]    = "bool default|0";

    $props["cote_admission"]      = $protocole->_props["cote"] . " show|0";
    $props["cote_consult_anesth"] = $protocole->_props["cote"] . " show|0";
    $props["cote_hospi"]          = $protocole->_props["cote"] . " show|0";
    $props["cote_bloc"]           = $protocole->_props["cote"] . " show|0";

    // Visite de pr�anesth�sie
    $props["date_visite_anesth"]     = "date";
    $props["prat_visite_anesth_id"]  = "ref class|CMediusers";
    $props["rques_visite_anesth"]    = "text helped show|0";
    $props["autorisation_anesth"]    = "bool default|0";

    $props["facture"]                = "bool default|0";

    // Max USCPO acc�l�re les chargements, ne pas supprimer, au pire augmenter
    $props["duree_uscpo"]            = "num min|0 max|10 default|0";

    if (CAppUI::conf("dPplanningOp COperation show_duree_uscpo") == 2) {
      $props["passage_uscpo"]        = "bool notNull";
    }
    else {
      $props["passage_uscpo"]        = "bool";
    }

    $props["duree_preop"]             = "time";

    $props["_duree_interv"]           = "time";
    $props["_duree_garrot"]           = "time";
    $props["_duree_induction"]        = "time";
    $props["_presence_salle"]         = "time";
    $props["_duree_sspi"]             = "time";
    $props["_horaire_voulu"]          = "time";

    $props["_date_min"]               = "date";
    $props["_date_max"]               = "date moreEquals|_date_min";
    $props["_plage"]                  = "bool";

    $props["_ranking"]                = "enum list|ok|ko";
    $props["_cotation"]               = "enum list|ok|ko";

    $props["_prat_id"]                = "text"; // ?? misdefined
    $props["_patient_id"]             = "ref class|CPatient show|1";
    $props["_bloc_id"]                = "ref class|CBlocOperatoire";
    $props["_specialite"]             = "text";
    $props["_ccam_libelle"]           = "bool default|1";
    $props["_time_op"]                = "time";
    $props["_datetime"]               = "dateTime show";
    $props["_datetime_reel"]          = "dateTime";
    $props["_datetime_reel_fin"]      = "dateTime";
    $props["_datetime_best"]          = "dateTime";
    $props["_move"]                   = "str";
    $props["_password_visite_anesth"] = "password notNull";
    $props["_heure_us"]               = "time";

    return $props;
  }

  /**
   * @see parent::loadRelPatient()
   */
  function loadRelPatient(){
    return $this->loadRefPatient();
  }

  /**
   * @see parent::getExecutantId()
   */
  function getExecutantId($code_activite) {
    $this->loadRefChir();
    $this->loadRefPlageOp();
    return ($code_activite == 4 ? $this->_ref_anesth->user_id : $this->chir_id);
  }

  /**
   * @see parent::getExtensionDocumentaire()
   */
  function getExtensionDocumentaire() {
    /** @var CTypeAnesth $type_anesth */
    $type_anesth = $this->loadFwdRef("type_anesth", true);
    $this->_ref_type_anesth = $type_anesth;
    return $type_anesth->ext_doc;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["blood_salvages"]           = "CBloodSalvage operation_id";
    $backProps["dossiers_anesthesie"]      = "CConsultAnesth operation_id";
    $backProps["naissances"]               = "CNaissance operation_id";
    $backProps["prescription_elements"]    = "CPrescriptionLineElement operation_id";
    $backProps["prescription_medicaments"] = "CPrescriptionLineMedicament operation_id";
    $backProps["prescription_comments"]    = "CPrescriptionLineComment operation_id";
    $backProps["prescription_dmis"]        = "CPrescriptionLineDMI operation_id";
    $backProps["prescription_line_mix"]    = "CPrescriptionLineMix operation_id";
    $backProps["check_lists"]              = "CDailyCheckList object_id";
    $backProps["anesth_perops"]            = "CAnesthPerop operation_id";
    $backProps["echanges_hprim"]           = "CEchangeHprim object_id";
    $backProps["echanges_hl7v2"]           = "CExchangeHL7v2 object_id";
    $backProps["echanges_hl7v3"]           = "CExchangeHL7v3 object_id";
    $backProps["echanges_dmp"]             = "CExchangeDMP object_id";
    $backProps["echanges_mvsante"]         = "CExchangeMVSante object_id";
    $backProps["product_orders"]           = "CProductOrder object_id";
    $backProps["op_brancardardage"]        = "CBrancardage operation_id";
    $backProps["besoins_ressources"]       = "CBesoinRessource operation_id";
    $backProps["poses_disp_vasc"]          = "CPoseDispositifVasculaire operation_id";
    $backProps["check_list_categories"]    = "CDailyCheckItemCategory target_id";
    $backProps["liaison_libelle"]          = "CLiaisonLibelleInterv operation_id";
    $backProps["affectations_personnel"]   = "CAffectationPersonnel object_id";
    $backProps["workflow"]                 = "COperationWorkflow operation_id";
    return $backProps;
  }

  function getTemplateClasses(){
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects li�s � l'op�ration
    $tab['COperation'] = $this->_id;
    $tab['CSejour'] = $this->_ref_sejour->_id;
    $tab['CPatient'] = $this->_ref_sejour->_ref_patient->_id;

    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;

    return $tab;
  }

  /**
   * @see parent::check()
   */
  function check() {
    $msg = null;
    $this->completeField("chir_id", "plageop_id", "sejour_id");
    if (!$this->_id && !$this->chir_id) {
      $msg .= "Praticien non valide ";
    }

    // Bornes du s�jour
    $sejour = $this->loadRefSejour();
    $this->loadRefPlageOp();

    if ($this->plageop_id !== null && !$sejour->entree_reelle) {
      $date = CMbDT::date($this->_datetime);
      $entree = CMbDT::date($sejour->entree_prevue);
      $sortie = CMbDT::date($sejour->sortie_prevue);
      if (!CMbRange::in($date, $entree, $sortie)) {
         $msg .= "Intervention du $date en dehors du s�jour du $entree au $sortie";
      }
    }

    // V�rification de la signature de l'anesth�siste pour la visite de pr�-anesth�sie
    if (
        $this->fieldModified("prat_visite_anesth_id") &&
        $this->prat_visite_anesth_id !== null &&
        $this->prat_visite_anesth_id != CAppUI::$user->_id
    ) {
      $anesth = new CUser();
      $anesth->load($this->prat_visite_anesth_id);

      if (!CUser::checkPassword($anesth->user_username, $this->_password_visite_anesth)) {
        $msg .= "Mot de passe incorrect";
      }
    }

    return $msg . parent::check();
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    $msg = parent::delete();
    $this->loadRefPlageOp();
    $this->_ref_plageop->reorderOp();
    return $msg;
  }

  /**
   * @see parent::merge()
   */
  function merge($objects, $fast = false) {
    // Remove operation miners as they prevent from merging
    $miners = $this->loadBackRefs("workflow");
    foreach ($objects as $_object) {
      $miners = array_merge($miners, $_object->loadBackRefs("workflow"));
    }
    foreach ($miners as $_miner) {
      $_miner->delete();
    }

    return parent::merge($objects, $fast);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_time_op = $this->temp_operation;
    $this->_time_urgence = $this->time_operation;

    /** @var CTypeAnesth $type_anesth */
    $this->_ref_type_anesth = $type_anesth = $this->loadFwdRef("type_anesth", true);
    $this->_lu_type_anesth = $type_anesth->name;

    $this->_fin_prevue = CMbDT::addTime($this->time_operation, $this->temp_operation);

    if ($this->debut_op && $this->fin_op && $this->fin_op > $this->debut_op) {
      $this->_duree_interv = CMbDT::subTime($this->debut_op, $this->fin_op);
    }
    if ($this->pose_garrot && $this->retrait_garrot && $this->retrait_garrot > $this->pose_garrot) {
      $this->_duree_garrot = CMbDT::subTime($this->pose_garrot, $this->retrait_garrot);
    }
    if ($this->induction_debut && $this->induction_fin && $this->induction_fin > $this->induction_debut) {
      $this->_duree_induction = CMbDT::subTime($this->induction_debut, $this->induction_fin);
    }
    if ($this->entree_salle && $this->sortie_salle && $this->sortie_salle>$this->entree_salle) {
      $this->_presence_salle = CMbDT::subTime($this->entree_salle, $this->sortie_salle);
    }
    if ($this->entree_reveil && $this->sortie_reveil_possible && $this->sortie_reveil_possible > $this->entree_reveil) {
      $this->_duree_sspi = CMbDT::subTime($this->entree_reveil, $this->sortie_reveil_possible);
    }

    $this->_acte_depassement        = $this->depassement;
    $this->_acte_depassement_anesth = $this->depassement_anesth;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if (is_array($this->_codes_ccam) && count($this->_codes_ccam)) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }

    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      // @TODO: change it to use removeValue
      while ($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if ($this->_time_op !== null) {
      $this->temp_operation = $this->_time_op;
    }
    if ($this->_time_urgence !== null) {
      $this->time_operation = $this->_time_urgence;
    }
    elseif ($this->_horaire_voulu) {
      $this->horaire_voulu = $this->_horaire_voulu;
    }

    $this->completeField('rank', 'plageop_id');

    if ($this->_move) {
      $op = new COperation;
      $op->plageop_id = $this->plageop_id;

      switch ($this->_move) {
        case 'before':
          $op->rank = $this->rank-1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank -= 1;
          }
          break;

        case 'after':
          $op->rank = $this->rank+1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank += 1;
          }
          break;

        case 'out':
          $this->rank = 0;
          $this->time_operation = '00:00:00';
          $this->pause = '00:00:00';
          break;

        case 'last':
          if ($op->loadMatchingObject('rank DESC')) {
            $this->rank = $op->rank+1;
          }
          break;
        default;
      }

      $this->_reorder_rank_voulu = true;
      $this->_move = null;
    }
  }

  /**
   * Prepare the alert before storage
   *
   * @return string Alert comments if necessary, null if no alert
   */
  function prepareAlert() {
    // Cr�ation d'un alerte sur l'intervention
    $comments = null;
    /** @var self $old */
    $old = $this->_old;
    if ($old->rank || ($this->materiel && $this->commande_mat)) {
      $this->loadRefPlageOp();
      $old->loadRefPlageOp();

      if ($this->fieldModified("annulee", "1")) {
        // Alerte sur l'annulation d'une intervention
        $comments .= "L'intervention a �t� annul�e pour le ".CMbDT::format($this->_datetime, CAppUI::conf("datetime")).".";
      }
      elseif (CMbDT::date(null, $this->_datetime) != CMbDT::date(null, $old->_datetime)) {
        // Alerte sur le d�placement d'une intervention
        $comments .= "L'intervention a �t� d�plac�e du ".CMbDT::format($old->_datetime, CAppUI::conf("date")).
          " au ".CMbDT::format($this->_datetime, CAppUI::conf("date")).".";
      }
      elseif ($this->fieldModified("materiel") && $this->commande_mat) {
        // Alerte sur la commande de mat�riel
        $comments .= "Le materiel a �t� modifi� \n - Ancienne valeur : ".$old->materiel.
          " \n - Nouvelle valeur : ".$this->materiel;
      }
      else {
        // Aucune alerte
        return null;
      }

      // Compl�ment d'alerte
      if ($old->rank) {
        $comments .= "\nL'intervention avait �t� valid�e.";
      }
      if ($this->materiel && $this->commande_mat) {
        $comments .= "\nLe materiel avait �t� command�.";
      }
    }

    return $comments;
  }

  /**
   * Create an alert if comments is not empty
   *
   * @param string  $comments Comments of the alert
   * @param boolean $update   Search an existing alert for updating
   * @param string  $tag      Tag of the alert
   *
   * @return string Store-like message
   */
  function createAlert($comments, $update = false, $tag = "mouvement_intervention") {
    if (!$comments) {
      return null;
    }

    $alerte = new CAlert();
    $alerte->setObject($this);
    $alerte->tag = $tag;
    $alerte->handled = "0";
    $alerte->level = "medium";
    if ($update) {
      $alerte->loadMatchingObject();
    }
    $alerte->comments = $comments;
    return $alerte->store();
  }

  /**
   * @see parent::store()
   */
  function store($reorder = true) {
    /** @var self $old */
    $old = $this->loadOldObject();

    $this->completeField(
      "annulee",
      "rank",
      "codes_ccam",
      "plageop_id",
      "chir_id",
      "materiel",
      "commande_mat",
      "date"
    );

    // Si on a une plage, la date est celle de la plage
    if ($this->plageop_id) {
      $plage = $this->loadRefPlageOp();
      $this->date = $plage->date;
    }

    // Si on choisit une plage, on copie la salle
    if ($this->fieldValued("plageop_id")) {
      $plage = $this->loadRefPlageOp();
      $this->salle_id = $plage->salle_id;
    }

    // Cas d'une plage que l'on quitte
    /** @var CPlageOp $old_plage */
    $old_plage = null;
    if ($this->fieldAltered("plageop_id") && $old->rank) {
      $old_plage = $old->loadRefPlageOp();
    }

    $comments = $this->prepareAlert();
    $place_after_interv_id = $this->_place_after_interv_id;
    $this->_place_after_interv_id = null;


    // Pr�-remplissage de la dur�e pr�op si c'est une nouvelle intervention
    if (!$this->_id && !$this->duree_preop) {
      $patient = $this->loadRefSejour()->loadRefPatient();

      if ($patient->_annees >= 18) {
        $this->duree_preop = "00:" . CAppUI::conf("dPplanningOp COperation duree_preop_adulte") . ":00";
      }
      else {
        $this->duree_preop = "00:" . CAppUI::conf("dPplanningOp COperation duree_preop_enfant") . ":00";
      }
    }

    // On recopie la sortie r�veil possible sur le r�el si pas utilis�e en config
    if (!CAppUI::conf("dPsalleOp COperation use_sortie_reveil_reel", CGroups::loadCurrent()->_guid)) {
      $this->sortie_reveil_reel = $this->sortie_reveil_possible;
    }

    // Cr�ation d'une alerte si modification du libell� et/ou du c�t�
    if ($this->_id && ($this->fieldModified("libelle") || $this->fieldModified("cote"))) {
      $alerte = "";
      $date = CMbDT::dateToLocale(CMbDT::date());

      if ($this->fieldModified("libelle")) {
        $alerte = "Le libell� a �t� modifi� le $date\n".
          "Ancienne valeur : ".$old->getFormattedValue("libelle").
          "\nNouvelle valeur : ".$this->getFormattedValue("libelle");
      }
      $this->createAlert($alerte, true, "libelle");
      $alerte = "";
      if ($this->fieldModified("cote")) {
        $alerte = "Le c�t� a �t� modifi� le $date : \n".
           "Ancienne valeur : " . $old->getFormattedValue("cote") .
           "\nNouvelle valeur : " . $this->getFormattedValue("cote");
      }
      $this->createAlert($alerte, true, "cote");
    }
    // Standard storage
    if ($msg = parent::store()) {
      return $msg;
    }

    // Cr�ation des besoins d'apr�s le protocole s�lectionn�
    // Ne le faire que pour une nouvelle intervention
    // Pour une intervention existante, l'application du protocole
    // store les protocoles
    if (
        CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert" &&
        $this->_types_ressources_ids && !$old->_id
    ) {
      $types_ressources_ids = explode(",", $this->_types_ressources_ids);

      foreach ($types_ressources_ids as $_type_ressource_id) {
        $besoin = new CBesoinRessource;
        $besoin->type_ressource_id = $_type_ressource_id;
        $besoin->operation_id = $this->_id;
        if ($msg = $besoin->store()) {
          return $msg;
        }
      }
    }

    $this->createAlert($comments);

    $sejour = $this->loadRefSejour();
    $do_store_sejour = false; // Flag pour storer le s�jour une seule fois

    // Mise � jour du type de PeC du s�jour en Chirurgical si pas d�ja obst�trique
    $sejour->completeField("type_pec");
    if (!$this->_id && $sejour->type_pec != "O") {
      $sejour->type_pec = "C";
      $do_store_sejour = true;
    }

    // Cas d'une annulation
    if (!$this->annulee) {
      // Si pas une annulation on recup�re le sejour
      // et on regarde s'il n'est pas annul�
      if ($sejour->annule) {
        $sejour->annule = 0;
        $do_store_sejour = true;
      }

      // Application des protocoles de prescription en fonction de l'operation->_id
      if ($this->_protocole_prescription_chir_id || $this->_protocole_prescription_anesth_id) {
        $sejour->_protocole_prescription_chir_id   = $this->_protocole_prescription_chir_id;
        $sejour->_protocole_prescription_anesth_id = $this->_protocole_prescription_anesth_id;
        $sejour->applyProtocolesPrescription($this->_id);

        // On les nullify pour eviter de les appliquer 2 fois
        $this->_protocole_prescription_anesth_id = null;
        $this->_protocole_prescription_chir_id   = null;
        $sejour->_protocole_prescription_chir_id   = null;
        $sejour->_protocole_prescription_anesth_id = null;
      }
    }
    elseif ($this->rank != 0 && !CAppUI::conf("dPplanningOp COperation save_rank_annulee_validee")) {
      $this->rank = 0;
      $this->time_operation = "00:00:00";
    }

    // Store du s�jour (une seule fois)
    if ($do_store_sejour) {
      $sejour->store();
    }

    // V�rification qu'on a pas des actes CCAM cod�s obsol�tes
    if ($this->codes_ccam) {
      $this->loadRefsActesCCAM();
      foreach ($this->_ref_actes_ccam as $keyActe => $acte) {
        if (stripos($this->codes_ccam, $acte->code_acte) === false) {
          $this->_ref_actes_ccam[$keyActe]->delete();
        }
      }
    }

    $reorder_rank_voulu = $this->_reorder_rank_voulu;
    $this->_reorder_rank_voulu = null;

    if ($this->plageop_id) {
      $plage = $this->loadRefPlageOp();
      // Cas de la cr�ation dans une plage de sp�cialit�
      if ($plage->spec_id && $plage->unique_chir) {
        $plage->chir_id = $this->chir_id;
        $plage->spec_id = "";
        $plage->store();
      }

      // Placement de l'interv selon la preference (placement souhait�)
      if ($place_after_interv_id) {
        $plage->loadRefsOperations(false, "rank, rank_voulu, horaire_voulu", true);

        unset($plage->_ref_operations[$this->_id]);

        if ($place_after_interv_id == -1) {
          $reorder = true;
          $reorder_rank_voulu = true;
          $plage->_ref_operations = CMbArray::mergeKeys(
            array($this->_id => $this), $plage->_ref_operations
          ); // To preserve keys (array_unshift does not)
        }
        elseif (isset($plage->_ref_operations[$place_after_interv_id])) {
          $reorder = true;
          $reorder_rank_voulu = true;
          CMbArray::insertAfterKey($plage->_ref_operations, $place_after_interv_id, $this->_id, $this);
        }

        if ($reorder_rank_voulu) {
          $plage->_reorder_up_to_interv_id = $this->_id;
        }
      }
    }

    // Gestion du tarif et precodage des actes
    if ($this->_bind_tarif && $this->_id) {
      if ($msg = $this->bindTarif()) {
        return $msg;
      }
    }

    // Standard storage bis
    if ($msg = parent::store()) {
      return $msg;
    }

    // R�ordonnancement post-store
    if ($reorder) {
      // R�ordonner la plage que l'on quitte
      if ($old_plage) {
        $old_plage->reorderOp();
      }

      $this->_ref_plageop->reorderOp($reorder_rank_voulu ? CPlageOp::RANK_REORDER : null);
    }
    return null;
  }

  /**
   * @see parent::loadGroupList()
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    // Filtre sur l'�tablissement
    $g = CGroups::loadCurrent();
    $where["sejour.group_id"] = "= '$g->_id'";

    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadRefPraticien()->loadRefFunction();
    $this->loadRefAnesth()->loadRefFunction();
    $this->loadRefPatient();
    $this->_ref_sejour->_ref_patient->loadRefPhotoIdentite();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();
    $this->loadRefPatient();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }

  /**
   * Chargmeent du chirurgien
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefChir($cache = true) {
    $this->_ref_chir = $this->loadFwdRef("chir_id", $cache);
    $this->_praticien_id = $this->_ref_chir->_id;
    return $this->_ref_chir;
  }

  /**
   * Chargement du deuxi�me chirurgien optionnel
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefChir2($cache = true) {
    return $this->_ref_chir_2 = $this->loadFwdRef("chir_2_id", $cache);
  }

  /**
   * Chargement du troisi�me chirurgien optionnel
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefChir3($cache = true) {
    return $this->_ref_chir_3 = $this->loadFwdRef("chir_3_id", $cache);
  }

  /**
   * Chargement du quatri�me chirurgien optionnel
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefChir4($cache = true) {
    return $this->_ref_chir_4 = $this->loadFwdRef("chir_4_id", $cache);
  }

  /**
   * Chargement du praticien responsable
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefPraticien($cache = true) {
    $this->_ref_praticien = $this->loadRefChir($cache);
    $this->_ref_executant = $this->_ref_praticien;
    return $this->_ref_praticien;
  }

  function getActeExecution() {
    $this->loadRefPlageOp();
  }

  /**
   * @return CAffectation
   */
  function loadRefAffectation() {
    $this->loadRefPlageOp();

    $sejour = $this->loadRefSejour();
    $this->_ref_affectation = $sejour->getCurrAffectation($this->_datetime);
    if (!$this->_ref_affectation->_id) {
      $this->_ref_affectation = $sejour->loadRefFirstAffectation();
    }
    $this->_ref_affectation->loadView();

    return $this->_ref_affectation;
  }

  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }


  function loadRefPoste() {
    return $this->_ref_poste = $this->loadFwdRef("poste_sspi_id");
  }

  /**
   * Met � jour les information sur la salle
   * N�cessiste d'avoir charg� la plage op�ratoire au pr�alable
   * 
   * @return CSalle
   */
  function updateSalle() {
    if ($this->plageop_id && $this->salle_id) {
      $this->_deplacee = $this->_ref_plageop->salle_id != $this->salle_id;
    }

    // Evite de recharger la salle quand ce n'est pas n�cessaire
    if ($this->plageop_id && !$this->_deplacee) {
      return $this->_ref_salle =& $this->_ref_plageop->_ref_salle;
    }
    else {
      return $this->_ref_salle = $this->loadFwdRef("salle_id", true);
    }
  }

  /**
   * Chargement de l'anesthesiste, sur l'op�ration si disponible, sinon sur la plage
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CMediusers
   */
  function loadRefAnesth($cache = true) {
    if ($this->anesth_id) {
      return $this->_ref_anesth = $this->loadFwdRef("anesth_id", $cache);
    }
    if ($this->plageop_id) {
      $plage = $this->_ref_plageop ?
        $this->_ref_plageop :
        $this->loadFwdRef("plageop_id", $cache);

      return $this->_ref_anesth = $plage->loadFwdRef("anesth_id", $cache);
    }
    return $this->_ref_anesth = new CMediusers();
  }

  /**
   * Chargement de la consultation anesth�siste pour l'oop�ration courante
   *
   * @param bool $cache Utilisation du cache
   *
   * @return CConsultAnesth
   */
  function loadRefVisiteAnesth($cache = true) {
    return $this->_ref_anesth_visite = $this->loadFwdRef("prat_visite_anesth_id", $cache);
  }

  /**
   * Chargement de la plage op�ratoire
   * 
   * @param bool $cache Utilisation du cache
   * 
   * @return CPlageOp
   */
  function loadRefPlageOp($cache = true) {

    if (!$this->_ref_plageop) {
      $this->_ref_plageop = $this->loadFwdRef("plageop_id", $cache);
    }

    $this->loadRefVisiteAnesth();

    /** @var CPlageOp $plage */
    $plage = $this->_ref_plageop;

    if ($plage->_id) {
      // Avec plage d'op�ration
      $plage->loadRefsFwd($cache);

      if ($this->anesth_id) {
        $this->loadRefAnesth();
      }
      else {
        $this->_ref_anesth = $plage->_ref_anesth;
      }
    }
    else {
      // Hors plage
      $this->loadRefAnesth();
    }

    // Champs d�riv�s
    $this->updateSalle();
    $this->updateDatetimes();
    $this->updateView();

    return $plage;
  }

  function updateView() {
    $this->_view = "Intervention";

    if (!$this->plageop_id) {
      $this->_view .= " [HP]";
    }

    $this->_view .= " le " . CMbDT::format($this->_datetime, CAppUI::conf("date"));

    if ($this->_ref_patient) {
      $this->_view .= " de ". $this->_ref_patient->_view;
    }

    if ($this->_ref_chir) {
      $this->_view .= " par le Dr ". $this->_ref_chir->_view;
    }
  }

  /**
   * Calculs sur les champs d'horodatage d�riv�s, notamment en fonction de la plage
   *
   * @return void;
   */
  function updateDatetimes() {
    $plage = $this->_ref_plageop;
    $date = $plage && $plage->_id ? $plage->date : $this->date;

    // Calcul du nombre de jour entre la date actuelle et le jour de l'operation
    $this->_compteur_jour = CMbDT::daysRelative($date, CMbDT::date());

    // Horaire global
    if ($this->time_operation && $this->time_operation != "00:00:00") {
      $this->_datetime = "$date $this->time_operation";
    }
    elseif ($this->horaire_voulu && $this->horaire_voulu != "00:00:00") {
      $this->_datetime = "$date $this->horaire_voulu";
    }
    elseif ($plage && $plage->_id) {
      $this->_datetime = "$date ".$plage->debut;
    }
    else {
      $this->_datetime = "$date 00:00:00";
    }

    $this->_datetime_best     = $this->_datetime;
    $this->_datetime_reel     = "$date $this->debut_op";
    if ($this->debut_op) {
      $this->_datetime_best = $this->_datetime_reel;
    }
    $this->_datetime_reel_fin = "$date $this->fin_op";

    // Heure standard d'ex�cution des actes
    if ($this->fin_op) {
      $this->_acte_execution = $this->_datetime_reel_fin;
    }
    elseif ($this->debut_op) {
      $this->_acte_execution = CMbDT::addDateTime($this->temp_operation, $this->_datetime_reel);
    }
    elseif ($this->time_operation != "00:00:00") {
      $this->_acte_execution = CMbDT::addDateTime($this->temp_operation, $this->_datetime);
    }
    else {
      $this->_acte_execution = $this->_datetime;
    }
  }

  /**
   * @see parent::preparePossibleActes()
   *
   * @return void
   */
  function preparePossibleActes() {
    $this->loadRefPlageOp();
  }

  /**
   * Chargement du dossier d'anesth�sie
   *
   * @return CConsultAnesth
   */
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return $this->_ref_consult_anesth;
    }

    $order = "date DESC";
    $ljoin = array(
      "consultation" => "consultation.consultation_id = consultation_anesth.consultation_id",
      "plageconsult" => "consultation.plageconsult_id = plageconsult.plageconsult_id"
    );
    return $this->_ref_consult_anesth = $this->loadFirstBackRef("dossiers_anesthesie", $order, null, $ljoin);
  }

  /**
   * Chargement de la consult de chirurgie avant l'intervention,
   * cad la derni�re consultation pour le patient par le chirurgien avant l'internvention et hors du s�jour
   *
   * @return CConsultation
   */
  public function loadRefConsultChir() {
    $sejour = $this->loadRefSejour();
    $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
    $where["patient_id"] = "= '$sejour->patient_id'";
    $where["chir_id"]    = "= '$this->chir_id'";
    $where["date"]       = "< '$sejour->entree'";
    $where[] = "sejour_id IS NULL OR sejour_id != '$this->sejour_id'";
    $order = "date DESC";
    $consult = new CConsultation;
    $consult->loadObject($where, $order, null, $ljoin);
    return $this->_ref_consult_chir = $consult;
  }

  /**
   * Chargement du s�jour
   * 
   * @param bool $cache Utilisation du cache
   * 
   * @return CSejour
   */
  function loadRefSejour($cache = true) {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
  }

  /**
   * Chargement des gestes perop
   * 
   * @return $this->_ref_anesth_perops
   */
  function loadRefsAnesthPerops() {
    return $this->_ref_anesth_perops = $this->loadBackRefs("anesth_perops", "datetime");
  }

  /**
   * Chargement des poses de dispositif vasculaire
   *
   * @param bool $count_check_lists Calcul du nombre de checklist remplies
   *
   * @return CPoseDispositifVasculaire[]
   */
  function loadRefsPosesDispVasc($count_check_lists = false){
    $this->_ref_poses_disp_vasc = $this->loadBackRefs("poses_disp_vasc", "date");

    if ($count_check_lists) {
      foreach ($this->_ref_poses_disp_vasc as $_pose) {
        /** @var CPoseDispositifVasculaire $_pose */
        $_pose->countSignedCheckLists();
      }
    }

    return $this->_ref_poses_disp_vasc;
  }

  /**
   * Chargement du patient concern�
   *
   * @param bool $cache Utilisation du cache
   * 
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    $sejour = $this->loadRefSejour($cache);
    $patient = $sejour->loadRefPatient($cache);
    $this->_ref_patient = $patient;
    $this->_patient_id = $patient->_id;
    $this->loadFwdRef("_patient_id", $cache);
    return $patient;
  }

  /**
   * Chargement de la salle concern�e
   *
   * @return CSalle
   */
  function loadRefSalle() {
    return $this->_ref_salle = $this->loadFwdRef("salle_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   * @deprecated
   */
  function loadRefsFwd($cache = true) {
    $consult_anesth = $this->loadRefsConsultAnesth();
    $consult_anesth->countDocItems();

    $consultation = $consult_anesth->loadRefConsultation();
    $consultation->countDocItems();
    $consultation->canRead();
    $consultation->canEdit();

    $this->loadRefPlageOp($cache);
    $this->loadExtCodesCCAM();

    $this->loadRefChir($cache)->loadRefFunction();
    $this->loadRefPatient($cache);
    $this->updateView();
  }

  /**
   * Chargement du bloodsalvage associ�
   *
   * @return CBloodSalvage
   */
  function loadRefBloodSalvage() {
    return $this->_ref_blood_salvage = $this->loadUniqueBackRef("blood_salvages");
  }

  /**
   * Chargement des besoins en ressources materielles
   *
   * @return CBesoinRessource[]
   */
  function loadRefsBesoins() {
    return $this->_ref_besoins = $this->loadBackRefs("besoins_ressources");
  }

  /**
   * Charge le pack de graphiques
   *
   * @return CSupervisionGraphPack
   */
  function loadRefGraphPack() {
    return $this->_ref_graph_pack = $this->loadFwdRef("graph_pack_id");
  }

  /**
   * Charge le validateur de la sortie
   *
   * @return CMediusers
   */
  function loadRefSortieLocker() {
    return $this->_ref_sortie_locker = $this->loadFwdRef("sortie_locker_id", true);
  }

  /**
   * Charge le workflow d'op�ration
   *
   * @return COperationWorkflow
   */
  function loadRefWorkflow() {
    return $this->_ref_workflow = $this->loadUniqueBackRef("workflow");
  }

  /**
   * @see parent::loadRefsBack()
   * @deprecated
   */
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActes();
    $this->loadRefsDocs();
  }

  /**
   * V�rifie si une intervention est consid�r�e
   * comme termin�e concernant le codage des actes
   *
   * @return bool
   */
  function isCoded() {
    $this->loadRefPlageOp();
    $this->_coded = (CAppUI::conf("dPsalleOp COperation modif_actes") == "never") ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "oneday" && $this->date > CMbDT::date()) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "button" && $this->_ref_plageop->actes_locked) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "facturation" && $this->facture);
    return $this->_coded;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_chir) {
      $this->loadRefChir();
    }

    if (!$this->_ref_anesth) {
      $this->loadRefPlageOp();
    }

    return (
      ($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType))
      && $this->_ref_module->getPerm($permType)
    );
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_sejour->loadRefsFwd();

    // Chargement du fillTemplate du praticien
    $this->_ref_chir->fillTemplate($template);

    // Chargement du fillTemplate du sejour
    $this->_ref_sejour->fillTemplate($template);

    $consult_anesth = $this->loadRefsConsultAnesth();
    $consult_anesth->_ref_operation = $this;
    $consult_anesth->fillLimitedTemplate($template);

    // Chargement du fillTemplate de l'op�ration
    $this->fillLimitedTemplate($template);
  }

  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd(1);
    $this->loadRefPraticien();
    $this->loadRefChir2();
    $this->loadRefChir3();
    $this->loadRefChir4();
    $this->loadRefsFiles();
    $this->loadAffectationsPersonnel();

    $plageop = $this->_ref_plageop;
    $plageop->loadAffectationsPersonnel();

    foreach ($this->_ext_codes_ccam as $_code) {
      $_code->getRemarques();
      $_code->getActivites();
    }

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Op�ration - Chirurgien",   $this->_ref_chir->_id   ? "Dr " . $this->_ref_chir->_view : '');
    $template->addProperty("Op�ration - Chirurgien 2", $this->_ref_chir_2->_id ? "Dr " . $this->_ref_chir_2->_view : '');
    $template->addProperty("Op�ration - Chirurgien 3", $this->_ref_chir_3->_id ? "Dr " . $this->_ref_chir_3->_view : '');
    $template->addProperty("Op�ration - Chirurgien 4", $this->_ref_chir_4->_id ? "Dr " . $this->_ref_chir_4->_view : '');

    $template->addProperty("Op�ration - Anesth�siste - nom"        , @$this->_ref_anesth->_user_last_name);
    $template->addProperty("Op�ration - Anesth�siste - pr�nom"     , @$this->_ref_anesth->_user_first_name);
    $template->addProperty("Op�ration - Anesth�sie"                , $this->_lu_type_anesth);
    $template->addProperty("Op�ration - libell�"                   , $this->libelle);
    $template->addProperty("Op�ration - CCAM1 - code"              , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Op�ration - CCAM1 - description"       , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Op�ration - CCAM1 - montant activit� 1", @$this->_ext_codes_ccam[0]->activites[1]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM1 - montant activit� 4", @$this->_ext_codes_ccam[0]->activites[4]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM2 - code"              , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Op�ration - CCAM2 - description"       , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Op�ration - CCAM2 - montant activit� 1", @$this->_ext_codes_ccam[1]->activites[1]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM2 - montant activit� 4", @$this->_ext_codes_ccam[1]->activites[4]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM3 - code"              , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Op�ration - CCAM3 - description"       , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Op�ration - CCAM3 - montant activit� 1", @$this->_ext_codes_ccam[2]->activites[1]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM3 - montant activit� 4", @$this->_ext_codes_ccam[2]->activites[4]->phases[0]->tarif);
    $template->addProperty("Op�ration - CCAM - codes"              , implode(" - ", $this->_codes_ccam));
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      $this->loadRefsActes();
      $template->addProperty("Op�ration - TARMED - codes"            , CActeTarmed::actesHtml($this), '', false);
      $template->addProperty("Op�ration - Caisse - codes"            , CActeCaisse::actesHtml($this), '', false);
    }
    $template->addProperty(
      "Op�ration - CCAM - descriptions", implode(" - ", CMbArray::pluck($this->_ext_codes_ccam, "libelleLong"))
    );
    $template->addProperty("Op�ration - salle"                     , @$this->_ref_salle->nom);
    $template->addProperty("Op�ration - c�t�"                      , $this->cote);
    $template->addProperty("Op�ration - position"                  , $this->getFormattedValue("position"));
    $template->addDateProperty("Op�ration - date"             , $this->_datetime_best != " 00:00:00" ? $this->_datetime_best : "");
    $template->addLongDateProperty("Op�ration - date longue"  , $this->_datetime_best != " 00:00:00" ? $this->_datetime_best : "");
    $template->addTimeProperty("Op�ration - heure"            , $this->time_operation);
    $template->addTimeProperty("Op�ration - dur�e"            , $this->temp_operation);
    $template->addTimeProperty("Op�ration - dur�e r�elle"     , $this->_duree_interv);
    $template->addTimeProperty("Op�ration - entr�e bloc"      , $this->entree_salle);
    $template->addTimeProperty("Op�ration - pose garrot"      , $this->pose_garrot);
    $template->addTimeProperty("Op�ration - d�but induction"  , $this->induction_debut);
    $template->addTimeProperty("Op�ration - d�but op"         , $this->debut_op);
    $template->addTimeProperty("Op�ration - fin op"           , $this->fin_op);
    $template->addTimeProperty("Op�ration - fin induction"    , $this->induction_fin);
    $template->addTimeProperty("Op�ration - retrait garrot"   , $this->retrait_garrot);
    $template->addTimeProperty("Op�ration - sortie bloc"      , $this->sortie_salle);
    $template->addTimeProperty("Op�ration - entr�e SSPI"      , $this->entree_reveil);
    $template->addTimeProperty("Op�ration - sortie SSPI"      , $this->sortie_reveil_reel);

    if (CModule::getActive("mvsante")) {
      $template->addTimeProperty("Op�ration - Remise au chirurgien", $this->remise_chir);
      /** @var CLiaisonLibelleInterv[] $liaisons_libelles */
      $liaisons_libelles = $this->loadBackRefs("liaison_libelle", "numero");
      CMbObject::massLoadFwdRef($liaisons_libelles, "libelleop_id");

      $libelles = array(0 => "", 1 => "", 2 => "", 3 => "");

      foreach ($liaisons_libelles as $_liaison) {
        $libelles[$_liaison->numero - 1] = $_liaison->loadRefLibelle()->nom;
      }

      $template->addProperty("Op�ration - Libell� 1"          , $libelles[0]);
      $template->addProperty("Op�ration - Libell� 2"          , $libelles[1]);
      $template->addProperty("Op�ration - Libell� 3"          , $libelles[2]);
      $template->addProperty("Op�ration - Libell� 4"          , $libelles[3]);
    }

    $template->addProperty("Op�ration - depassement"          , $this->depassement);
    $template->addProperty("Op�ration - exams pre-op"         , $this->examen);
    $template->addProperty("Op�ration - mat�riel"             , $this->materiel);
    $template->addProperty("Op�ration - convalescence"        , $this->_ref_sejour->convalescence);
    $template->addProperty("Op�ration - remarques"            , $this->rques);
    $template->addProperty("Op�ration - Score ASA"            , $this->getFormattedValue("ASA"));

    $consult_anesth = $this->_ref_consult_anesth;
    $consult = $consult_anesth->loadRefConsultation();
    $consult->loadRefPlageConsult();
    $prat = $consult->loadRefPraticien();
    $template->addDateProperty("Op�ration - Consultation anesth�sie - Date", $consult->_id ? $consult->_datetime : "");
    $template->addLongDateProperty("Op�ration - Consultation anesth�sie - Date (longue)", $consult->_id ? $consult->_datetime : "");
    $template->addLongDateProperty(
      "Op�ration - Consultation anesth�sie - Date (longue, minuscule)", $consult->_id ? $consult->_datetime : "", true
    );
    $template->addTimeProperty("Op�ration - Consultation anesth�sie - Heure", $consult->_id ? $consult->_datetime : "");
    $template->addProperty("Op�ration - Consultation anesth�sie - Praticien - Pr�nom", $consult->_id ? $prat->_user_first_name : "");
    $template->addProperty("Op�ration - Consultation anesth�sie - Praticien - Nom", $consult->_id ? $prat->_user_last_name : "");
    $template->addProperty("Op�ration - Consultation anesth�sie - Remarques", $consult->rques);

    /** @var CMediusers $prat_visite */
    $prat_visite = $this->loadFwdRef("prat_visite_anesth_id", true);

    $template->addDateProperty("Op�ration - Visite pr� anesth�sie - Date", $this->date_visite_anesth);
    $template->addLongDateProperty("Op�ration - Visite pr� anesth�sie - Date (longue)", $this->date_visite_anesth);
    $template->addProperty("Op�ration - Visite pr� anesth�sie - Rques", $this->getFormattedValue("rques_visite_anesth"));
    $template->addProperty("Op�ration - Visite pr� anesth�sie - Autorisation", $this->getFormattedValue("autorisation_anesth"));
    $template->addProperty("Op�ration - Visite pr� anesth�sie - Praticien - Pr�nom", $prat_visite->_user_first_name);
    $template->addProperty("Op�ration - Visite pr� anesth�sie - Praticien - Nom", $prat_visite->_user_last_name);

    $template->addBarcode("Op�ration - Code Barre ID"         , $this->_id);

    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Op�ration - Liste des fichiers", $list);

    foreach ($this->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Op�ration - personnel r�el - $locale", $property);
    }

    foreach ($plageop->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Op�ration - personnel pr�vu - $locale", $property);
    }

    $evts = $incidents = array();

    foreach ($this->loadRefsAnesthPerops() as $_evt) {
      if ($_evt->incident) {
        $incidents[] = $_evt;
        continue;
      }
      $evts[] = $_evt;
    }

    $template->addListProperty("Op�ration - Evenements per-op�ratoires", $evts);
    $template->addListProperty("Op�ration - Incidents per-op�ratoires", $incidents);

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Op�ration");
    }

    if (CAppUI::conf("dPsalleOp enable_surveillance_perop")) {
      $obs_view = "";

      if ($template->valueMode && $this->_id && $this->graph_pack_id) {
        /** @var CObservationResultSet[] $list_obr */
        list($list, $grid, $graphs, $labels, $list_obr) = CObservationResultSet::getChronological($this, $this->graph_pack_id);

        foreach ($grid as $_row) {
          /** @var CObservationResult[] $_row */

          foreach ($_row as $_cell) {
            if ($_cell && $_cell->file_id) {
              $_cell->loadRefFile()->getDataUri();
            }
          }
        }

        $smarty = new CSmartyDP("modules/dPpatients");

        // Horizontal
        $smarty->assign("observation_grid", $grid);
        $smarty->assign("observation_labels", $labels);
        $smarty->assign("observation_list", $list_obr);
        $smarty->assign("in_compte_rendu", true);

        $obs_view = $smarty->fetch("inc_observation_results_grid.tpl", '', '', 0);
        $obs_view = preg_replace('`([\\n\\r])`', '', $obs_view);
      }

      $template->addProperty("Op�ration - Tableau supervision", $obs_view, '', false);
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function getDMIAlert(){
    if (!CModule::getActive("dmi")) {
      return null;
    }

    $this->_dmi_prescription_id = null;
    $this->_dmi_praticien_id    = null;

    /** @var CPrescriptionLineDMI[] $lines */
    $lines = $this->loadBackRefs("prescription_dmis");

    if (empty($lines)) {
      return $this->_dmi_alert = "none";
    }

    $auto_validate = CAppUI::conf("dmi CDMI auto_validate");
    if ($auto_validate) {
      return $this->_dmi_alert = "ok";
    }

    foreach ($lines as $_line) {
      if (!isset($this->_dmi_prescription_id)) {
        $this->_dmi_prescription_id = $_line->prescription_id;
        $this->_dmi_praticien_id    = $_line->loadRefPrescription()->praticien_id;
      }

      if ($_line->type != "purchase" && !$_line->isValidated()) {
        return $this->_dmi_alert = "warning";
      }
    }

    return $this->_dmi_alert = "ok";
  }

  function updateHeureUS() {
    $this->_heure_us = $this->duree_preop ? CMbDT::subTime($this->duree_preop, $this->time_operation) : $this->time_operation;
  }

  function getAffectation() {
    $sejour = $this->_ref_sejour;

    if (!$this->_ref_sejour) {
      $sejour = $this->loadRefSejour();
    }

    if (!$this->_datetime_best) {
      $this->loadRefPlageOp();
    }

    $affectation = new CAffectation();
    $order = "entree";
    $where = array();

    $where["sejour_id"] = "= '$this->sejour_id'";

    $moment = $this->_datetime_best;

    // Si l'intervention est en dehors du s�jour,
    // on recadre dans le bon intervalle
    if ($moment < $sejour->entree) {
      $moment = $sejour->entree;
    }

    if ($moment > $sejour->sortie) {
      $moment = $sejour->sortie;
    }

    if (CMbDT::time(null, $moment) == "00:00:00") {
      $where["entree"] = $this->_spec->ds->prepare("<= %", CMbDT::date(null, $moment)." 23:59:59");
      $where["sortie"] = $this->_spec->ds->prepare(">= %", CMbDT::date(null, $moment)." 00:00:01");
    }
    else {
      $where["entree"] = $this->_spec->ds->prepare("<= %", $moment);
      $where["sortie"] = $this->_spec->ds->prepare(">= %", $moment);
    }

    $affectation->loadObject($where, $order);

    return $affectation;
  }

  /**
   * @see parent::completeLabelFields()
   */
  function completeLabelFields(&$fields) {
    if (!isset($this->_from_sejour)) {
      $this->loadRefSejour()->_from_op = 1;
      $this->_ref_sejour->completeLabelFields($fields);
    }
    $this->loadRefPlageOp();
    $this->loadRefAnesth();

    $new_fields = array(
      "ANESTH"  => $this->_ref_anesth->_view,
      "LIBELLE" => $this->libelle,
      "DATE"    => $this->_id ? CMbDT::dateToLocale(CMbDT::date($this->_datetime_best)) : "",
      "COTE"    => $this->getFormattedValue("cote")
    );

    $fields = array_merge($fields, $new_fields);
  }

  function loadBrancardage() {
    if (!CModule::getActive("brancardage")) {
      return null;
    }

    //Chargement de la destination
    $salle = new CSalle();
    $salle->load($this->salle_id);

    $destination = new CDestinationBrancardage();
    $destination->object_id    = $salle->bloc_id;
    $destination->object_class = "CBlocOperatoire";
    $destination->loadMatchingObject();

    //Chargement du brancardage s'il existe
    $brancardage = new CBrancardage();
    $brancardage->operation_id              = $this->_id;
    $brancardage->destinationBrancardage_id = $destination->_id;
    $brancardage->loadMatchingObject("brancardage_id DESC");

    return $this->_ref_brancardage = $brancardage;
  }

  /**
   * Return idex type if it's special (e.g. Idex/...)
   *
   * @param CIdSante400 $idex Idex
   *
   * @return string|null
   */
  function getSpecialIdex(CIdSante400 $idex) {
    if (CModule::getActive("mvsante")) {
      return CMVSante::getSpecialIdex($idex);
    }

    return null;
  }

  function loadPersonnelDisponible() {
    $listPers = array(
      "iade"         => CPersonnel::loadListPers("iade"),
      "op"           => CPersonnel::loadListPers("op"),
      "op_panseuse"  => CPersonnel::loadListPers("op_panseuse"),
      "sagefemme"    => CPersonnel::loadListPers("sagefemme"),
      "manipulateur" => CPersonnel::loadListPers("manipulateur")
    );

    $plage = $this->_ref_plageop;

    if (!$plage) {
      $plage = $this->loadRefPlageOp();
    }

    $listPers = $plage->loadPersonnelDisponible($listPers);

    if (!$this->_ref_affectations_personnel) {
      $this->loadAffectationsPersonnel();
    }

    $affectations_personnel = $this->_ref_affectations_personnel;

    $personnel_ids = array();
    foreach ($affectations_personnel as $_aff_by_type) {
      foreach  ($_aff_by_type as $_aff) {
        if ((!$_aff->debut || !$_aff->fin) && !$_aff->parent_affectation_id) {
          $personnel_ids[] = $_aff->personnel_id;
        }
      }
    }

    // Suppression de la liste des personnels d�j� pr�sents
    foreach ($listPers as $key => $persByType) {
      foreach ($persByType as $_key => $pers) {
        if (in_array($pers->_id, $personnel_ids)) {
          unset($listPers[$key][$_key]);
        }
      }
    }

    return $listPers;
  }
}