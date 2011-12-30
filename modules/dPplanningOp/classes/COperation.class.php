<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class COperation extends CCodable implements IPatientRelated {
  // DB Table key
  var $operation_id  = null;

  // DB References
  var $sejour_id  = null;
  var $chir_id    = null;
  var $anesth_id  = null;
  var $plageop_id = null;

  // DB Fields S@nt�.com communication
  var $code_uf    = null;
  var $libelle_uf = null;

  // DB Fields
  var $salle_id       = null;
  var $date           = null;
  var $libelle        = null;
  var $cote           = null;
  var $temp_operation = null;
  var $pause          = null;
  var $time_operation = null;
  var $examen         = null;
  var $materiel       = null;
  var $commande_mat   = null;
  var $info           = null;
  var $type_anesth    = null;  
  var $rques          = null;
  var $rank           = null;
  var $anapath        = null;
  var $labo           = null;
  var $prothese       = null;

  var $depassement        = null;
  var $forfait            = null;
  var $fournitures        = null;
  var $depassement_anesth = null;
  
  var $annulee = null;
  
  // Timings enregistr�s
  var $debut_prepa_preop = null;
  var $fin_prepa_preop   = null;
  var $entree_bloc       = null;
  var $entree_salle      = null;
  var $pose_garrot       = null;
  var $debut_op          = null;
  var $fin_op            = null;
  var $retrait_garrot    = null;
  var $sortie_salle      = null;
  var $entree_reveil     = null;
  var $sortie_reveil     = null;
  var $induction_debut   = null;
  var $induction_fin     = null;
  var $horaire_voulu     = null;
  
  // V�rification du c�t�
  var $cote_admission      = null;
  var $cote_consult_anesth = null;
  var $cote_hospi          = null;
  var $cote_bloc           = null;
  
  // Visite de pr�anesth�sie
  var $date_visite_anesth    = null;
  var $prat_visite_anesth_id = null;
  var $rques_visite_anesth   = null;
  var $autorisation_anesth   = null;
  
  // Form fields
  var $_hour_op         = null;
  var $_min_op          = null;
  var $_hour_urgence    = null;
  var $_min_urgence     = null;
  var $_lu_type_anesth  = null;
  var $_codes_ccam      = array();
  var $_duree_interv    = null;
  var $_duree_garrot    = null;
  var $_duree_induction = null;
  var $_presence_salle  = null;
  var $_duree_sspi      = null;
  var $_hour_voulu      = null;
  var $_min_voulu       = null;
  var $_deplacee        = null;
  var $_compteur_jour   = null;
  var $_pause_min       = null;
  var $_pause_hour      = null;
  var $_protocole_prescription_anesth_id = null;
  var $_protocole_prescription_chir_id   = null;
  var $_move            = null;
  var $_password_visite_anesth = null;
  var $_patient_id      = null;
  
  // Distant fields
  var $_datetime          = null;
  var $_datetime_reel     = null;
  var $_datetime_reel_fin = null;
  var $_ref_affectation   = null;
  
  // Links
  var $_link_editor = null;
  var $_link_viewer = null;

  // References
  var $_ref_chir           = null;
  var $_ref_plageop        = null;
  var $_ref_salle          = null;
  var $_ref_anesth         = null;
  var $_ref_type_anesth    = null;
  var $_ref_consult_anesth = null;
  var $_ref_anesth_visite  = null;
  var $_ref_actes_ccam     = array();
  var $_ref_echange_hprim  = null;
  var $_ref_anesth_perops  = null;
  var $_ref_naissances     = null;
  
  // External references
  var $_ext_codes_ccam = null;

 //Filter Fields
  var $_date_min      = null;
  var $_date_max      = null;
  var $_plage        = null;
  var $_service      = null;
  var $_intervention = null;
  var $_specialite    = null;
  var $_scodes_ccam  = null;
  var $_prat_id      = null;
  var $_bloc_id      = null;
  var $_ccam_libelle = null;

  function COperation() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp COperation locked");
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'operations';
    $spec->key   = 'operation_id';
    $spec->measureable = true;
    $spec->events = array(
      "checklist" => array(
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
  
  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]          = "ref notNull class|CSejour";
    $specs["chir_id"]            = "ref notNull class|CMediusers seekable";
    $specs["anesth_id"]          = "ref class|CMediusers";
    $specs["plageop_id"]         = "ref class|CPlageOp seekable show|0";
    $specs["pause"]              = "time show|0";
    $specs["salle_id"]           = "ref class|CSalle";
    $specs["date"]               = "date";
    $specs["code_uf"]            = "str length|3";
    $specs["libelle_uf"]         = "str maxLength|35";
    $specs["libelle"]            = "str seekable autocomplete dependsOn|chir_id";
    $specs["cote"]               = "enum notNull list|droit|gauche|bilat�ral|total|inconnu default|inconnu";
    $specs["temp_operation"]     = "time show|0";
    $specs["debut_prepa_preop"]  = "time show|0";
    $specs["fin_prepa_preop"]    = "time show|0";
    $specs["entree_salle"]       = "time show|0";
    $specs["sortie_salle"]       = "time show|0";
    $specs["time_operation"]     = "time show|0";
    $specs["examen"]             = "text helped";
    $specs["materiel"]           = "text helped seekable show|0";
    $specs["commande_mat"]       = "bool show|0";
    $specs["info"]               = "bool";
    $specs["type_anesth"]        = "ref class|CTypeAnesth";
    $specs["rques"]              = "text helped";
    $specs["rank"]               = "num max|255 show|0";
    $specs["depassement"]        = "currency min|0 confidential show|0";
    $specs["forfait"]            = "currency min|0 confidential show|0";
    $specs["fournitures"]        = "currency min|0 confidential show|0";
    $specs["depassement_anesth"] = "currency min|0 confidential show|0";
    $specs["annulee"]            = "bool show|0";
    $specs["pose_garrot"]        = "time show|0";
    $specs["debut_op"]           = "time show|0";
    $specs["fin_op"]             = "time show|0";
    $specs["retrait_garrot"]     = "time show|0";
    $specs["entree_reveil"]      = "time show|0";
    $specs["sortie_reveil"]      = "time show|0";
    $specs["induction_debut"]    = "time show|0";
    $specs["induction_fin"]      = "time show|0";
    $specs["entree_bloc"]        = "time show|0";
    $specs["anapath"]            = "enum list|1|0|? default|? show|0";
    $specs["labo"]               = "enum list|1|0|? default|? show|0";
    $specs["prothese"]           = "enum list|1|0|? default|? show|0";
    $specs["horaire_voulu"]      = "time show|0";
    
    $specs["cote_admission"]     = "enum list|droit|gauche show|0";
    $specs["cote_consult_anesth"]= "enum list|droit|gauche show|0";
    $specs["cote_hospi"]         = "enum list|droit|gauche show|0";
    $specs["cote_bloc"]          = "enum list|droit|gauche show|0";
    
    // Visite de pr�anesth�sie
    $specs["date_visite_anesth"]    = "dateTime";
    $specs["prat_visite_anesth_id"] = "ref class|CMediusers";
    $specs["rques_visite_anesth"]   = "text helped show|0";
    $specs["autorisation_anesth"]   = "bool default|0";

    $specs["facture"]                 = "bool default|0";
    
    
    $specs["_duree_interv"]           = "time";
    $specs["_duree_garrot"]           = "time";
    $specs["_duree_induction"]        = "time";
    $specs["_presence_salle"]         = "time";
    $specs["_duree_sspi"]             = "time";

    $specs["_date_min"]               = "date";
    $specs["_date_max"]               = "date moreEquals|_date_min";
    $specs["_plage"]                  = "bool";
    $specs["_intervention"]           = "text";
    $specs["_prat_id"]                = "text";
    $specs["_patient_id"]             = "ref class|CPatient show|1";
    $specs["_bloc_id"]                = "ref class|CBlocOperatoire";
    $specs["_specialite"]             = "text";
    $specs["_ccam_libelle"]           = "bool default|1";
    $specs["_hour_op"]                = "";
    $specs["_min_op"]                 = "";
    $specs["_datetime"]               = "dateTime show";
    $specs["_pause_min"]              = "numchar length|2";
    $specs["_pause_hour"]             = "numchar length|2";
    $specs["_move"]                   = "str";
    $specs["_password_visite_anesth"] = "password notNull";
    
    return $specs;
  }
  
  function loadRelPatient(){
    return $this->loadRefPatient();
  }
  
  function getExecutantId($code_activite) {
    $this->loadRefChir();
    $this->loadRefPlageOp();
    return ($code_activite == 4 ? $this->_ref_anesth->user_id : $this->chir_id);
  }
  
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
    $backProps["echanges_ihe"]             = "CExchangeIHE object_id";
    $backProps["product_orders"]           = "CProductOrder object_id";
    $backProps["op_brancardardage"]        = "CBrancardage operation_id";
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
  
  function check() {
    $msg = null;
    $this->completeField("chir_id", "plageop_id", "sejour_id");
    if(!$this->_id && !$this->chir_id) {
      $msg .= "Praticien non valide ";
    }

    // Bornes du s�jour
    $sejour = $this->loadRefSejour();
    $this->loadRefPlageOp();
    
    if ($this->plageop_id !== null && !$sejour->entree_reelle) {
      $date = mbDate($this->_datetime);
      $entree = mbDate($sejour->entree_prevue);
      $sortie = mbDate($sejour->sortie_prevue);
      if (!CMbRange::in($date, $entree, $sortie)) {
         $msg .= "Intervention du $date en dehors du s�jour du $entree au $sortie";
      }
    }
    
    // V�rification de la signature de l'anesth�siste pour la
    // visite de pr�-anesth�sie
    $user = CAppUI::$user;
    if ($this->fieldModified("prat_visite_anesth_id") && $this->prat_visite_anesth_id !== null && $this->prat_visite_anesth_id != $user->_id) {
      $anesth = new CUser();
      $anesth->load($this->prat_visite_anesth_id);
      if($anesth->user_password != md5($this->_password_visite_anesth)) {
        $msg .= "Mot de passe incorrect";
      }
    }
    return $msg . parent::check();
  }
  
  function delete() {
    $msg = parent::delete();
    $this->loadRefPlageOp();
    $this->_ref_plageop->reorderOp();
    return $msg;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
    $this->_hour_urgence = intval(substr($this->time_operation, 0, 2));
    $this->_min_urgence  = intval(substr($this->time_operation, 3, 2));
    
    if($this->horaire_voulu){
      $this->_hour_voulu = intval(substr($this->horaire_voulu, 0, 2));
      $this->_min_voulu  = intval(substr($this->horaire_voulu, 3, 2)); 
    }
    
    if($this->pause){
      $this->_pause_hour = intval(substr($this->pause, 0, 2));
      $this->_pause_min  = intval(substr($this->pause, 3, 2)); 
    }
      
    $this->_ref_type_anesth = $this->loadFwdRef("type_anesth", true);
    $this->_lu_type_anesth = $this->_ref_type_anesth->name;
    
    if($this->debut_op && $this->fin_op && $this->fin_op > $this->debut_op){
      $this->_duree_interv = mbSubTime($this->debut_op,$this->fin_op);
    }
    if($this->pose_garrot && $this->retrait_garrot && $this->retrait_garrot > $this->pose_garrot){
      $this->_duree_garrot = mbSubTime($this->pose_garrot,$this->retrait_garrot);
    }
    if($this->induction_debut && $this->induction_fin && $this->induction_fin > $this->induction_debut){
      $this->_duree_induction = mbSubTime($this->induction_debut,$this->induction_fin);
    }
    if($this->entree_salle && $this->sortie_salle && $this->sortie_salle>$this->entree_salle){
      $this->_presence_salle = mbSubTime($this->entree_salle,$this->sortie_salle);
    }
    if($this->entree_reveil && $this->sortie_reveil && $this->sortie_reveil > $this->entree_reveil){
      $this->_duree_sspi = mbSubTime($this->entree_reveil,$this->sortie_reveil);
    }
    if($this->plageop_id) {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_planning&operation_id=".$this->_id;
    } else {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_urgence&operation_id=".$this->_id;
    }
    $this->_acte_depassement        = $this->depassement;
    $this->_acte_depassement_anesth = $this->depassement_anesth;   
  }
  
  function updatePlainFields() {
    if (is_array($this->_codes_ccam) && count($this->_codes_ccam)) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }
    
    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      // @TODO: change it to use removeValue
      while($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation = sprintf("%02d:%02d:00", $this->_hour_op, $this->_min_op);
    }
    if($this->_hour_urgence !== null and $this->_min_urgence !== null) {
      $this->time_operation = sprintf("%02d:%02d:00", $this->_hour_urgence, $this->_min_urgence);
    }
    if($this->_hour_voulu != null and $this->_min_voulu != null) {
      $this->horaire_voulu = sprintf("%02d:%02d:00", $this->_hour_voulu, $this->_min_voulu);
    }
    if($this->_pause_hour !== null and $this->_pause_min !== null) {
      $this->pause = sprintf("%02d:%02d:00", $this->_pause_hour, $this->_pause_min);
    }
    
    $this->completeField('rank');
    $this->completeField('plageop_id');
    if($this->_move) {
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
      $this->_move = null;
    }
  }

  function store($reorder = true) {
    
    $this->completeField("annulee", "rank", "codes_ccam",
                         "plageop_id", "chir_id", "materiel", "commande_mat");
    $this->loadOldObject();
    $oldIntervention = $this->_old;
                         
    // Cr�ation d'un alerte sur l'intervention
    $msgAlerte = "";
    $isAlerte = false;
    if($this->_old->rank || ($this->materiel && $this->commande_mat)) {
      $this->loadRefPlageOp();
      $this->_old->loadRefPlageOp();
      if($this->fieldModified("annulee", "1")) {
        // Alerte sur l'annulation d'une intervention
        $msgAlerte .= "L'intervention a �t� annul�e pour le ".mbTransformTime(null, $this->_datetime, CAppUI::conf("datetime")).".";
        $isAlerte = true;
      } elseif(mbDate(null, $this->_datetime) != mbDate(null, $this->_old->_datetime)) {
        // Alerte sur le d�placement d'une intervention
        $msgAlerte .= "L'intervention a �t� d�plac�e du ".mbTransformTime(null, $this->_old->_datetime, CAppUI::conf("date"))." au ".mbTransformTime(null, $this->_datetime, CAppUI::conf("date")).".";
        $isAlerte = true;
      } elseif($this->fieldModified("materiel") && $this->commande_mat) {
        $msgAlerte .= "Le materiel a �t� modifi� \n - Ancienne valeur : ".$this->_old->materiel." \n - Nouvelle valeur : ".$this->materiel;
        $isAlerte = true;
      }
      if($this->_old->rank) {
        $msgAlerte .= "\nL'intervention avait �t� valid�e.";
      }
      if($this->materiel && $this->commande_mat) {
        $msgAlerte .= "\nLe materiel avait �t� command�.";
      }
    }
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if($isAlerte) {
      $alerte = new CAlert();
      $alerte->setObject($this);
      $alerte->comments = $msgAlerte;
      $alerte->tag = "mouvement_intervention";
      $alerte->handled = "0";
      $alerte->level = "medium";
      $alerte->store();
    }
    
    // Cas d'une annulation
    if (!$this->annulee) {
      // Si pas une annulation on recup�re le sejour
      // et on regarde s'il n'est pas annul�
      $this->loadRefSejour();
      if($this->_ref_sejour->annule) {
        $this->_ref_sejour->annule = 0;
        $this->_ref_sejour->store();
      }

      // Application des protocoles de prescription en fonction de l'operation->_id
      if ($this->_protocole_prescription_chir_id || $this->_protocole_prescription_anesth_id) {
        $this->_ref_sejour->_protocole_prescription_chir_id = $this->_protocole_prescription_chir_id;
        $this->_ref_sejour->_protocole_prescription_anesth_id = $this->_protocole_prescription_anesth_id;
        $this->_ref_sejour->applyProtocolesPrescription($this->_id);
        
        // On les nullify pour eviter de les appliquer 2 fois
        $this->_protocole_prescription_anesth_id = null;
        $this->_protocole_prescription_chir_id = null;
        $this->_ref_sejour->_protocole_prescription_chir_id = null;
        $this->_ref_sejour->_protocole_prescription_anesth_id = null;
      }
    } elseif($this->rank != 0) {
      $this->rank = 0;
      $this->time_operation = "00:00:00";
    }
    
    // V�rification qu'on a pas des actes CCAM cod�s obsol�tes
    if($this->codes_ccam) {
      $this->loadRefsActesCCAM();
      foreach($this->_ref_actes_ccam as $keyActe => $acte) {
        if(strpos(strtoupper($this->codes_ccam), strtoupper($acte->code_acte)) === false) {
          $this->_ref_actes_ccam[$keyActe]->delete();
        }
      }
    }
    
    // Cas de la cr�ation dans une plage de sp�cialit�
    if($this->plageop_id) {
      $this->loadRefPlageOp();
      if($this->_ref_plageop->spec_id && $this->_ref_plageop->unique_chir) {
        $this->_ref_plageop->chir_id = $this->chir_id;
        $this->_ref_plageop->spec_id = "";
        $this->_ref_plageop->store();
      }
    }
    if ($msg = parent::store()) {
      return $msg;
    }
    if($reorder) {
      if($this->plageop_id && $oldIntervention->plageop_id && $oldIntervention->rank && ($this->plageop_id != $oldIntervention->plageop_id)) {
        $oldIntervention->loadRefPlageOp();
        $oldIntervention->_ref_plageop->reorderOp();
      }
      $this->_ref_plageop->reorderOp();
    }
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    // Filtre sur l'�tablissement
    $g = CGroups::loadCurrent();
    $where["sejour.group_id"] = "= '$g->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
  
  function loadView() {
    parent::loadView();
    $this->loadRefPatient();
  }
  
  function loadComplete() {
    parent::loadComplete();
    $this->loadRefPatient();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  function loadRefChir($cache = 0) {
    $this->_ref_chir = $this->loadFwdRef("chir_id", $cache);
    $this->_praticien_id = $this->_ref_chir->_id;
    return $this->_ref_chir;
  }
  
  function loadRefPraticien($cache = 0) {
    $this->loadRefChir($cache);
    return $this->_ref_praticien =& $this->_ref_chir;
  }
  
  function getActeExecution() {
    $this->loadRefPlageOp();
  }
  
  function loadRefAffectation() {
    $this->loadRefPlageOp();
    $where = array();
    $where["sejour_id"] = "= $this->sejour_id";
    $where["entree"] = " <= '$this->_datetime'";
    $where["sortie"] = " >= '$this->_datetime'";
    $this->_ref_affectation = new CAffectation();
    $this->_ref_affectation->loadObject($where);
    $this->_ref_affectation->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  
  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }
  
  /**
   * Met � jour les information sur la salle 
   * N�cessiste d'avoir charg� la plage op�ratoire au pr�alable
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
      $salle = new CSalle;
      return $this->_ref_salle = $salle->getCached($this->salle_id);
    }
  }
  
  function loadRefPlageOp($cache = 0) {
    $this->_ref_anesth        = $this->loadFwdRef("anesth_id"            , $cache);
    $this->_ref_anesth_visite = $this->loadFwdRef("prat_visite_anesth_id", $cache);
    
    $this->_ref_plageop = $this->loadFwdRef("plageop_id", $cache);
    // Avec plage d'op�ration
    if ($this->_ref_plageop->_id) {
      $this->_ref_plageop->loadRefsFwd($cache);
      
      if (!$this->anesth_id) {
        $this->_ref_anesth =& $this->_ref_plageop->_ref_anesth;
      }
      
      $date = $this->_ref_plageop->date;
    }
    // Hors plage
    else {
      $date = $this->date;
    }    
    
    $this->updateSalle();
    
    //Calcul du nombre de jour entre la date actuelle et le jour de l'operation
    $this->_compteur_jour = mbDaysRelative($date, mbDate());
    
    // Horaire global
    if($this->time_operation && $this->time_operation != "00:00:00") {
      $this->_datetime = "$date $this->time_operation";
    } elseif($this->horaire_voulu && $this->horaire_voulu != "00:00:00") {
      $this->_datetime = "$date $this->horaire_voulu"; 
    } elseif($this->_ref_plageop->_id) {
      $this->_datetime = "$date ".$this->_ref_plageop->debut;
    } else {
      $this->_datetime = "$date 00:00:00";
    }
    $this->_datetime_reel     = "$date $this->debut_op";
    $this->_datetime_reel_fin = "$date $this->fin_op";
    
    // Heure standard d'ex�cution des actes
    if ($this->fin_op) {
      $this->_acte_execution = $this->_datetime_reel_fin;
    } 
    elseif ($this->debut_op) {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime_reel);
    } 
    elseif ($this->time_operation != "00:00:00") {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime);
    } 
    else {
      $this->_acte_execution = mbDateTime();
    }

    $this->_view = "Intervention du ";
    $this->_view .= mbTransformTime(null, $this->_datetime, CAppUI::conf("date"));
    return $this->_ref_plageop;
  }
  
  function preparePossibleActes() {
    $this->loadRefPlageOp();
  }
  
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return;
    }
    
    $order = "consultation_anesth_id ASC";
    $this->_ref_consult_anesth = new CConsultAnesth();
    $this->_ref_consult_anesth->operation_id = $this->_id;
    $this->_ref_consult_anesth->loadMatchingObject($order);
    
    return $this->_ref_consult_anesth;
  }
  
  function loadRefSejour($cache = false) {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
  }
  
   /*
   * Chargement des gestes perop
   */
  function loadRefsAnesthPerops(){
    $this->_ref_anesth_perops = $this->loadBackRefs("anesth_perops", "datetime");
  }
  
  
  function loadRefPatient($cache = false) {
    $this->loadRefSejour($cache);
    $this->_ref_sejour->loadRefPatient($cache);
    $this->_ref_patient =& $this->_ref_sejour->_ref_patient;
    $this->_patient_id = $this->_ref_patient->_id;
    $this->loadFwdRef("_patient_id", $cache);
    return $this->_ref_patient;
  }
  
  function loadRefsFwd($cache = false) {
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();
    $this->_ref_consult_anesth->_ref_consultation->countDocItems();
    $this->_ref_consult_anesth->_ref_consultation->canRead();
    $this->_ref_consult_anesth->_ref_consultation->canEdit();
    $this->loadRefPlageOp($cache);
    $this->loadExtCodesCCAM();
    
    $this->loadRefChir($cache);
    $this->loadRefPatient($cache);
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr {$this->_ref_chir->_view}";
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActes();
    $this->loadRefsDocs();
  }
  
  function loadEchangeHprim() {
    $order = "date_production DESC";
    // R�cup�ration de tous les �changes produits
    $this->_ref_echange_hprim = $this->loadBackRefs("echanges_hprim", $order);
  }
  
  function isCoded() {
    $this->loadRefPlageOp();
    $this->_coded = (CAppUI::conf("dPsalleOp COperation modif_actes") == "never") ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "oneday" && $this->date > mbDate()) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "button" && $this->_ref_plageop->actes_locked) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "facturation" && $this->facture);
    return $this->_coded;
  }
  
  function getPerm($permType) {
    switch($permType) {
      case PERM_EDIT :
        if(!$this->_ref_chir){
          $this->loadRefChir();
        }if(!$this->_ref_anesth){
          $this->loadRefPlageOp();
        }
        if($this->plageop_id) {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm($permType));
        } else {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm(PERM_READ));
        }
        break;
      default :
        return parent::getPerm($permType);
    }
    
    //if(!$this->_ref_chir){
    //  $this->loadRefChir();
    //}if(!$this->_ref_anesth){
    //  $this->loadRefPlageOp();
    //}
    //return ($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType));
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_sejour->loadRefsFwd();
    
    // Chargement du fillTemplate du praticien
    $this->_ref_chir->fillTemplate($template);

    // Chargement du fillTemplate du sejour
    $this->_ref_sejour->fillTemplate($template);
    
    // Chargement du fillTemplate de l'op�ration 
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd(1);

    $this->loadRefPraticien();
    $template->addProperty("Op�ration - Chirurgien"           , $this->_ref_praticien->_id ? ("Dr ".$this->_ref_praticien->_view) : '');
    $template->addProperty("Op�ration - Anesth�siste - nom"   , @$this->_ref_anesth->_user_last_name);
    $template->addProperty("Op�ration - Anesth�siste - pr�nom", @$this->_ref_anesth->_user_first_name);
    $template->addProperty("Op�ration - Anesth�sie"           , $this->_lu_type_anesth);
    $template->addProperty("Op�ration - libell�"              , $this->libelle);
    $template->addProperty("Op�ration - CCAM1 - code"         , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Op�ration - CCAM1 - description"  , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Op�ration - CCAM2 - code"         , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Op�ration - CCAM2 - description"  , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Op�ration - CCAM3 - code"         , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Op�ration - CCAM3 - description"  , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Op�ration - CCAM - codes"         , implode(" - ", $this->_codes_ccam));
    $template->addProperty("Op�ration - CCAM - descriptions"  , implode(" - ", CMbArray::pluck($this->_ext_codes_ccam, "libelleLong")));
    $template->addProperty("Op�ration - salle"                , @$this->_ref_salle->nom);
    $template->addProperty("Op�ration - c�t�"                 , $this->cote);
    
    $template->addDateProperty("Op�ration - date"             , $this->_datetime);
    $template->addTimeProperty("Op�ration - heure"            , $this->time_operation);
    $template->addTimeProperty("Op�ration - dur�e"            , $this->temp_operation);    
    $template->addTimeProperty("Op�ration - dur�e r�elle"     , $this->_duree_interv);
    $template->addTimeProperty("Op�ration - entr�e bloc"      , $this->entree_salle);
    $template->addTimeProperty("Op�ration - pose garrot"      , $this->pose_garrot);
    $template->addTimeProperty("Op�ration - d�but op"         , $this->debut_op);
    $template->addTimeProperty("Op�ration - fin op"           , $this->fin_op);
    $template->addTimeProperty("Op�ration - retrait garrot"   , $this->retrait_garrot);
    $template->addTimeProperty("Op�ration - sortie bloc"      , $this->sortie_salle);
    
    $template->addProperty("Op�ration - depassement"          , $this->depassement);
    $template->addProperty("Op�ration - exams pre-op"         , $this->examen);
    $template->addProperty("Op�ration - mat�riel"             , $this->materiel);
    $template->addProperty("Op�ration - convalescence"        , $this->_ref_sejour->convalescence);
    $template->addProperty("Op�ration - remarques"            , $this->rques);
    
    $this->loadAffectationsPersonnel();
    foreach ($this->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Op�ration - personnel r�el - $locale", $property);
    }
    
    $plageop = $this->_ref_plageop;
    $plageop->loadAffectationsPersonnel();
    foreach ($plageop->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Op�ration - personnel pr�vu - $locale", $property);
    }
  }
  
  function docsEditable() {
    if (parent::docsEditable()) {
      return true;
    }
    
    $fix_edit_doc = CAppUI::conf("dPplanningOp CSejour fix_doc_edit");
    $this->loadRefSejour();
    return !$fix_edit_doc ? true : $this->_ref_sejour->sortie_reelle === null;
  }
}

?>