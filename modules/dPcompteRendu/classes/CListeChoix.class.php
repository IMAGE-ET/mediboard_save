<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage CompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Listes de choix
 */
class CListeChoix extends CMbObject {
  // DB Table key
  public $liste_choix_id;

  // DB References
  public $user_id; // not null when associated to a user
  public $function_id; // not null when associated to a function
  public $group_id; // not null when associated to a group

  // DB fields
  public $nom;
  public $valeurs;
  public $compte_rendu_id;
  
  // Form fields
  public $_valeurs;
  public $_new;
  public $_del;
  public $_owner;
  
  /** @var CMediusers */
  public $_ref_user;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CGroups */
  public $_ref_group;

  /** @var CCompteRendu */
  public $_ref_modele;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'liste_choix';
    $spec->key   = 'liste_choix_id';
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]         = "ref class|CMediusers";
    $props["function_id"]     = "ref class|CFunctions";
    $props["group_id"]        = "ref class|CGroups";
    $props["nom"]             = "str notNull";
    $props["valeurs"]         = "text confidential";
    $props["compte_rendu_id"] = "ref class|CCompteRendu cascade";
    
    $props["_owner"]           = "enum list|prat|func|etab";
    return $props;
  }

  /**
   * Charge l'utilisateur associ� � la liste de choix
   *
   * @return CMediusers
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", true);
  }

  /**
   * Charge la fonction associ�e � la liste de choix
   *
   * @return CFunctions
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * Charge l'�tablissement associ� associ�e � la liste de choix
   *
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * Charge le propri�taire de la liste
   *
   * @return CMediusers|CFunctions|CGroups
   */
  function loadRefOwner() {
    return CValue::first(
      $this->loadRefUser(),
      $this->loadRefFunction(),
      $this->loadRefGroup()
    );
  }

  /**
   * Charge le mod�le associ�
   *
   * @return CCompteRendu
   */
  function loadRefModele() {
    return $this->_ref_modele = $this->loadFwdRef("compte_rendu_id", true);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_valeurs = $this->valeurs != "" ? explode("|", $this->valeurs) : array();
    natcasesort($this->_valeurs);

    if ($this->user_id) {
      $this->_owner = "prat";
    }

    if ($this->function_id) {
      $this->_owner = "func";
    }

    if ($this->group_id) {
      $this->_owner = "etab";
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if ($this->_new !== null) {
      $this->updateFormFields();
      $this->_valeurs[] = trim($this->_new);
      natcasesort($this->_valeurs);
      $this->valeurs = implode("|", $this->_valeurs);
    }

    if ($this->_del !== null) {
      $this->updateFormFields();
      foreach ($this->_valeurs as $key => $value) {
        if (trim($this->_del) == trim($value)) {
          unset($this->_valeurs[$key]);
        }
      }
      $this->valeurs = implode("|", $this->_valeurs);
    }
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $owner = $this->loadRefOwner();
    return $owner->getPerm($permType);
  }

  /**
   * Charge les listes d'un utilisateur
   *
   * @param int $user_id User ID
   *
   * @return self[]
   */
  static function loadAllFor($user_id) {
    $user = new CMediusers;
    $user->load($user_id);

    $listes = array();
    foreach ($user->getOwners() as $type => $owner) {
      $listes[$type] = $owner->loadBackRefs("listes_choix", "nom");
    }
    return $listes;
  }
}
