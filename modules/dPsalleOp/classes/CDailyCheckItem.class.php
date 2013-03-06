<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien M�nager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItem extends CMbObject {
  public $daily_check_item_id;

  // DB Fields
  public $list_id;
  public $item_type_id;
  public $checked;
  
  /** @var CDailyCheckList */
  public $_ref_list;

  /** @var CDailyCheckItemType */
  public $_ref_item_type;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item';
    $spec->key   = 'daily_check_item_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['list_id']      = 'ref notNull class|CDailyCheckList';
    $props['item_type_id'] = 'ref notNull class|CDailyCheckItemType';
    $props['checked']      = 'enum notNull list|yes|no|nr|na';
    return $props;
  }
  
  function getAnswer(){
    return $this->getFormattedValue("checked");
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "$this->_ref_item_type (".$this->getAnswer().")";
  }
  
  function loadRefsFwd() {
    $this->loadRefList();
    $this->loadRefItemType();
  }

  /**
   * @return CDailyCheckList
   */
  function loadRefList(){
    return $this->_ref_list = $this->loadFwdRef("list_id", true);
  }

  /**
   * @return CDailyCheckItemType
   */
  function loadRefItemType(){
    return $this->_ref_item_type = $this->loadFwdRef("item_type_id", true);
  }
}
