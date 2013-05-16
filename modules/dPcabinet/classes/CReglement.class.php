<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


/**
 * Les r�glements
 */
class CReglement extends CMbMetaObject {
  // DB Table key
  public $reglement_id;

  // DB References
  public $banque_id;

  // DB fields
  public $date;
  public $montant;
  public $emetteur;
  public $mode;
  public $object_class;
  public $object_id;
  public $reference;
  public $num_bvr;
  
  /** @var CBanque */
  public $_ref_banque;

  /** @var CFacture */
  public $_ref_facture;
  
  var $_update_facture = true;
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'reglement';
    $spec->key   = 'reglement_id';
    return $spec;
  }
  
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $props = parent::getProps();
    $props['object_class']    = 'enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet';
    $props['banque_id']       = 'ref class|CBanque';
    $props['date']            = 'dateTime notNull';
    $props['montant']         = 'currency notNull';
    $props['emetteur']        = 'enum notNull list|patient|tiers';
    $props['mode']            = 'enum notNull list|cheque|CB|especes|virement|BVR|autre default|cheque';
    $props['reference']       = 'str';
    $props['num_bvr']         = 'str';
    return $props;
  }
  
  /**
   * Accesseur sur la banque
   * 
   * @return CBanque La banque
   */
  function loadRefBanque() {
    return $this->_ref_banque = $this->loadFwdRef("banque_id", true);
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
   */
  function loadRefsFwd() {
    $this->loadTargetObject();
    $this->loadRefBanque();
  }
  
  /**
   * V�rification des champs
   * 
   * @return string|null
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    $this->completeField("montant", "mode");
    
    if (!$this->montant) {
      return 'Le montant du r�glement ne doit pas �tre nul';
    }
    
    if (!$this->mode) {
      return 'Le mode de paiment ne doit pas �tre nul';
    }

    $this->loadRefsFwd();
  }
  
  /**
   * Accesseur sur la facture
   * 
   * @return array La facture
   */
  function loadRefFacture() {
    $target = $this->loadTargetObject();
    $target->loadRefsObjects();
    $target->loadRefPatient();
    $target->loadRefPraticien();
    return $this->_ref_facture = $target;
  }
  
  /**
   * Acquite la facture automatiquement
   * 
   * @return string|null
   */
  function acquiteFacture() {
    $this->loadRefsFwd();
    $facture = $this->_ref_object;
    $facture->loadRefsObjects();
    $facture->loadRefsReglements();
    
    // Acquitement patient
    if ($this->emetteur == "patient" && $facture->du_patient) {
      $facture->patient_date_reglement = $facture->_du_restant_patient <= 0 ? CMbDT::date() : "";
    }
    
    // Acquitement tiers
    if ($this->emetteur == "tiers" && $facture->du_tiers) {
      $facture->tiers_date_reglement = $facture->_du_restant_tiers <= 0 ? CMbDT::date() : "";
    }
    
    return $facture->store();
  }
  
  /**
   * Red�finition du store
   * 
   * @return string|null
   */
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }
  
  /**
   * Red�finition du delete
   * 
   * @return string|null
   */
  function delete() {
    // Preload consultation
    $this->load();
    $this->loadRefsFwd();
    
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }
  
  function getPerm($permType) {
    return $this->loadTargetObject()->getPerm($permType);
  }
}
