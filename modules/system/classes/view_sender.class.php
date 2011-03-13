<?php /* $Id: message.class.php 8208 2010-03-04 19:14:03Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8208 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * View sender class. 
 * @abstract Sends the content of a view on FTP source handling :
 * - FTP source
 * - cron table-like period + offset planning
 * - rotation on destination
 */
class CViewSender extends CMbObject {
  // DB Table key
  var $sender_id = null; 
  
  // DB fields
  var $source_id   = null;
  var $name        = null;
  var $description = null;
  var $params      = null;
  var $period      = null;
  var $offset      = null;
  
  // Form fields
	var $_params = null;
  
  // Object references
  var $_ref_source;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "view_sender";
    $spec->key   = "sender_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["source_id"  ] = "ref class|CViewSenderSource";
    $props["name"       ] = "str notNull";
    $props["description"] = "text";
    $props["params"     ] = "text notNull";
    $props["period"     ] = "enum list|1|2|3|4|5|6|10|15|20|30 moreThan|offset";
    $props["offset"     ] = "num pos";
    return $props;
  }

  function updateFormFields() {
    parent::updataFormFields();
    $this->_view = $this->name;
  }
}

?>