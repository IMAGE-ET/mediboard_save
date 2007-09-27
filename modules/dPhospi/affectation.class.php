<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CAffectation. 
 * @abstract G�re les affectation des s�jours dans des lits
 */
class CAffectation extends CMbObject {
  // DB Table key
	var $affectation_id = null;
  
  // DB References
  var $lit_id    = null;
  var $sejour_id = null;

  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $confirme = null;
  var $effectue = null;
  var $rques    = null;
  
  // Form Fields
  var $_entree_relative;
  var $_sortie_relative;
  var $_mode_sortie;
  
  // Object references
  var $_ref_lit    = null;
  var $_ref_sejour = null;
  var $_ref_prev   = null;
  var $_ref_next   = null;
  var $_no_synchro = null;
  var $_list_repas = null;

	function CAffectation() {
		$this->CMbObject("affectation", "affectation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["repas"] = "CRepas affectation_id";
     return $backRefs;
  } 
 
  function getSpecs() {
    return array (
      "lit_id"       => "notNull ref class|CLit",
      "sejour_id"    => "ref class|CSejour cascade",
      "entree"       => "notNull dateTime",
      "sortie"       => "notNull dateTime",
      "_mode_sortie" => "enum list|normal|transfert|deces default|normal",
      "confirme"     => "bool",
      "effectue"     => "bool",
      "rques"        => "text"
    );
  }

	function loadView() {
  	$this->loadRefLit();
    $this->_ref_lit->loadCompleteView();
  }
  
  function check() {
    if($msg = parent::check()) {
      return $msg;
    }
    if(!$this->affectation_id) {
      return null;
    }
  	$obj = new CAffectation();
  	$obj->load($this->affectation_id);
  	$obj->loadRefsFwd();
  	
  	$obj->_mode_sortie = $this->_mode_sortie;
  	
  	if(!$this->entree && $obj->affectation_id)
  	  $this->entree = $obj->entree;
  	if(!$this->sortie && $obj->affectation_id)
  	  $this->sortie = $obj->sortie;
    if ($this->sortie <= $this->entree) {
      return "La date de sortie doit �tre sup�rieure � la date d'entr�e";
    }
    return null;
  }
  
  function deleteOne() {
    return parent::delete();
  }
  
  function delete() {
  	$this->load();
    if($this->sejour_id){
      $this->loadRefSejour();
      return $this->_ref_sejour->delAffectations();
    }
    return $this->deleteOne();
  }
  
  function store() {
    $oldAff = new CAffectation();
    if($this->_id) {
      $oldAff->load($this->_id);
      $oldAff->loadRefsAffectations();
    }
    if($msg = parent::store()) {
      return $msg;
    }
    // Modification de la date d'admission et de la dur�e de l'hospi
    $this->load($this->affectation_id);
    
    $this->loadRefSejour();
    if($oldAff->_id) {
      $this->_ref_prev = $oldAff->_ref_prev;
      $this->_ref_next = $oldAff->_ref_next;
    } else {
      $this->loadRefsAffectations();
    }
     
    $changeSejour = 0;
    $changePrev   = 0;
    $changeNext   = 0;

    if($this->_no_synchro) {
      return $msg;
    }
    if(!$this->_ref_prev->affectation_id && $this->sejour_id) {
      if($this->entree != $this->_ref_sejour->entree_prevue) {
        $this->_ref_sejour->entree_prevue = $this->entree;
        $changeSejour = 1;
      }
    } elseif($this->sejour_id) {
      if($this->entree != $this->_ref_prev->sortie) {
        $this->_ref_prev->sortie = $this->entree;
        $changePrev = 1;
      }
    }
    if(!$this->_ref_next->affectation_id  && $this->sejour_id) {
      if($this->sortie != $this->_ref_sejour->sortie_prevue) {
        $this->_ref_sejour->sortie_prevue = $this->sortie;
        $changeSejour = 1;
      }
    } elseif($this->sejour_id) {
      if($this->sortie != $this->_ref_next->entree) {
        $this->_ref_next->entree = $this->sortie;
        $changeNext = 1;
      }
    }
    if($changePrev) {
      $this->_ref_prev->store();
    }
    if($changeNext) {
      $this->_ref_next->store();
    }
    if($changeSejour) {
      $this->_ref_sejour->_date_entree_prevue = null;
      $this->_ref_sejour->_date_sortie_prevue = null;
      $this->_ref_sejour->_hour_entree_prevue = null;
      $this->_ref_sejour->_hour_sortie_prevue = null;
      $this->_ref_sejour->_min_entree_prevue  = null;
      $this->_ref_sejour->_min_sortie_prevue  = null;
      $this->_ref_sejour->store();
    }
    return $msg;
  }
  
  function loadRefLit() {
    $where = array (
      "lit_id" => "= '$this->lit_id'"
    );

    $this->_ref_lit = new CLit;
    $this->_ref_lit->loadObject($where);
  }
  
  function loadRefSejour() {
    $where = array (
      "sejour_id" => "= '$this->sejour_id'"
    );
    
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->loadObject($where);
    
  }
  
  function loadRefsFwd() {
    $this->loadRefLit();
    $this->loadRefSejour();
    $this->loadRefsAffectations();
  }
  
  function loadRefsAffectations() {
    
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "sortie" => "= '$this->entree'"
    );
    
    $this->_ref_prev = new CAffectation;
    $this->_ref_prev->loadObject($where);
    
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "entree" => "= '$this->sortie'"
    );
    
    $this->_ref_next = new CAffectation;
    $this->_ref_next->loadObject($where);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_lit) {
      $this->loadRefLit();
    }
    if(!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    return ($this->_ref_lit->getPerm($permType) && $this->_ref_sejour->getPerm($permType));
  }
  
  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = mbDaysRelative($date." 10:00:00", $this->entree);
      $this->_sortie_relative = mbDaysRelative($date." 10:00:00", $this->sortie);
    }
  }
  
  function colide($aff) {
  	if (($aff->entree < $this->sortie and $aff->sortie > $this->sortie)
     or ($aff->entree < $this->entree and $aff->sortie > $this->entree)
     or ($aff->entree >= $this->entree and $aff->sortie <= $this->sortie))
      return true;
    return false;
  }
  
  function loadMenu($date, $listTypeRepas = null){
    $this->_list_repas[$date] = array();
    $repas =& $this->_list_repas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }
    
    $where                   = array();
    $where["date"]           = $this->_spec->ds->prepare(" = %", $date);
    $where["affectation_id"] = $this->_spec->ds->prepare(" = %", $this->affectation_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $repasDuJour = new CRepas;
      $repasDuJour->loadObject($where);
      $repas[$keyType] = $repasDuJour;
    }
    
    
  }
}
?>