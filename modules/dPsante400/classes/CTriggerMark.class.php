<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTriggerMark extends CMbObject {
  public $mark_id;

  // DB Fields
  public $trigger_class;
  public $trigger_number;
  public $when;
  public $mark;
  public $done;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "trigger_mark";
    $spec->key   = "mark_id";
    $spec->loggable = false;
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["trigger_class"]  = "str notNull";
    $props["trigger_number"] = "numchar notNull maxLength|10";
    $props["when"]           = "dateTime";
    $props["done"]           = "bool notNull";
    $props["mark"]           = "str notNull";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "Mark for $this->trigger_class #$this->trigger_number";
  }
}
