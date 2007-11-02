<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvement400 extends CRecordSante400 {
  protected $base = null;
  protected $table = null;
  
  protected $prodField = null;
  protected $idField = null;
  protected $typeField = null;
  protected $groupField = null;
  
  public $statuses = array(null, null, null, null, null, null, null, null);
  public $cached   = array(null, null, null, null, null, null, null, null);
  
  public $status = null;
  public $rec = null;
  public $type = null;
  public $prod = null;
  public $when = null;
  public $changedFields = array();

  function initialize() {
    // Consume metadata
    $this->when = $this->consumeDateTimeFlat("TRDATE", "TRHEURE");
    $this->rec = $this->consume($this->idField);
    $this->prod = $this->consume($this->prodField);
    $this->type = $this->consume($this->typeField);
    
    // Analyse changed fields
    foreach (array_keys($this->data) as $beforeName) {
      $matches = array();
    	if (!preg_match("/B_(\w*)/i", $beforeName, $matches)) {
    	  continue;
    	}
    	
    	$name = $matches[1];
    	$afterName = "A_$name";
    	if ($this->data[$beforeName] != $this->data[$afterName]) {
    	  $this->changedFields[] = $name;
    	}
    }
    
    // Initialize prefix
    $this->valuePrefix = $this->type == "S" ? "B_": "A_";
  }
  
  function multipleLoad($marked = false, $max = 100) {
    global $dPconfig;
    $idConfig = $dPconfig["dPsante400"];

    $query = "SELECT * FROM $this->base.$this->table";
    $query.= $this->getMarkedClause($marked);
    $query.= $this->getFilterClause();
 
    $mouvs = CRecordSante400::multipleLoad($query, array(), $max, get_class($this));

    // Multiple checkout
    $recs = array();
    foreach ($mouvs as &$mouv) {
      $mouv->initialize();
      $recs[] = "'$mouv->rec'";
    }
    
    if (count($mouvs)) {
      if ($idConfig["mark_row"]) {
        $recs = join($recs, ",");
        
        $query = "UPDATE $mouv->base.$mouv->table " .
            "\n SET $mouv->prodField = '========' " .
            "\n WHERE $mouv->idField IN ($recs)";
        
        $rec = new CRecordSante400;
        $rec->query($query);
      }
    }
    
    return $mouvs;
  }
  
  function getFilterClause() {
    if (!$this->groupField) {
      return;
    }

    global $dPconfig;
    $idConfig = $dPconfig["dPsante400"];
    
    $group_id = $idConfig["group_id"];
    $beforeField = "B_$this->groupField";
    $afterField  = "A_$this->groupField";

    return  $group_id ? 
      "\n AND ($beforeField = '$group_id' OR $afterField = '$group_id')" : 
      "";
  }

  function getMarkedClause($marked) {
    if (!$this->prodField) {
      return;
    }
    
    return $marked ? 
      "\n WHERE $this->prodField NOT IN ('', '========')" : 
      "\n WHERE $this->prodField = ''";
  }

  function count($marked = false) {
    $req = new CRecordSante400();
    $query = "SELECT COUNT(*) AS TOTAL FROM $this->base.$this->table";
    $query.= $this->getMarkedClause($marked);
    $query.= $this->getFilterClause();
    $req->query($query);

    return $req->consume("TOTAL");
  }
  
  /**
   * Purge all mouvements
   * @return int the number of deleted mouvements
   */
  function purge($marked = false) {
    $req = new CRecordSante400();
    $query = "DELETE  FROM $this->base.$this->table";
    $query.= $this->getMarkedClause($marked);
    $query.= $this->getFilterClause();
    return $req->query($query);

//    return $req->consume("TOTAL");
  }
  
  function load($rec) {
    global $dPconfig;
    $idConfig = $dPconfig["dPsante400"];

    $query = "SELECT * FROM $this->base.$this->table" .
        "\n WHERE $this->idField = ?";

    $values = array (
      intval($rec),
    );    

    $this->loadOne($query, $values);
    $this->initialize();

    // Checkout
    if ($idConfig["mark_row"]) {
      $query = "UPDATE $this->base.$this->table " .
          "\n SET $this->prodField = '========' " .
          "\n WHERE $this->idField = ?";
      $values = array($this->rec);
      
      $rec = new CRecordSante400;
      $rec->query($query, $values);
    }
  }
    
  function markRow() {
    global $dPconfig;
    if (!$dPconfig["dPsante400"]["mark_row"]) {
      return;
    }
    
    if (!$this->prodField) {
      return null;
    }
    
    $this->status = "";
    foreach ($this->statuses as $status) {
      $this->status .= null !== $status ? chr($status + ord("0")) : "-";    
    }

    $query = 
      !in_array(null, $this->statuses, true) ?
      "DELETE FROM $this->base.$this->table WHERE $this->idField = ?" :
      "UPDATE $this->base.$this->table SET $this->prodField = '$this->status' WHERE $this->idField = ?";
    $values = array (
      $this->rec,
    );
    
    $rec = new CRecordSante400;
    $rec->query($query, $values);
  }
  
  /**
   * Initialisation du status � z�ro si non existant
   * @param int $rank
   * @param int $value valeur � mettre, incr�mente le rang si null 
   */
  function setStatus($rank) {
    if (null === @$this->statuses[$rank]) {
      $this->statuses[$rank] = 0;
    }
  }
  
  /**
   * Changement de status sur un rang � une valeur donn�e
   * @param int $rank
   * @param int $value valeur � mettre, incr�mente le rang si null 
   */
  function markStatus($rank, $value = null) {
    $this->statuses[$rank] = null === $value ? @$this->statuses[$rank] + 1 : $value;
  }

  function markCache($rank, $value = null) {
    $this->markStatus($rank, $value);
    $this->cached[$rank] = null === $value ? @$this->cached[$rank] + 1 : $value;
  }

  /**
   * Trace value with given title
   * @param mixed $value
   * @param string title
   */
  function trace($value, $title) {
    if (self::$verbose) {
      mbTrace($value, $title);
    }
  }
  
  function proceed() {
    $this->trace($this->data, "Donn�es � traiter dans le mouvement");
    $this->trace(join(" ", $this->changedFields), "Donn�es modifi�es");
    
    try {
      $this->synchronize();
      $return = true;
    } catch (Exception $e) {
      if (self::$verbose) {
        trigger_error($e->getMessage(), E_USER_WARNING);
      }
      $return = false;
    }
    
    $this->markRow();
    $this->trace($this->data, "Donn�es non trait�es dans le mouvement");
    return $return;
  }
  
  function synchronize() {
  }
}
?>
