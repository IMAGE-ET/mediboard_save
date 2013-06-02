<?php

/**
 * $Id$
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Classe non persistente d'acte pouvant �tre associ�es � un codable
 *
 * @see CCodable
 */
class CActe extends CMbMetaObject {
  public $montant_depassement;
  public $montant_base;
  public $execution;
  
  // DB References
  public $executant_id;
  public $facturable;

  // Form fields
  public $_preserve_montant;
  public $_montant_facture;
  
  // Derived fields
  public $_full_code;
  
  // Behaviour fields
  public $_check_coded = true;
  public $_permissive;
  
  // Distant object
  /** @var CSejour */
  public $_ref_sejour;
  /** @var CPatient */
  public $_ref_patient;
  /** @var CMediusers Probable user */
  public $_ref_praticien;
  /** @var CMediusers Actual user */
  public $_ref_executant;

  /** @var CMediusers[] */
  public $_list_executants;

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
  }
  
  /**
   * Charge le s�jour associ�
   *
   * @return CSejour
   */
  function loadRefSejour() {
    /** @var CCodable $object */
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }

    return $this->_ref_sejour = $object->loadRefSejour();
  }
  
  /**
   * Charge le patient associ�
   *
   * @return CPatient
   */
  function loadRefPatient() {
    /** @var CCodable $object */
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }

    return $this->_ref_patient = $object->loadRefPatient();
  }
  
  /**
   * Charge le praticien associ�
   *
   * @return CMediusers
   */
  function loadRefPraticien() {
    /** @var CCodable $object */
    if (null == $object = $this->loadTargetObject()) {
      return null;
    }
    
    return $this->_ref_praticien = $object->loadRefPraticien();
  }

  /**
   * Charge l'ex�cutant associ�
   *
   * @return CMediusers
   */
  function loadRefExecutant() {
    /** @var CMediusers $executant */
    $executant = $this->loadFwdRef("executant_id", true);
    $executant->loadRefFunction();
    return $this->_ref_executant = $executant;
  }

  /**
   * Charge les ex�cutants possibles
   *
   * @return CMediusers[]|null Ex�cutants possible, null si ex�cutant d�termin�
   */
  function loadListExecutants() {
    $user = CMediusers::get(); 
    $this->_list_executants = $user->loadProfessionnelDeSante(PERM_READ);

    // No executant guess for the existing acte
    if ($this->executant_id || $this->_id) {
      return null;
    }
    
    // User executant
    if (CAppUI::pref("user_executant")) {
      $this->executant_id = $user->_id;
      return null;
    }

    // Referring pratician executant
    $praticien = $this->loadRefPraticien();
    if ($praticien && $praticien->_id) {
      $this->executant_id = $praticien->_id;
      return null;
    }

    return $this->_list_executants;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["object_id"]           = "ref notNull class|CCodable meta|object_class";
    $props["executant_id"]        = "ref notNull class|CMediusers";
    $props["montant_base"]        = "currency";
    $props["montant_depassement"] = "currency";
    $props["execution"]           = "dateTime notNull";
    $props["facturable"]          = "bool notNull default|1 show|0";

    $props["_montant_facture"]    = "currency";

    return $props;
  }
  
  /**
   * Check if linked object is already coded
   *
   * @return string|null Error message, null when succesfull
   */
  function checkCoded() {
    if (!$this->_check_coded || $this->_forwardRefMerging) {
      return null;
    }
    
    $this->completeField("object_class");
    $this->completeField("object_id");
    /** @var CCodable $object */
    $object = new $this->object_class;
    $object->load($this->object_id);
    if ($object->_coded == "1") {
      return CAppUI::tr($object->_class) ." d�j� valid�e : Impossible de coter l'acte";
    }

    return null;
  }

  /**
   * Tell wether acte is ready for precoding
   *
   * @return bool
   */
  function getPrecodeReady() {
    return false;
  }
  
  /**
   * Return a full serialised code
   *
   * @return string Serialised full code
   */
  function makeFullCode() {
    return $this->_full_code = "";
  }

  /**
   * Precode with a full serialised code for the act
   *
   * @param string $code Serialised full code
   *
   * @return void
   */
  function setFullCode($code) {
  }

  /**
   * Update montant
   *
   * @return string|null Error message
   */
  function updateMontant(){
    if ($this->_preserve_montant || $this->_forwardRefMerging) {
      return null;
    }

    /** @var CCodable $object */
    $object = new $this->object_class;
    $object->load($this->object_id);
    
    // Permet de mettre a jour le montant dans le cas d'une consultation
    return $object->doUpdateMontants();
  }

  /**
   * Charge l'ex�cution
   *
   * @return void
   */
  function loadExecution() {
    /** @var CCodable $object */
    $object = $this->loadTargetObject();
    $object->getActeExecution();
    $this->execution = $object->_acte_execution;
  }

  /**
   * @see parent::store()
   */
  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }
    
    return $this->updateMontant();
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    if (!$this->_purge) {
      return $this->updateMontant();
    }
    return null;
  }
}
