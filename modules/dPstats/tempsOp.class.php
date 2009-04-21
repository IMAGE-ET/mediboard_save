<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPStats
 *	@version $Revision$
 *  @author S�bastien Fillonneau
 */

/**
 * The CTempsOp class
 */
class CTempsOp extends CMbObject {
  // DB Table key
  var $temps_op_id = null;
  
  // DB Fields
  var $chir_id         = null;
  var $ccam            = null;
  var $nb_intervention = null;
  var $estimation      = null;
  var $occup_moy       = null;
  var $occup_ecart     = null;
  var $duree_moy       = null;
  var $duree_ecart     = null;
  
  // Object References
  var $_ref_praticien = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'temps_op';
    $spec->key   = 'temps_op_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]         = "ref class|CMediusers";
    $specs["nb_intervention"] = "num pos";
    $specs["estimation"]      = "time";
    $specs["occup_moy"]       = "time";
    $specs["occup_ecart"]     = "time";
    $specs["duree_moy"]       = "time";
    $specs["duree_ecart"]     = "time";
    $specs["ccam"]            = "str";
    return $specs;
  }	
  
  
  function loadRefsFwd(){
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->chir_id);
  }
  
  static function getTime($chir_id = 0, $ccam = null){
    $time = 0;
    $where = array();
    $total = array();
    $total["occup_somme"] = 0;
    $total["nbInterventions"] = 0;
    $where["chir_id"] = "= '$chir_id'";
    
    if(is_array($ccam)){
      foreach($ccam as $keyccam => $code){
        $where[] = "ccam LIKE '%".strtoupper($code)."%'";
      }
    }elseif($ccam){
      $where["ccam"] = "LIKE '%".strtoupper($ccam)."%'";
    }
    
    $temp = new CTempsOp;
    $liste = $temp->loadList($where);
    foreach($liste as $keyTemps => $temps) {
      $total["nbInterventions"] += $temps->nb_intervention;
      $total["occup_somme"] += $temps->nb_intervention * strtotime($temps->occup_moy);
    }
    if($total["nbInterventions"]) {
      $time = $total["occup_somme"] / $total["nbInterventions"];
    } else {
      $time = 0;
    }
    return $time;
  }
}
?>