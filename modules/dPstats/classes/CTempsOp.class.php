<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  var $reveil_moy       = null;
  var $reveil_ecart     = null;
  
  // Object References
  var $_ref_praticien = null;
	
	// Derived Fields
	var $_codes = null;

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
    $specs["reveil_moy"]       = "time";
    $specs["reveil_ecart"]     = "time";
    $specs["ccam"]            = "str";
    return $specs;
  }	
  
	function updateFormFields() {
		parent::updateFormFields();
		$this->_codes = explode("|", strtoupper($this->ccam));
	}
	
  function loadRefsFwd() { 
    $this->_ref_praticien = $this->loadFwdRef("chir_id", 1);
    $this->_ref_praticien->loadRefFunction();
  }
  
	/**
	 * Dur�e moyenne d'intervention
	 * @param object $chir_id [optional]
	 * @param object $ccam [optional]
	 * @return int Dur�e en minutes, 0 si aucune intervention, false si temps non calcul�
	 */
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
    if (null == $liste = $temp->loadList($where)) {
    	return;
    }
		
    foreach($liste as $temps) {
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