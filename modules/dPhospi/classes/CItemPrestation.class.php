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

/**
 * Items de prestation
 */
class CItemPrestation extends CMbMetaObject {
  // DB Table key
  public $item_prestation_id;
  
  // DB Fields
  public $nom;
  public $rank;
  public $color;

  // Form field
  public $_quantite;

  // References
  /** @var CPrestationPonctuelle|CPrestationJournaliere */
  public $_ref_object;

  // Distant fields
  /** @var  CSousItemPrestation[] */
  public $_refs_sous_items;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_prestation";
    $spec->key   = "item_prestation_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]          = "str notNull seekable";
    /*$specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";*/
    $props["object_class"] = "enum list|CPrestationPonctuelle|CPrestationJournaliere";
    $props["rank"]         = "num pos default|1";
    $props["color"]        = "color show|0";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["liaisons_souhaits"] = "CItemLiaison item_souhait_id";
    $backProps["liaisons_realises"] = "CItemLiaison item_realise_id";
    $backProps["liaisons_lits"]     = "CLitLiaisonItem item_prestation_id";
    $backProps["sous_items"]        = "CSousItemPrestation item_prestation_id";

    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * Charge la prestation
   *
   * @return CPrestationPonctuelle|CPrestationJournaliere
   */
  function loadRefObject() {
    $this->_ref_object = new $this->object_class;
    return $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }

  /**
   * Charge les sous-items
   *
   * @return CSousItemPrestation[]
   */
  function loadRefsSousItems() {
    return $this->_refs_sous_items = $this->loadBackRefs("sous_items", "nom");
  }
}