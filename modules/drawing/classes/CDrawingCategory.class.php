<?php

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CDrawingCategory extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $drawing_category_id;

  public $group_id;
  public $function_id;
  public $user_id;

  public $name;
  public $description;
  public $creation_datetime;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "drawing_category";
    $spec->key    = "drawing_category_id";
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }

  /**
   * @see parent::store()
   */
  function store() {

    if (!$this->creation_datetime) {
      $this->creation_datetime = CMbDT::dateTime();
    }

    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = ucfirst($this->name);
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]          = "ref class|CMediusers purgeable show|1";
    $props["function_id"]      = "ref class|CFunctions purgeable";
    $props["group_id"]         = "ref class|CGroups purgeable";

    $props["name"] = "str notNull";
    $props["description"] = "text";
    $props["creation_datetime"] = "dateTime notNull";
    return $props;
  }
}
