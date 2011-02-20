<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * @abstract Stores id linkage between Mediboard and other system records
 */
 class CIdSante400 extends CMbMetaObject {
  // DB Table key
  var $id_sante400_id = null;

  // DB fields
  var $id400         = null;
  var $tag           = null;
  var $last_update   = null;

  // Derivate fields
  var $_last_id      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'id_sante400';
    $spec->key   = 'id_sante400_id';
    $spec->loggable = false;
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["id400"]        = "str notNull maxLength|25";
    $specs["tag"]          = "str maxLength|80";
    $specs["last_update"]  = "dateTime notNull";
    return $specs;
  }
  
  function updateDBFields() {
    if($this->last_update === "") {
      $this->last_update = mbDateTime();
    }
    return parent::updateDBFields();
  }
  
  /**
   * Loads a specific id400 for a given object (and optionnaly tag)
   * @return ref|CMbObject Id of the loaded object
   */
  function loadLatestFor($mbObject, $tag = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Sant� 400 � un objet de classe '$object_class'");
    }
        
    $this->_id = null;
    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
    $this->tag = $tag;
    
    // Don't load if object is undefined
    if ($mbObject->_id) {
      $this->loadMatchingObject("`last_update` DESC");
    }
    
    return $mbObject->_id;
  }
  
  /**
   * Tries to get an already bound object if id400 is not older than delay
   * @param int $delay hours number of cache duration, if null use module config
   * @return CMbObject
   */
  function getCachedObject($delay = null) {
    // Get config cache duration
    if (null === $delay) {
      $delay = CAppUI::conf("dPsante400 cache_hours");
    }
    
    // Look for object
    $this->_id = null;
    $this->loadMatchingObject("`last_update` DESC");
    $this->loadRefsFwd();

    // Check against cache duration
    if (mbDateTime("+ $delay HOURS", $this->last_update) < mbDateTime()) {
      $this->_ref_object = new $this->object_class;
    }

    return $this->_ref_object;
  }
  
  /**
   * Tries to get an already bound object if id400
   * @return CMbObject    
   */
  function getMbObject() {
    // Look for object
    $this->_id = null;
    $this->loadMatchingObject("`last_update` DESC");
    $this->loadRefsFwd();

    // Always instanciate
    if (!$this->_ref_object) {
      $this->_ref_object = new $this->object_class;
    }

    return $this->_ref_object;
  }
  
  /**
   * Binds the id400 to an object, and updates the object
   * Will only bind default object properties when it's created
   */
  function bindObject(&$mbObject, $mbObjectDefault = null) {
    $object_class = get_class($mbObject);
    if (!$mbObject instanceof CMbObject) {
      trigger_error("Impossible d'associer un identifiant Sant� 400 � un objet de classe '$object_class'");
    }

    $this->object_class = $object_class;
    $this->object_id = $mbObject->_id;
		$this->last_update = null; // In case already defined
    $this->loadMatchingObject("`last_update` DESC");
    $this->_ref_object = null; // Prevent optimisation errors
    $this->loadRefs();
    
    // Object has not been found : never created or deleted since last binding
    if (!@$this->_ref_object->_id && $mbObjectDefault) {
      $mbObjectDefault->nullifyEmptyFields();
      $mbObject->extendsWith($mbObjectDefault);
    }
    
    // Create/update bound object
    $mbObject->_id = $this->object_id;
    $mbObject->updateDBFields();
    $mbObject->repair();
    
    if ($msg = $mbObject->store()) {
      throw new Exception($msg);
    }
    
    $this->object_id = $mbObject->_id;
    $this->last_update = mbDateTime();

    // Create/update the idSante400    
    if ($msg = $this->store()) {
      throw new Exception($msg);
    }
  }
  
  static function getMatch($object_class, $tag, $id400) {
    $id_ext               = new self;
    $id_ext->object_class = $object_class;
    $id_ext->tag          = $tag;
    $id_ext->id400        = $id400;
    $id_ext->loadMatchingObject();
    
    return $id_ext;
  }
}

?>