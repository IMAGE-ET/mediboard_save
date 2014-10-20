<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe CChargePriceIndicator
 *
 * Table type d'activit�, mode de traitement
 */
class CChargePriceIndicator extends CMbObject {
  // DB Table key
  public $charge_price_indicator_id;
    
  // DB Table key
  public $code;
  public $type;
  public $type_pec;
  public $group_id;
  public $libelle;
  public $color;
  public $actif;

  public $_font_color;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'charge_price_indicator';
    $spec->key   = 'charge_price_indicator_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]     = "str notNull";
    $sejour = new CSejour();
    $props["type"]     = $sejour->_props["type"];
    $props["type_pec"] = $sejour->_props["type_pec"];
    $props["color"]    = "color default|ffffff notNull";
    $props["group_id"] = "ref notNull class|CGroups";
    $props["libelle"]  = "str";
    $props["actif"]    = "bool default|0";
    
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour charge_id";
    $backProps['protocoles'] = 'CProtocole charge_id';
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->libelle ? $this->libelle : $this->code;
    $this->_shortview = $this->code;

    $this->_font_color = CColorSpec::get_text_color($this->color) > 130 ? '000000' : "ffffff";
  }
}
