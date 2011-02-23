<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExListItem extends CMbObject {
  var $ex_list_item_id = null;
  
  var $list_id         = null;
  var $name            = null;
  var $value           = null;
  
  var $_ref_list       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list_item";
    $spec->key   = "ex_list_item_id";
    $spec->uniques["value"] = array("list_id", "value");
    $spec->uniques["name"]  = array("list_id", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["list_id"] = "ref notNull class|CExList cascade";
    $props["name"]    = "str notNull";
    $props["value"]   = "num";
    return $props;
  }
	
	function loadRefList($cache = true){
		return $this->_ref_list = $this->loadFwdRef("list_id", $cache);
	}
  
  function updateFormFields(){
    parent::updateFormFields();
		$list = $this->loadRefList();
    $this->_view = "{$list->_view} / $this->name [$this->value]";
  }
}
