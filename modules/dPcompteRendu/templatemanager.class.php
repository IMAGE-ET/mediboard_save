<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/


class CTemplateManager {
  var $editor = "fckeditor";
  
  var $sections = array();
  var $helpers = array();
  var $lists = array();
  
  var $template = null;
  var $document = null;
  var $usedLists = array();
  
  var $valueMode = true; // @todo : changer en applyMode
  
  function CTemplateManager() {
    $this->addProperty("G�n�ral - date du jour"  , mbTransformTime(null, null, "%d/%m/%Y"));
    $this->addProperty("G�n�ral - heure courante", mbTransformTime(null, null, "%Hh%M"));
  }

  function makeSpan($spanClass, $text) {
    // Escape entities cuz FCKEditor does so
    $text = htmlentities($text);
    
    // Keep backslashed double quotes instead of quotes 
    // cuz FCKEditor creates double quoted attributes
    $html = "<span class=\"{$spanClass}\">{$text}</span>";
    
    return $html; 
  }
  
  function addProperty($field, $value = null) {
  	$sec = split(' - ', $field, 2);
  	if (count($sec) > 1) {
  		$section = $sec[0];
  		$item = $sec[1];
  	} else {
  		$section = ' ';
  		$item = $sec;
  	}
  	
  	if (!array_key_exists($section, $this->sections)) {
  		$this->sections[$section] = array();
  	}
  	$this->sections[$section][$field] = array (
  		"view" => $item,
		"field" => $field,
		"value" => $value,
		"fieldHTML" => htmlentities("[{$field}]"),
		"valueHTML" => $value
  	);
  }

  function addList($name, $choice = null) {
    $this->lists[$name] = array (
      "name" => $name,
      // @todo : passer en regexp
      //"nameHTML" => $this->makeSpan("name", "[Liste - {$name}]"));
      "nameHTML" => htmlentities("[Liste - {$name}]"));
  } 
  
  function addHelper($name, $text) {
    $this->helpers[$name] = $text;
  }
  
  function applyTemplate($template) {
    assert($template instanceof CCompteRendu || $template instanceof CPack);
    
    if($template instanceof CCompteRendu) {
    
      if (!$this->valueMode) {
        $this->setFields($template->object_class, $template->chir_id);
      }

      $this->renderDocument($template->source);
    
    } else {
    
      if (!$this->valueMode) {
        $this->setFields("hospitalisation", $template->chir_id);
      }

      $this->renderDocument($template->_source);
    }
  }
  
  function initHTMLArea () {
    // Don't use mbSetValue which uses $m
    $_SESSION["dPcompteRendu"]["templateManager"] = $this;
   
    $smarty = new CSmartyDP("modules/dPcompteRendu");
    $smarty->assign("templateManager", $this);
    $smarty->display("init_htmlarea.tpl");
	}
  
  function setFields($modeleType) {
    if ($modeleType){
      $object = new $modeleType;
    }
    
    if(isset($object)) {
      $object->fillTemplate($this);
    }
  }
  
  function loadLists($user_id, $compte_rendu_id = 0) {
    global $AppUI;
    // Liste de choix
    $chir = new CMediusers;
    $where = array();
    if($user_id){
      $chir->load($user_id);
      $where[] = "(chir_id = '$chir->user_id' OR function_id = '$chir->function_id')";
    }else{
      $chir->load($AppUI->user_id); 
      $where["function_id"] = "= '$chir->function_id'";
    }
    $where[] = CSQLDataSource::get("std")->prepare("`compte_rendu_id` IS NULL OR compte_rendu_id = %",$compte_rendu_id); 
    $order = "nom ASC";
    $lists = new CListeChoix();
    $lists = $lists->loadList($where, $order);
    foreach ($lists as $list) {
      $this->addList($list->nom);
    }
  }
  
  
  function loadHelpers($user_id, $modeleType) {
    global $dPconfig;
    $compte_rendu = new CCompteRendu();
  	
    // Chargement de l'utilisateur courant
    $currUser = new CMediusers();
    $currUser->load($user_id);
    
    $aidesUser = array();
    $aidesFunc = array();
    
    $order = "name";
    // Where user_id
    $whereUser = array();
    $whereUser["user_id"] = $compte_rendu->_spec->ds->prepare("= %", $user_id);
    $whereUser["class"]   = $compte_rendu->_spec->ds->prepare("= %", $compte_rendu->_class_name);
    // Where function_id
    $whereFunc = array();
    $whereFunc["function_id"] = $compte_rendu->_spec->ds->prepare("= %", $currUser->function_id);
    $whereFunc["class"]       = $compte_rendu->_spec->ds->prepare("= %", $compte_rendu->_class_name);
    
    $aide = new CAideSaisie();
    // Chargement des aides
    $aidesUser = $aide->loadList($whereUser,$order);
    $aidesFunc = $aide->loadList($whereFunc,$order);
    
    $this->helpers["Aide de l'utilisateur"] = "";
    foreach($aidesUser as $aideUser){
    	if($aideUser->depend_value == $modeleType){
    	  $this->helpers[$aideUser->name] = $aideUser->text;
    	}
    }
    $this->helpers["Aide de la fonction"] = "";
    foreach($aidesFunc as $aideFunc){
    	if($aideFunc->depend_value == $modeleType){
    	  $this->helpers[$aideFunc->name] = $aideFunc->text;
    	} 
   }
  }
  
  function renderDocument($source) {
    $fields = array();
    $values = array();
    foreach($this->sections as $properties) {
    	foreach($properties as $property) {
	        $fields[] = $property["fieldHTML"];
	        $values[] = nl2br($property["valueHTML"]);
    	}
    }
    
    if(count($fields)) {
      $this->document = str_replace($fields, $values, $source);
    }
  }
  
  // Obtention des listes utilis�es dans le document
  function getUsedLists($lists) {
  	$this->usedLists = array();
    foreach($lists as $key => $value) {
      $pos = strpos($this->document, htmlentities(stripslashes("[Liste - $value->nom]")));
      if($pos !== false) {
        $this->usedLists[$pos] = $value;
      }
    }
    ksort($this->usedLists);
    return $this->usedLists;
  }
}
?>