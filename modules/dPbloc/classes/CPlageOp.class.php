<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlageOp extends CMbObject {
  static $minutes = array();
  static $hours = array();
  static $hours_start = null;  
  static $hours_stop = null;
  static $minutes_interval = null;
  
  // DB Table key
  var $plageop_id = null;
  
  // DB References
  var $chir_id      = null;
  var $anesth_id    = null;
  var $spec_id      = null;
  var $salle_id     = null;
  var $spec_repl_id = null;

  // DB fields
  var $date             = null;
  var $debut            = null;
  var $fin              = null;
  var $unique_chir      = null;
  var $temps_inter_op   = null;
  var $max_intervention = null;
  var $verrouillage     = null;
  var $delay_repl       = null;
  var $actes_locked     = null;
    
  // Form Fields
  var $_day          = null;
  var $_month        = null;
  var $_year         = null;
  var $_duree_prevue = null;
  var $_type_repeat  = null;
  
  // Behaviour Fields
  var $_verrouillee = null;
  
  // Object References
  var $_ref_chir       = null;
  var $_ref_anesth     = null;
  var $_ref_spec       = null;
  var $_ref_spec_repl  = null;
  var $_ref_salle      = null;
  var $_ref_operations = null;
  var $_nb_operations  = null;
  var $_nb_operations_placees  = null;
  var $_fill_rate      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plagesop';
    $spec->key   = 'plageop_id';
    $spec->xor["owner"] = array("spec_id", "chir_id");
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation plageop_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]          = "ref class|CMediusers";
    $specs["anesth_id"]        = "ref class|CMediusers";
    $specs["spec_id"]          = "ref class|CFunctions";
    $specs["salle_id"]         = "ref notNull class|CSalle";
    $specs["spec_repl_id"]     = "ref class|CFunctions";
    $specs["date"]             = "date notNull";
    $specs["debut"]            = "time notNull";
    $specs["fin"]              = "time notNull moreThan|debut";
    $specs["unique_chir"]      = "bool default|1";
    $specs["temps_inter_op"]   = "time";
    $specs["max_intervention"] = "num min|0";
    $specs["verrouillage"]     = "enum list|defaut|non|oui default|defaut";
    $specs["delay_repl"]       = "num min|0";
    $specs["actes_locked"]     = "bool";
    
    $specs["_type_repeat"]     = "enum list|simple|double|triple|quadruple|sameweek";
    return $specs;
  }
  
  function loadRefs($annulee = 1) {
    $this->loadRefsFwd();
    $this->loadRefsBack($annulee);
  }
  
  function loadRefChir($cache = 1) {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", $cache);
  }
  
  function loadRefAnesth($cache = 1) {
    return $this->_ref_anesth = $this->loadFwdRef("anesth_id", $cache);
  }
    
  function loadRefSpec($cache = 1) {
    return $this->_ref_spec = $this->loadFwdRef("spec_id", $cache);
  }
    
  function loadRefSpecRepl($cache = 1) {
    return $this->_ref_spec_repl = $this->loadFwdRef("spec_repl_id", $cache);
  }
  
  function loadRefSalle($cache = 1) {
    return $this->_ref_salle = $this->loadFwdRef("salle_id", $cache);
  }
  
  function makeView(){
    if($this->chir_id){
      $this->_view = $this->_ref_chir->_view;
    } elseif($this->spec_id){
      $this->_view = $this->_ref_spec->_shortview;
    }
    if($this->anesth_id){
      $this->_view .= " - ".$this->_ref_anesth->_shortview;
    }	
  }

  function loadRefsFwd($cache = 0) {
    $this->loadRefChir($cache);
    $this->loadRefAnesth($cache);
    $this->loadRefSpec($cache);
    $this->loadRefSalle($cache);
    $this->makeView();
  }
  
  function loadRefsOperations($annulee = 1, $order = "rank, horaire_voulu") {
    $where = array();
    $where["plageop_id"] = "= '$this->plageop_id'";
    if(!$annulee) {
      $where["annulee"] = "= '0'";
    }
    $op = new COperation;
    $this->_ref_operations = $op->loadList($where, $order);
    foreach ($this->_ref_operations as &$operation) {
      $operation->_ref_plageop =& $this;
    }
    return $this->_ref_operations;
  }

  function loadRefsBack($annulee = 1, $order = "rank, horaire_voulu") {
  	$this->loadRefsOperations($annulee, $order);
  }
  
	/** Mise � jour des horaires en fonction de l'ordre des operations, 
	 *  et mise a jour des rank, de sorte qu'ils soient consecutifs
	 */
  function reorderOp() {
    $this->completeField("debut", "temps_inter_op");
    if(!count($this->_ref_operations)) {
      $this->loadRefsBack(0);
    }
    $new_time = $this->debut;
    $i = 0;
    foreach ($this->_ref_operations as &$op) {
      if($op->rank) {
        $op->rank = ++$i;
        $op->time_operation = $new_time;
        // Pour faire suivre un changement de salle
        if($this->salle_id && $this->fieldModified("salle_id")) {
          $op->salle_id = $this->salle_id;
        }
        $op->updateFormFields();
        $op->store(false);
        // Dur�e de l'operation
        // + dur�e entre les operations
        // + dur�e de pause
        $new_time = mbAddTime($op->temp_operation, $new_time);
        $new_time = mbAddTime($this->temps_inter_op, $new_time);
        $new_time = mbAddTime($op->pause, $new_time);
      }
    }
  }
  

