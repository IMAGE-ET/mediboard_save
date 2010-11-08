<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Technicien de SSR, association entre un plateau technique et un utilisateur
 */
class CTechnicien extends CMbObject {
  // DB Table key
  var $technicien_id = null;
  
  // References
  var $plateau_id = null;
  var $kine_id    = null;
	
	// DB Fields
	var $actif      = null;
	
	// Derived references
	var $_ref_conge_date = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'technicien';
    $spec->key   = 'technicien_id';
		return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $props["kine_id"]    = "ref notNull class|CMediusers";
    $props["actif"]      = "bool notNull default|1";
    return $props;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bilan_ssr"] = "CBilanSSR technicien_id";
    return $backProps;
  }
	
  function loadRefKine() {
    $this->_ref_kine = $this->loadFwdRef("kine_id", true);
		$this->_ref_kine->loadRefFunction();
    $this->_view = $this->_ref_kine->_view;
		return $this->_ref_kine;
  }
	
	function loadRefCongeDate($date) {
		$this->_ref_conge_date = new CPlageConge;
		$this->_ref_conge_date->loadFor($this->kine_id, $date);
		return $this->_ref_conge_date;
	}
}

?>