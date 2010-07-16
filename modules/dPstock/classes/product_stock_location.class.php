<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductStockLocation extends CMbObject {
  // DB Table key
  var $stock_location_id = null;

  // DB Fields
  var $name              = null;
  var $desc              = null;
	var $position          = null;
	var $group_id          = null;

  // Object References
  var $_ref_group_stocks = null;
  /**
   * @var CGroups
   */
	var $_ref_group        = null;
	
	var $_before           = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_stock_location';
    $spec->key   = 'stock_location_id';
    $spec->uniques["name"] = array("name");
    return $spec;
  }
	
  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull seekable';
    $specs['desc'] = 'text seekable';
		$specs['position'] = 'num min|1';
		$specs['group_id'] = 'ref notNull class|CGroups';
		$specs['_before']  = 'ref class|CProductStockLocation autocomplete|name|true';
    return $specs;
  }

	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["group_stocks"] = "CProductStockGroup location_id";
	  return $backProps;
	}

	function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = ($this->position ? "[$this->position] " : "") . $this->name;
  }
	
	function updateDBfields() {
		parent::updateDBfields();
		
		if ($this->_before && $this->_before != $this->_id) {
			$next_object = new self;
			$next_object->load($this->_before);
			
			if ($next_object->_id) {
				$query = '';
				if ($this->position)
					$query = "AND `position` BETWEEN $next_object->position AND $this->position";
				else if ($next_object->position)
					$query = "AND `position` >= $next_object->position";
				
				$where = array(
			    "`position` IS NOT NULL $query"
				);
				
				$this->position = $next_object->position;
				$next_objects = $this->loadList($where);
	      foreach($next_objects as &$object) {
	        $object->position++;
	        $object->store();
	      }

				if (count($next_objects) == 0) {
					$next_object->position = 2;
					$next_object->store();
					$this->position = 1;
				}
			}

			$this->_before = null;
		}
    else if (!$this->_id && !$this->position) {
      $existing = $this->loadList(null, "position");
      if ($location = end($existing)) 
        $this->position = $location->position + 1;
      else 
        $this->position = 1;
    }
	}
  
  function loadRefsStocks(){
    $ljoin = array(
      "product" => "product_stock_group.product_id = product.product_id",
    );
    return $this->loadBackRefs("group_stocks", "product.name", null, null, $ljoin);
  }

  function loadRefsFwd(){
    $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
}
?>