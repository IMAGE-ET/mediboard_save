<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CUniteFonctionnelle extends CMbObject {
  // DB Table key
  public $uf_id;
  
  // DB Fields
  public $group_id;
  public $code;
  public $libelle;
  public $description;
  public $type;
  
  /** @var CGroups */
  public $_ref_group;

  /** @var CAffectationUniteFonctionnelle[] */
  public $_ref_affectations_uf;
  
  /** @var CMediusers[] */
  public $_ref_praticiens;

  /** @var CLit[] */
  public $_ref_lits;

  /** @var CChambre */
  public $_ref_chambre;

  /** @var CService */
  public $_ref_service;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'uf';
    $spec->key   = 'uf_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref class|CGroups notNull";
    $props["code"]        = "str notNull seekable";
    $props["libelle"]     = "str notNull seekable";
    $props["description"] = "text";
    $props["type"]        = "enum list|hebergement|soins|medicale default|hebergement";
    
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations_uf"         ] = "CAffectationUniteFonctionnelle uf_id";

    $backProps["affectations_hebergement"] = "CAffectation uf_hebergement_id";
    $backProps["affectations_medical"    ] = "CAffectation uf_medicale_id";
    $backProps["affectations_soin"       ] = "CAffectation uf_soins_id";
    
    $backProps["sejours_hebergement"     ] = "CSejour uf_hebergement_id";
    $backProps["sejours_medical"         ] = "CSejour uf_medicale_id";
    $backProps["sejours_soin"            ] = "CSejour uf_soins_id";
    
    $backProps["protocoles_hebergement"  ] = "CProtocole uf_hebergement_id";
    $backProps["protocoles_medical"      ] = "CProtocole uf_medicale_id";
    $backProps["protocoles_soin"         ] = "CProtocole uf_soins_id";
    
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  static function getUF($code_uf, $type = null, $group_id = null) {
    $uf       = new self;
    $uf->code = $code_uf;
    $uf->type = $type;
    $uf->group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
    $uf->loadMatchingObject();

    return $uf;
  }
  
  static function getUFs() {
    $uf = new self;
    $group_id = CGroups::loadCurrent()->_id;
    
    return array(
      "hebergement" => $uf->loadList(array("type" => "= 'hebergement'", "group_id" => "= '$group_id'"), "libelle"),
      "medicale"    => $uf->loadList(array("type" => "= 'medicale'", "group_id" => "= '$group_id'"), "libelle"),
      "soins"       => $uf->loadList(array("type" => "= 'soins'", "group_id" => "= '$group_id'"), "libelle"),
    );
  }
}

