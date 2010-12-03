<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6194 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
class CPlageConge extends CMbObject {
	// DB Table key
  var $plage_id = null;
  
  // DB Fields
  var $date_debut  = null;
  var $date_fin    = null;
  var $libelle     = null;
  var $user_id     = null;
	var $replacer_id = null;

  // Object References
  var $_ref_user  = null;
  var $_ref_replacer = null;
	
  // Form field
  var $_duree      = null;

  function getSpec() {
		$specs = parent::getSpec();
		$specs->table = "plageconge";
		$specs->key   = "plage_id";
		return $specs;
	}

  //sp�cification des propri�t�s
  function getProps() { 
    $specs = parent::getProps();
    $specs["user_id"]     = "ref class|CMediusers notNull";
    $specs["date_debut"]  = "date notNull";
    $specs["date_fin"]    = "date moreEquals|date_debut notNull";
    $specs["libelle"]     = "str notNull";
    $specs["replacer_id"] = "ref class|CMediusers";
		$specs["_duree"]      = "num";
    return $specs;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["replacement"] = "CReplacement conge_id";
    return $backProps;
  }

	function updateFormFields() {
		parent::updateFormFields();
		$this->_shortview = $this->_view = $this->libelle;
	}
	
	function check() {
		$this->completeField("date_debut", "date_fin", "user_id");
		$plage_conge  = new CPlageConge();
		$plage_conge->user_id = $this->user_id;
		$plages_conge = $plage_conge->loadMatchingList();
		unset($plages_conge[$this->_id]);
		
		foreach($plages_conge as $_plage) {
 			if (CMbRange::collides($this->date_debut, $this->date_fin, $_plage->date_debut, $_plage->date_fin)) {
        return CAppUI::tr("CPlageConge-conflit %s", $_plage->_view);
		  }
	  }
    return parent::check();
  }
	
  function loadFor($user_id, $date) {
  	$where["user_id"] = "= '$user_id'";
		$where[] = "'$date' BETWEEN date_debut AND date_fin";
		$this->loadObject($where);
  }

  function loadListForRange($user_id, $min, $max) {
    $where["user_id"] = "= '$user_id'";
    $where["date_debut"] = "<= '$max'";
    $where["date_fin"  ] = ">= '$min'";
		$order = "date_debut";
    return $this->loadList($where, $order);
  }

	
	function loadRefsReplacementsFor($user_id, $date) {
    $where["replacer_id"] = "= '$user_id'";
    $where[] = "'$date' BETWEEN date_debut AND date_fin";
    return $this->loadList($where);
	}
	
	function loadRefReplacer(){
		$this->_ref_replacer = $this->loadUniqueBackRef("replacement");
	}
	
	function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id");
		$this->_ref_user->loadRefFunction();
	}

  function loadRefsFwd() {
  	$this->loadRefUser(); 
  }
  
	function getPerm($permType) {
    global $AppUI;
		if ($this->user_id == $AppUI->user_id) {
      return true;
    } 

		if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return $this->_ref_user->getPerm($permType);
  }
}
?>