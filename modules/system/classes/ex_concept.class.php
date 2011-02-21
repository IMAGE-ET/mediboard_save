<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExConcept extends CMbObject {
  var $ex_concept_id = null;
  
  var $ex_list_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $prop = null; 
  
  var $_ref_ex_list = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field";
    $spec->key   = "ex_class_field_id";
    $spec->uniques["name"] = array("ex_class_id", "name");
    
    // should ignore empty values
    //$spec->uniques["coord_label"] = array("ex_class_id", "coord_label_x", "coord_label_y");
    //$spec->uniques["coord_field"] = array("ex_class_id", "coord_field_x", "coord_field_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_list_id"]  = "ref class|CExList";
    $props["name"]        = "str notNull protected canonical";
    $props["prop"]        = "str notNull";
    return $props;
  }
	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField concept_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "$this->name [$this->prop]";
    
    $this->updateTranslation();
  }
  
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
  
  function loadRefConcept($cache = true){
    return $this->_ref_concept = $this->loadFwdRef("concept_id", $cache);
  }
  
  function loadRefTranslation() {
    $trans = new CExClassFieldTranslation;
    $trans->lang = CAppUI::pref("LOCALE");
    $trans->ex_class_field_id = $this->_id;
    $trans->loadMatchingObject();
    $trans->fillIfEmpty($this->name);
    return $this->_ref_translation = $trans;
  }
  
  function loadRefEnumTranslations() {
    $trans = new CExClassFieldEnumTranslation;
    $trans->lang = CAppUI::pref("LOCALE");
    $trans->ex_class_field_id = $this->_id;
    return $trans->loadMatchingList();
  }
  
  function updateTranslation(){
  	$base = $this;
		
  	if ($this->concept_id) {
  		$base = $this->loadRefConcept();
  	}
		
    $enum_trans = $base->loadRefEnumTranslations();
    foreach($enum_trans as $_enum_trans) {
      $_enum_trans->updateLocales($this);
    }
    
    $trans = $this->loadRefTranslation();
    $this->_locale       = $trans->std;
    $this->_locale_desc  = $trans->desc;
    $this->_locale_court = $trans->court;
    
    $this->_view = $this->_locale;
    
    return $trans;
  }
  
  function getTableName(){
    return $this->loadRefExClass()->getTableName();
  }
  
  function getSpecObject(){
    return $this->_spec_object = CMbFieldSpecFact::getSpecWithClassName("CExObject", $this->name, $this->prop);
  }
  
  function getSQLSpec(){
    return $this->getSpecObject()->getFullDBSpec();
  }
  
  function store(){
    if (!$this->_id && $this->concept_id) {
      $this->prop = $this->loadRefConcept()->prop;
    }
		
    if ($msg = $this->check()) return $msg;
    
    if (!preg_match('/^[a-z0-9_]+$/i', $this->name)) {
      return "Nom de champ invalide ($this->name)";
    }
    
    $ds = $this->_spec->ds;
    
		$this->completeField("ex_class_id");
		
		// If this is not a concept
		if ($this->ex_class_id) {
	    if (!$this->_id) {
	      $table_name = $this->getTableName();
	      $sql_spec = $this->getSQLSpec();
	      $query = "ALTER TABLE `$table_name` ADD `$this->name` $sql_spec";
	      
	      if (!$ds->query($query)) {
	        return "Le champ '$this->name' n'a pas pu �tre ajout� � la table '$table_name' (".$ds->error().")";
	      }
	      
	      $spec_type = $this->getSpecObject()->getSpecType();
	      
	      // ajout de l'index
	      if (in_array($spec_type, self::$_indexed_types)) {
	        $query = "ALTER TABLE `$table_name` ADD INDEX (`$this->name`)";
	        
	        if (!$ds->query($query)) {
	          //return "L'index sur le champ '$this->name' n'a pas pu �tre ajout� (".$ds->error().")";
	        }
	      }
	    }
	    
	    else if ($this->fieldModified("name") || $this->fieldModified("prop")) {
	      $table_name = $this->getTableName();
	      $sql_spec = $this->getSQLSpec();
	      $query = "ALTER TABLE `$table_name` CHANGE `{$this->_old->name}` `$this->name` $sql_spec";
	
	      if (!$ds->query($query)) {
	        return "Le champ '$this->name' n'a pas pu �tre mis � jour (".$ds->error().")";
	      }
	    }
		}
    
    $locale       = $this->_locale;
    $locale_desc  = $this->_locale_desc;
    $locale_court = $this->_locale_court;
    $locale_enum  = $this->_enum_translation;
    
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // self translations
    if ($locale || $locale_desc || $locale_court) {
      $trans = $this->loadRefTranslation();
      $trans->std = $locale;
      $trans->desc = $locale_desc;
      $trans->court = $locale_court;
      if ($msg = $trans->store()) {
        mbTrace($msg, get_class($this), true);
      }
    }
    
    // enum translations
    if ($locale_enum) {
      $values = json_decode(utf8_encode($locale_enum), true);
      $spec = $this->getSpecObject();
      
      $enum_trans = array_values($this->loadRefEnumTranslations());
      
      foreach($values as $i => $value) {
      	$value = utf8_decode($value);
				
        if (!isset($enum_trans[$i])) {
          $enum_trans[$i] = new CExClassFieldEnumTranslation;
        }
        
        $enum_trans[$i]->ex_class_field_id = $this->_id;
        $enum_trans[$i]->key = $spec->_list[$i];
        $enum_trans[$i]->value = $value;
        $enum_trans[$i]->lang = CAppUI::pref("LOCALE");
        
        if ($msg = $enum_trans[$i]->store()) {
          mbTrace($msg, get_class($this), true);
        }
      }
    }
  }
  
  function delete(){
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }
    
    $this->completeField("ex_class_id");
    
		// If this is not a concept
    if ($this->ex_class_id && !$this->_dont_drop_column) {
      $this->completeField("name");
      
      $table_name = $this->loadRefExClass()->getTableName();
      $query = "ALTER TABLE `$table_name` DROP `$this->name`";
      $ds = $this->_spec->ds;
      
      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu �tre supprim� (".$ds->error().")";
      }
    }
    
    return parent::delete();
  }
}
