<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien M�nager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CDailyCheckListTypeLink extends CMbObject {
  public $daily_check_list_type_link_id;

  public $object_class;
  public $object_id;
  public $list_type_id;

  public $_object_guid;

  /** @var CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire */
  public $_ref_object;

  /** @var CDailyCheckListType */
  public $_ref_list_type;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_list_type_link';
    $spec->key   = 'daily_check_list_type_link_id';
    $spec->uniques["object"] = array("object_class", "object_id", "list_type_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props['object_class'] = 'enum notNull list|CSalle|CBlocOperatoire default|CSalle';
    $props['object_id']    = 'ref class|CMbObject meta|object_class autocomplete';
    $props['list_type_id'] = 'ref notNull class|CDailyCheckListType';
    $props['_object_guid'] = 'str';
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->loadRefObject()->_view." - ".$this->loadRefListType()->_view;
  }

  /**
   * @return CSalle|CBlocOperatoire|COperation|CPoseDispositifVasculaire
   */
  function loadRefObject(){
    return $this->_ref_object = $this->loadFwdRef("object_id", true);
  }

  /**
   * @return CDailyCheckListType
   */
  function loadRefListType(){
    return $this->_ref_list_type = $this->loadFwdRef("list_type_id", true);
  }
}
