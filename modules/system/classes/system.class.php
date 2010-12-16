<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPreferences extends CMbObject {
  static $modules = array();
  
	static function loadModules() {
		foreach (glob("./modules/*/preferences.php") as $file) {
		  require_once($file);
		}
	}

	var $pref_id = null;
	
	var $user_id = null;
	var $key     = null;
	var $value   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "user_preferences";
    $spec->key   = "pref_id";
    $spec->uniques["uniques"] = array("user_id", "key");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"] = "num min|0"; //"ref class|CUser"; // Needed for the default preferences
    $specs["key"]     = "str notNull maxLength|40";
    $specs["value"]   = "str";
    return $specs;
  }
  
  static function get($user_id = 0) {
    $where["user_id"] = "= '$user_id'";
		if ($user_id != 0) {
      $where["value"] = "IS NOT NULL";
		}

  	$preferences = array();
    $pref = new self;
  	foreach($pref->loadList($where) as $_pref) {
  		$preferences[$_pref->key] = $_pref->value;
  	}

  	return $preferences;
  } 
  
  function loadRefsFwd(){
  	$this->loadRefUser();
  }
  
  function loadRefUser(){
  	return $this->loadFwdRef("user_id", true);
  }
}
?>