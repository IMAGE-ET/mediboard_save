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
  
  static $_prefixe = null;
  
  // DB Table key
  public $lit_id;
  
  // DB References
  public $chambre_id;

  // DB Fields
  public $nom;
  public $nom_complet;
  public $annule;
  
  // Form Fields
  public $_overbooking;
  public $_selected_item;
  public $_lines;
  public $_sexe_other_patient;
  public $_affectation_id;
  public $_sejour_id;

  // Object references
  
  /**
   * @var CChambre
   */
  public $_ref_chambre;
  /**
   * @var CService
   */
  public $_ref_service;
  public $_ref_affectations;
  public $_ref_last_dispo;
  public $_ref_next_dispo;
  
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
    $backProps["ufs"]              = "CAffectationUniteFonctionnelle object_id";
    $backProps["liaisons_items"]   = "CLitLiaisonItem lit_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["chambre_id"]  = "ref notNull class|CChambre seekable";
    $specs["nom"]         = "str notNull seekable";
    $specs["nom_complet"] = "str seekable";
    $specs["annule"]      = "bool default|0";
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
    $this->checkDispo($date);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = self::$_prefixe . ($this->nom_complet ? $this->nom_complet : $this->nom);
  }
  
  function loadCompleteView() {
    $this->loadRefsFwd();
    
    $chambre =& $this->_ref_chambre;
    $chambre->loadRefsFwd();
    $this->_view = $this->nom_complet ? self::$_prefixe . $this->nom_complet : "{$chambre->_ref_service->_view} $chambre->_view $this->_shortview";
  }

  /**
   * @return CChambre
   */
  function loadRefChambre() {
    $this->_ref_chambre =  $this->loadFwdRef("chambre_id", true);
    $this->_view = $this->nom_complet ? self::$_prefixe . $this->nom_complet : "{$this->_ref_chambre->_view} - $this->_shortview";
    return $this->_ref_chambre;
  }

  /**
   * @return CService
   */
  function loadRefService() {
    if (!$this->_ref_chambre) {
      $this->loadRefChambre();
    } 
    
    return $this->_ref_service = $this->_ref_chambre->loadRefService();
  }  

  function loadRefsFwd() {
    $this->loadRefChambre();
  }
  
  function getPerm($permType) {
    if (!$this->_ref_chambre) {
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
  
  function loadRefsLiaisonsItems() {
    return $this->_ref_liaisons_items = $this->loadBackRefs("liaisons_items");
  }
  
  /**
   * Construit le tag Lit en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger l'id externe d'un lit pour un �tablissement donn� si non null
   *
   * @return string
   */
  static function getTagLit($group_id = null) {
    // Pas de tag Lit
    if (null == $tag_lit = CAppUI::conf("dPhospi CLit tag")) {
      return;
    }

    // Permettre des id externes en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_lit);
  }
}

CLit::$_prefixe = CAppUI::conf("dPhospi CLit prefixe");
