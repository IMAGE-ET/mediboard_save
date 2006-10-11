<?php

global $AppUI;
require_once $AppUI->getModuleClass("dPsante400", "recordsante400");

class CMouvSejourTonkin extends CRecordSante400 {
  static $table = "TRSJ0";
  static $complete = ">EFCPSN";
  static $verbose = false;
  static $prodret = "ETAT";
  
  public $status = null;
  public $rec = null;
  
  public $sejour = null;
  public $etablissement = null;
  public $fonction = null;
  public $patient = null;
  public $praticien = null;
  public $naissance = null;
  
  function __construct() {
  }

  function getMarkedQuery($marked) {
    $complete = self::$complete;
    $prodret = self::$prodret;
    return $marked ? 
      "\n WHERE $prodret != '$complete'" : 
      "\n WHERE $prodret IS NULL";
  }

  function multipleLoad($marked = false, $max = 100) {
    $table = self::$table;
    $query = "SELECT * FROM $table";
    $query .= self::getMarkedQuery($marked);
    
    return CRecordSante400::multipleLoad($query, $max, "CMouvSejourTonkin");
  }

  function count($marked = false) {
    $table = self::$table;
    $req = new CRecordSante400();
    $query = "SELECT COUNT(*) AS COUNT FROM $table";
    $query .= self::getMarkedQuery($marked);
    
    $req->query($query);
    return ($req->consume("COUNT"));
  }
  
  function load($rec = null) {
    $base  = self::$base;
    $table = self::$table;
    $query = "SELECT * FROM $base.$table";

    if ($rec !== null) {
      $rec = intval($rec);
      $query .= "\n WHERE IDUENR = $rec";
    }
    
    $this->query($query);
  }
    
  function markRow() {
    $base  = self::$base;
    $table = self::$table;
    $prodret = self::$prodret;
    
    if ($this->status == self::$complete) {
//      $query = "DELETE FROM $table WHERE IDUENR = $this->rec";
      $query = "UPDATE $table SET $prodret = '$this->status' WHERE IDUENR = $this->rec";
    } else {
      $query = "UPDATE $table SET $prodret = '$this->status' WHERE IDUENR = $this->rec";
    }
    
    $rec = new CRecordSante400;
    $rec->query($query);
  }
  
  function markStatus($letter) {
    $this->status .= $letter;
  }

  function trace($value, $title) {
    if (self::$verbose) {
      mbTrace($value, $title);
    }
  }
  
  function proceed() {
    $this->status = ">";
    $this->trace($this->data, "Donn�es � traiter dans le mouvement");
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
    $this->rec = $this->consume("IDUENR");
    
  }
}
?>
