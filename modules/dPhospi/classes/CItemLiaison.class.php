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
 * Lien entre un s�jour et des prestations
 */
class CItemLiaison extends CMbObject{
  // DB Table key
  public $item_liaison_id;
  
  // DB Fields
  public $sejour_id;
  public $item_souhait_id;
  public $item_realise_id;
  public $sous_item_id;
  public $date;
  public $quantite;
  
  /** @var CAffectation */
  public $_ref_affectation;

  /** @var CItemPrestation */
  public $_ref_item;

  /** @var CItemPrestation */
  public $_ref_item_realise;

  /** @var CSousItemPrestation */
  public $_ref_sous_item;

  /** @var CSejour */
  public $_ref_sejour;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_liaison";
    $spec->key   = "item_liaison_id";
    $spec->uniques["unique"] = array("date", "sejour_id", "item_souhait_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["sejour_id"]       = "ref notNull class|CSejour cascade";
    $props["item_souhait_id"] = "ref class|CItemPrestation cascade";
    $props["item_realise_id"] = "ref class|CItemPrestation cascade";
    $props["sous_item_id"]    = "ref class|CSousItemPrestation cascade";
    $props["date"]            = "date";
    $props["quantite"]        = "num default|0";

    return $props;
  }

  /**
   * Charge l'item de prestation souhait�
   *
   * @return CItemPrestation
   */
  function loadRefItem() {
    return $this->_ref_item = $this->loadFwdRef("item_souhait_id", true);
  }

  /**
   * Charge l'item de prestation r�alis�
   *
   * @return CItemPrestation
   */
  function loadRefItemRealise() {
    return $this->_ref_item_realise = $this->loadFwdRef("item_realise_id", true);
  }

  /**
   * Charge le sous-item
   *
   * @return CSousItemPrestation
   */
  function loadRefSousItem() {
    return $this->_ref_sous_item = $this->loadFwdRef("sous_item_id", true);
  }

  /**
   * Charge le s�jour
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
}