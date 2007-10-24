<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage admin
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

if(!defined("PERM_DENY")) {
  define("PERM_DENY" , "0");
  define("PERM_READ" , "1");
  define("PERM_EDIT" , "2");
}

/**
 * The CPermObject class
 */
class CPermObject extends CMbObject {
  // DB Table key
  var $perm_object_id = null;
  
  // DB Fields
  var $user_id      = null;
  var $object_id    = null;
  var $object_class = null;
  var $permission   = null;
  
  // References
  var $_ref_db_user   = null;
  var $_ref_db_object = null;
  
  function CPermObject() {
    $this->CMbObject("perm_object", "perm_object_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "user_id"      => "notNull ref class|CUser",
      "object_id"    => "ref class|CMbObject meta|object_class cascade",
      "object_class" => "notNull str",
      "permission"   => "notNull numchar maxLength|1"
    );
  }
  
  function loadRefDBObject() {
    $this->_ref_db_object = new $this->object_class;
    $this->_ref_db_object->load($this->object_id);
  }

  function loadRefDBUser() {
    $this->_ref_db_user = new CUser;
    $this->_ref_db_user->load($this->user_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefDBObject();
    $this->loadRefDBUser();
  }
  
  
  // Chargement des droits du user
  function loadExactPermsObject($user_id = null){
    global $AppUI, $userPermsObject, $permissionSystemeDown;
    
    $perm = new CPermObject;
    $listPermsObjects = array();
    $where = array();
    $where["user_id"] = "= '$user_id'";
    return $listPermsObject = $perm->loadList($where);
  }
  
  // Those functions are statics
  function loadUserPerms($user_id = null) {
    global $AppUI, $userPermsObjects, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    
    // D�claration du user
    $user = new CUser();
    if($user_id !== null){
      $user->load($user_id);
    } else {
      $user->load($AppUI->user_id);
    }
    
    $perm = new CPermObject;
    
    //Declaration des tableaux de droits 
    $permsObjectProfil = array();
    $permsObjectSelf = array();
    $permsObjectFinal = array();
    
    // Declaration des tableaux de droits
    $tabObjectProfil = array();
    $tabObjectSelf = array();
    $tabObjectFinal = array();
    
    //Chargement des droits
    $permsObjectProfil = $perm->loadExactPermsObject($user->profile_id);
    $permsObjectSelf = $perm->loadExactPermsObject($user->user_id);
    
    // Creation du tableau de droits du user
    foreach($permsObjectSelf as $key => $value){
      $tabObjectSelf["obj_".$value->object_id.$value->object_class] = $value;
    }
    
    // Creation du tableau de droits du profil
    foreach($permsObjectProfil as $key => $value){
      $tabObjectProfil["obj_".$value->object_id.$value->object_class] = $value;
    }
    // Fusion des deux tableaux de droits
    $tabObjectFinal = array_merge($tabObjectProfil, $tabObjectSelf);
    
    
    // Creation du tableau de fusion des droits
    foreach($tabObjectFinal as $object => $value){
      $permsObjectFinal[$value->perm_object_id] = $value;
    }

    // Tri du tableau de droit final en fonction des cle (perm_module_id)
    ksort($permsObjectFinal);

    $userPermsObjects = array();
    foreach($permsObjectFinal as $perm_obj) {
      if(!$perm_obj->object_id){
        $userPermsObjects[$perm_obj->object_class][0] = $perm_obj;
      }else{
        $userPermsObjects[$perm_obj->object_class][$perm_obj->object_id] = $perm_obj;
      }
    }
    return $userPermsObjects;
  }
  
  function getPermObject($object, $permType) {
    global $AppUI, $userPermsObjects, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $result = PERM_DENY;
    $object_class = get_class($object);
    $object_id    = $object->_id;
    if(isset($userPermsObjects[$object_class][0]) || isset($userPermsObjects[$object_class][$object_id])) {
      if(isset($userPermsObjects[$object_class][0])) {
        $result = $userPermsObjects[$object_class][0]->permission;
      }
      if(isset($userPermsObjects[$object_class][$object_id])) {
        $result = $userPermsObjects[$object_class][$object_id]->permission;
      }
    } else {
      return $object->_ref_module->getPerm($permType);
    }
    return $result >= $permType;
  }
  
  function check() {
    // Data checking
    $msg = null;
    if(!$this->perm_object_id) {
      $where = array();
      $where["user_id"]      = $this->_spec->ds->prepare("= %",$this->user_id);
      $where["object_class"] = $this->_spec->ds->prepare("= %",$this->object_class);
      if($this->object_id){
        $where["object_id"]    = $this->_spec->ds->prepare("= %",$this->object_id);
      }else{
        $where["object_id"]    = "IS NULL";
      }
      
      $sql = new CRequest();
      $sql->addSelect("count(perm_object_id)");
      $sql->addTable("perm_object");
      $sql->addWhere($where);
      
      $nb_result = $this->_spec->ds->loadResult($sql->getRequest());
      
      if($nb_result){
        $msg.= "Une permission sur cet objet existe d�j�.<br />";
      }
    }
    return $msg . parent::check();
  }
}

CPermObject::loadUserPerms();

?>