<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExList extends CMbObject {
  var $ex_list_id = null;
  
  var $name       = null;
  var $category_id = null;
  
  var $_ref_category = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list";
    $spec->key   = "ex_list_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"] = "str notNull";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["concepts"] = "CExList list_id";
    return $backProps;
  }
}
