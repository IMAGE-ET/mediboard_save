<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CLit. 
 * @abstract G�re les lits d'hospitalisation
 */
class CLit extends CMbObject {
  // DB Table key
	var $lit_id = null;	
  
  // DB References
  var $chambre_id = null;

  // DB Fields
  var $nom = null;
  
  // Form Fields
  var $_overbooking = null;

  // Object references
  var $_ref_chambre      = null;
  var $_ref_affectations = null;
  var $_ref_last_dispo   = null;
  var $_ref_next_dispo   = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'lit';
    $spec->key   = 'lit_id';
    $spec->measureable = true;
    return $spec;
  }
 
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations"]     = "CAffectation lit_id";
    $backProps["affectations_rpu"] = "CRPU box_id";
    return $backProps;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chambre_id"] = "ref notNull class|CChambre seekable";
    $specs["nom"]        = "str notNull seekable";
    return $specs;
  }
  
  function loadAffectations($date) {
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "entree" => "<= '$date 23:59:59'",
      "sortie" => ">= '$date 00:00:00'"
    );
    $order = "sortie DESC";
    
    $this->_ref_affectations = new CAffectation;
    $this->_ref_affectations = $this->_ref_affectations->loadList($where, $order);
    if(!count($this->_ref_affectations)) {
      $this->checkDispo($date);
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_short_view = $this->nom;
  }
  
  function loadCompleteView() {
    $this->loadRefsFwd();
    
    $chambre =& $this->_ref_chambre;
    $chambre->loadRefsFwd();
    
    $this->_view = "{$chambre->_ref_service->nom} $chambre->nom $this->nom";
    $this->_short_view = $this->nom;
  }
  
  function loadRefChambre() {
    $this->_ref_chambre =  $this->loadFwdRef("chambre_id", true);	
    $this->_view = "{$this->_ref_chambre->nom} - $this->nom";
    return $this->_ref_chambre;
  }

  function loadRefsFwd() {
    $this->loadRefChambre();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chambre) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_chambre->getPerm($permType));
  }
  
  function checkOverBooking() {
    assert($this->_ref_affectations !== null);
    $this->_overbooking = 0;
    $listAff = $this->_ref_affectations;
    
    foreach ($this->_ref_affectations as $aff1) {
      foreach ($listAff as $aff2) {
        if ($aff1->affectation_id != $aff2->affectation_id) {
          if ($aff1->collide($aff2)) {
            $this->_overbooking++;
          }
        }
      }
    }
    $this->_overbooking = $this->_overbooking / 2;
  }
  
  function checkDispo($date) {
    assert($this->_ref_affectations !== null);

    // Last Dispo
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "sortie" => "<= '$date 23:59:59'",
    );
    $order = "sortie DESC";
    
    $this->_ref_last_dispo = new CAffectation;
    $this->_ref_last_dispo->loadObject($where, $order);
    $this->_ref_last_dispo->checkDaysRelative($date);
    
    // Next Dispo
    $where = array (
      "lit_id" => "= '$this->lit_id'",
      "entree" => ">= '$date 00:00:00'",
    );
    $order = "entree ASC";

    $this->_ref_next_dispo = new CAffectation;
    $this->_ref_next_dispo->loadObject($where, $order);
    $this->_ref_next_dispo->checkDaysRelative($date);
  }
  
}
?>