/**
 * returns collision message, null for no collision
 * @return str
 */
  function hasCollisions() {
    $this->completeField("salle_id");
    $this->completeField("date");
    
    // Get all other plages the same day
    $where = array();
    $where["salle_id"]   = "= '$this->salle_id'";
    $where["date"]       = "= '$this->date'";
    $where["plageop_id"] = "!= '$this->plageop_id'";
    $plages = $this->loadList($where);
    $msg = null;
    foreach ($plages as $key => $plage) {
      if (($plage->debut < $this->fin and $plage->fin > $this->fin)
        or($plage->debut < $this->debut and $plage->fin > $this->debut)
        or($plage->debut >= $this->debut and $plage->fin <= $this->fin)) {
        $msg .= "Collision avec la plage du $plage->date, de $plage->debut � $plage->fin. ";
      }
    }
    return $msg;   
  }

  function check() {
    // Data checking
    $msg = null;
    if(!$this->plageop_id && !$this->chir_id && !$this->spec_id) {
      $msg .= "Vous devez choisir un praticien ou une sp�cialit�<br />";
    }
    return $msg . parent::check();
  }
  
  function store() {
    $this->updatePlainFields();
    if ($msg = $this->hasCollisions()) {
      return $msg;
    }
    $oldPlage = new CPlageOp;
    if($this->_id) {
      $oldPlage->load($this->_id);
      $oldPlage->loadRefsBack();
    }
    // Erreur si on est en multi-praticiens, qu'il y a des interventions et qu'on veut mettre un praticien
    if (null !== $this->chir_id && $this->_id && !$this->unique_chir) {
      if(count($oldPlage->_ref_operations) && $oldPlage->spec_id && $this->chir_id) {
        $msg = "Impossible de selectionner un praticien : ".count($oldPlage->_ref_operations)." intervention(s) d�j� pr�sentes dans une plage multi-praticiens";
        return $msg;
      }
    }
    
    // Erreur si on change de praticien alors qu'il y a d�j� des interventions
    if (null !== $this->chir_id && $this->_id) {
      if(count($oldPlage->_ref_operations) && $oldPlage->chir_id && ($oldPlage->chir_id != $this->chir_id)) {
        $msg = "Impossible de changer le praticien : ".count($oldPlage->_ref_operations)." intervention(s) d�j� pr�sentes";
        return $msg;
      }
    }
    // Modification du salle_id de la plage -> repercussion sur les interventions
    if($this->_id && $this->salle_id && $this->salle_id != $oldPlage->salle_id) {
      foreach($oldPlage->_ref_operations as &$_operation) {
        if($_operation->salle_id == $oldPlage->salle_id) {
          $_operation->salle_id = $this->salle_id;
          $_operation->store();
        }
      }
    }
    // Modification du d�but de la plage ou des minutes entre les interventions
    $this->completeField("debut","temps_inter_op");
    if($this->_id && ($this->debut != $oldPlage->debut || $this->temps_inter_op != $oldPlage->temps_inter_op)) {
      $this->reorderOp();
    }
    return parent::store();
  }

  function delete() {
    $this->completeField("salle_id", "date");
    $this->loadRefsOperations();
    $_op = new COperation();
    foreach($this->_ref_operations as $_op) {
      if($_op->annulee) {
        $_op->plageop_id = "";
        $_op->date       = $this->date;
        $_op->salle_id   = $this->salle_id;
        $_op->store();
      }
    }
    return parent::delete();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree_prevue = mbTimeRelative($this->debut, $this->fin);
    $this->_view = "Plage du ".$this->getFormattedValue("date");
  }  
  
  /**
   * find the next plageop according
   * to the current plageop parameters
   * return the number of weeks jumped
   * @return int
   */
  function becomeNext() {
    $week_jumped = 0;
    if(!$this->_type_repeat) {
      $this->_type_repeat = "simple";
    }
    switch($this->_type_repeat) {
      case "quadruple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 4
        $week_jumped++;
      case "triple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 3
        $week_jumped++;
      case "double": 
        $this->date = mbDate("+1 WEEK", $this->date); // 2
        $week_jumped++;
      case "simple": 
        $this->date = mbDate("+1 WEEK", $this->date); // 1
        $week_jumped++;
        break;
      case "sameweek":
        $week_number = CMbDate::weekNumberInMonth($this->date);
        $next_month  = CMbDate::monthNumber(mbDate("+1 MONTH", $this->date));
        $i=0;
        do {
          $this->date = mbDate("+1 WEEK", $this->date);
          $week_jumped++;
          $i++;
        } while(
          $i<10 && 
          (CMbDate::monthNumber($this->date)       <  $next_month) ||
          (CMbDate::weekNumberInMonth($this->date) != $week_number)
        );
      break;
    }
    
    // Stockage des champs modifi�s
    $debut            = $this->debut;
    $fin              = $this->fin;
    $temps_inter_op   = $this->temps_inter_op;
    $max_intervention = $this->max_intervention;
    $anesth_id        = $this->anesth_id;
    $delay_repl       = $this->delay_repl;
    $spec_repl_id     = $this->spec_repl_id;
    $type_repeat      = $this->_type_repeat;
    
    // Recherche de la plafe suivante
    $where             = array();
    $where["date"]     = "= '$this->date'";
    $where[]           = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    $where["salle_id"] = "= '$this->salle_id'";
    if($this->chir_id) {
      $where["chir_id"] = "= '$this->chir_id'";
    } else {
      $where["spec_id"] = "= '$this->spec_id'";
    }
    $plages           = $this->loadList($where);
    if(count($plages) > 0) {
      $this->load(reset($plages)->plageop_id);
    }
    else {
      $this->plageop_id = null;
    }
    if(!$this->chir_id) $this->chir_id = "";
    if(!$this->spec_id) $this->spec_id = "";
    
    // Remise en place des champs modifi�s
    $this->debut            = $debut;
    $this->fin              = $fin;
    $this->temps_inter_op   = $temps_inter_op;
    $this->max_intervention = $max_intervention;
    $this->anesth_id        = $anesth_id;
    $this->delay_repl       = $delay_repl;
    $this->spec_repl_id     = $spec_repl_id;
    $this->_type_repeat     = $type_repeat;
    $this->updateFormFields();
    return $week_jumped;
  }

  function getNbOperations($addedTime = null, $useTimeInterOp = true) {
    if($useTimeInterOp == true){
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`) + TIME_TO_SEC(`plagesop`.`temps_inter_op`)) AS time";
    }else{
      $select_time = "\nSUM(TIME_TO_SEC(`operations`.`temp_operation`)) AS time";
    }
        
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total, $select_time
        FROM `operations`, `plagesop`
        WHERE `operations`.`plageop_id` = '$this->plageop_id'
        AND `operations`.`plageop_id` = `plagesop`.`plageop_id`
        AND `operations`.`annulee` = '0'";
    $result = $this->_spec->ds->loadHash($sql);
    $this->_nb_operations = $result["total"];
    if($addedTime){
      $result["time"] = $result["time"] + $addedTime;
    }
    $this->_fill_rate = number_format($result["time"]*100/(strtotime($this->fin)-strtotime($this->debut)), 2);
        
    $sql = "SELECT COUNT(`operations`.`operation_id`) AS total, $select_time
        FROM `operations`, `plagesop`
        WHERE `operations`.`plageop_id` = '$this->plageop_id'
        AND `operations`.`plageop_id` = `plagesop`.`plageop_id`
        AND `operations`.`rank` > 0
        AND `operations`.`annulee` = '0'";
    $result = $this->_spec->ds->loadHash($sql);
    $this->_nb_operations_placees = $result["total"];
    
    if($this->verrouillage == "oui") {
      $this->_verrouillee = 1;
    } elseif($this->verrouillage == "non") {
      $this->_verrouillee = 0;
    } else {
      $this->loadRefSalle();
      $this->_ref_salle->loadRefBloc();
      $date_min = mbDate("+ " . $this->_ref_salle->_ref_bloc->days_locked . " DAYS");
      $check_datemin      = $this->date < $date_min;
      $check_fill         = ($this->_fill_rate > 100) && CAppUI::conf("dPbloc CPlageOp locked");
      $check_max          = $this->max_intervention && $this->_nb_operations >= $this->max_intervention;
      $this->_verrouillee = $check_datemin || $check_fill || $check_max;
    }
    
  }
  
  function getPerm($permType) {
    // Chargement
    if(!$this->_ref_salle){
      $this->loadRefSalle();
    }
    if($this->chir_id && !$this->_ref_chir){
      $this->loadRefChir();
    }
    if($this->spec_id && !$this->_ref_spec){
      $this->loadRefSpec();
    }

    //Test de Permission
    if($this->chir_id) {
      $pratPerm = $this->_ref_chir->getPerm($permType);
    } elseif($this->spec_id) {
      $pratPerm = $this->_ref_spec->getPerm($permType);
    }
    
    return ($this->_ref_salle->getPerm($permType) && $pratPerm);
  }
}

$pcConfig = CAppUI::conf("dPbloc CPlageOp");

CPlageOp::$hours_start = str_pad(CValue::first($pcConfig["hours_start"], "08"),2,"0",STR_PAD_LEFT);
CPlageOp::$hours_stop  = str_pad(CValue::first($pcConfig["hours_stop"], "20"),2,"0",STR_PAD_LEFT);
CPlageOp::$minutes_interval = CValue::first($pcConfig["minutes_interval"],"15");

$listHours = range($pcConfig["hours_start"], $pcConfig["hours_stop" ]);
$listMins  = range(0, 59, CPlageOp::$minutes_interval);

foreach($listHours as $key => $hour){
	CPlageOp::$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

foreach($listMins as $key => $min){
	CPlageOp::$minutes[] = str_pad($min, 2, "0", STR_PAD_LEFT);
}
	
?>