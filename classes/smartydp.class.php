<?php /* CLASSES $Id$ */
/**
 * @package dotproject
 * @subpackage classes
 * @author Thomas Despoix
 */

CAppUI::requireLibraryFile("smarty/libs/Smarty.class");
CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.escape");
CAppUI::requireLibraryFile("json/JSON");

/**
 * Delegates the actual translation to $AppUI framework object
 */
function do_translation($params, $content, &$smarty, &$repeat) {
  if (isset($content)) {
    $content = CAppUI::tr($content);
    
    foreach ($params as $_key => $_val) {
      switch ($_key) {
        case "escape":
          if($_val=="JSAttribute"){
            $content = JSAttribute($content);
          }else{
            $content = smarty_modifier_escape($content, $_val);
          }
          break;
          
        default:
      }
    }
    return $content;
  }
}

/**
 * Render an image using phpThumb
 */
function thumb($params, &$smarty) {
  $finUrl = "";
  foreach ($params as $_key => $_val) {
    if($_key == "src") {
      $src = urlencode(CAppUI::conf("root_dir")."/".$_val);
    } else {
      $finUrl .= ("&amp;$_key=$_val");
    }
  }
  
  
  return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" alt=\"thumb\" />";
}

function smarty_modifier_json($object) {
  if (function_exists("json_encode")) {
    return json_encode($object);
  }
  
  // create a new instance of Services_JSON
  $json = new Services_JSON();
  $sJson = html_entity_decode($json->encode($object),ENT_NOQUOTES);
  
  return strtr($sJson, array("&quot;"=>"\\\""));
}

function smarty_modifier_const($object, $constName) {
  $class = new ReflectionClass($object);
  if (null == $const = $class->getConstant($constName)) {
    trigger_error("Constant '$constName' for class '$class->name' does not exist", E_USER_WARNING);
  }
  
  return $const;
}

function JSAttribute($string){
  return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'&quot;',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
}


function smarty_modifier_cleanField($string){
  if (!is_scalar($string)) {
    return $string;
  }

  return htmlspecialchars($string,ENT_QUOTES);

}

function smarty_modifier_stripslashes($string){
  return stripslashes($string);
}


/**
 * @param array params tableau des parametres
 * - object          : Objet
 * - field           : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 * - prop            : {optionnel} Specification du champs, par defaut, celle de la classe
 * - separator       : {optionnel} S�paration entre les champs de type "radio" [default: ""]
 * - cycle           : {optionnel} Cycle de r�p�tition du s�parateur (pour les enums en type radio) [default: "1"]
 * - typeEnum        : {optionnel} Type d'affichage des enums (values : "select", "radio") [default: "select"]
 * - defaultOption   : {optionnel} Ajout d'un "option" en amont des valeurs ayant pour value ""
 * - class           : {optionnel} Permet de donner une classe aux champs
 * - hidden          : {optionnel} Permet de forcer le type "hidden"
 * - canNull         : {optionnel} Permet de passer outre le notNull de la sp�cification
 */
function smarty_function_mb_field($params, &$smarty) {
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

  $object  = CMbArray::extract($params, "object", null, true);
  $field   = CMbArray::extract($params, "field" , null, true);
  $propKey = array_key_exists("prop", $params);
  $prop    = CMbArray::extract($params, "prop");
  $canNull = CMbArray::extract($params, "canNull");
 
  $spec = $propKey ?  CMbFieldSpecFact::getSpec($object, $field, $prop) : $object->_specs[$field];
  
  if ($canNull === "true" || $canNull === true) {
    $spec->notNull = 0;
    $tabSpec = split(" ",$spec->prop);
    CMbArray::removeValue("notNull", $tabSpec);
    $spec->prop = join($tabSpec, " ");
  }
  
  if ($canNull === "false" || $canNull === false) {
    $spec->notNull = 1;
    $spec->prop = "canNull $spec->prop";
  }
  
  return $spec->getFormElement($object, $params);
}


/**
 * Show a value if different from previous cached one
 * @param array params Smarty parameters
 * - name  : Name of the cached value
 * - value : Value to show, empty string to clear out cache
 */
function smarty_function_mb_ditto($params, &$smarty) {
  static $cache = array();
  $name   = CMbArray::extract($params, "name", null, true);
  $value  = CMbArray::extract($params, "value", null, true);
  $old = CMbArray::get($cache, $name, "");
  $cache[$name] = $value;
  return $old != $value ? $value : "|";
}

/**
 * Fonction that return the value of an object field
 */
function smarty_function_mb_value($params, &$smarty) {
  return $params["object"]->_specs[$params["field"]]->getValue($params["object"], $smarty, $params);
}

/**
 * Fonction d'�criture  des labels
 * @param array params tableau des parametres
 * - object      : Objet
 * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 * - defaultFor  : {optionnel} Ajout d'une valeur � cibler pour "select" ou "radio"
 * - typeEnum    : {optionnel} Type d'affichage des enums � cibler (values : "select", "radio") [default: "select"]
 */
function smarty_function_mb_label($params, &$smarty) {
  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
    $object = new $class;
  }
  
  $field      = CMbArray::extract($params, "field" , null, true);
  
  if (!array_key_exists($field, $object->_specs)) {
     $object->_specs[$field] = CMbFieldSpecFact::getSpec($object, $field, "");
     trigger_error("Spec missing for class '$object->_class_name' field '$field'", E_USER_WARNING);
  }

  return $object->_specs[$field]->getLabelElement($object, $params);
}

/**
 * Fonction d'�criture  des labels de titre
 * @param array params tableau des parametres
 * - object      : Objet
 * - field       : Nom du champ a afficher (le champs doit avoir des specs sinon "spec" non optionnel) 
 */
function smarty_function_mb_title($params, &$smarty) {
  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
    $object = new $class;
  }
  
  $field = CMbArray::extract($params, "field" , null, true);

  return $object->_specs[$field]->getTitleElement($object, $params);
}

/**
 * Fonction d'�criture  des labels
 * @param array params 
 * - var   : Name of the new variable
 * - test  : Test for ternary operator 
 * - value : Value if test is true
 * - other : Value if test is false
 */
function smarty_function_mb_ternary($params, &$smarty) {
  $var   = CMbArray::extract($params, "var"   , null, true);
  $test  = CMbArray::extract($params, "test"  , null, true);
  $value = CMbArray::extract($params, "value" , null, true);
  $other = CMbArray::extract($params, "other" , null, true);
  
  $smarty->assign($var, $test ? $value : $other);
}

function smarty_function_mb_colonne($params, &$smarty) {
  $class     = CMbArray::extract($params, "class"     , null, true);
  $field     = CMbArray::extract($params, "field"     , null, true);
  $order_col = CMbArray::extract($params, "order_col" , null, true);
  $order_way = CMbArray::extract($params, "order_way" , null, true);
  $url       = CMbArray::extract($params, "url"       , null, true);
  
  $sHtml  = "<label for=\"$field\" title=\"".CAppUI::tr($class."-".$field."-desc")."\">";
  $sHtml .= CAppUI::tr($class."-".$field);
  $sHtml .= "</label>";
    
  $css_class = ($order_col == $field) ? "sorted" : "sortable";
  $order_way_inv = ($order_way == "ASC") ? "DESC" : "ASC";
   
  if($css_class == "sorted"){
  	return "<a class='$css_class $order_way' href='$url&amp;order_col=$order_col&amp;order_way=$order_way_inv'>$sHtml</a>";
  }
  if($css_class == "sortable"){
  	return "<a class='$css_class' href='$url&amp;order_col=$field&amp;order_way=ASC'>$sHtml</a>";
  }
}

function smarty_function_mb_include_script($params, &$smarty) {
  global $version;
  $version_build = $version['build'];
  
  // Dans le cas ou le path est fourni
  $path   = CMbArray::extract($params, "path"  );
  $module = CMbArray::extract($params, "module");
  $script = CMbArray::extract($params, "script");
    
    
  if($path){
    $path = "$path?build=$version_build";
  }
  if($module XOR $script){
    trigger_error("Module: $module Script: $script");
  }
  if($module && $script){
    $path = "modules/$module/javascript/$script.js?build=$version_build";  
  }
  
  
  return "<script type='text/javascript' src='$path'></script>";
}

/**
 * dotProject integration of Smarty engine main class
 *
 * Provides an extension of smarty class with directory initialization
 * integrated to dotProject framework as well as standard data assignment
 */
class CSmartyDP extends Smarty {

  /**
   * Construction
   *
   * Directories initialisation
   * Standard data assignment
   */
  function CSmartyDP($rootDir = null) {
    global $AppUI, $version, $dPconfig, $can, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $suppressHeaders;
    
    $root = $dPconfig["root_dir"];

    if (!$rootDir) {
      $rootDir = "$root/modules/$m"; 
    }
    
    // Directories initialisation
    $this->template_dir = "$rootDir/templates/";
    $this->compile_dir  = "$rootDir/templates_c/";
    $this->config_dir   = "$rootDir/configs/";
    $this->cache_dir    = "$rootDir/cache/";
    
    // Debugginf directives
    $this->debug_tpl = "classes/smarty_debug.tpl";
    $this->debugging = false;
    
    // Delimiter definition
    $this->left_delimiter = "{{";
    $this->right_delimiter = "}}";
    
    // Default modifier for security reason
    $this->default_modifiers = array("@cleanField");
    
    // Register mediboard functions
    $this->register_block   ("tr"                , "do_translation"); 
    $this->register_function("thumb"             , "thumb");
    $this->register_function("mb_ditto"          , "smarty_function_mb_ditto");
    $this->register_function("mb_field"          , "smarty_function_mb_field");
    $this->register_function("mb_value"          , "smarty_function_mb_value");
    $this->register_function("mb_label"          , "smarty_function_mb_label");
    $this->register_function("mb_title"          , "smarty_function_mb_title");
    $this->register_function("mb_ternary"        , "smarty_function_mb_ternary");
    $this->register_function("mb_colonne"        , "smarty_function_mb_colonne");
    $this->register_function("mb_include_script" , "smarty_function_mb_include_script");
    $this->register_modifier("json"              , "smarty_modifier_json");
    $this->register_modifier("const"             , "smarty_modifier_const");
    $this->register_modifier("cleanField"        , "smarty_modifier_cleanField");
    $this->register_modifier("stripslashes"      , "smarty_modifier_stripslashes");
    $this->register_modifier("JSAttribute"       , "JSAttribute");
    
    $modules = CModule::getActive();
    foreach ($modules as $mod) {
    	$mod->canDo();
    }
    
    // Standard data assignment
    $this->assign("app", $AppUI);
    $this->assign("dPconfig", $dPconfig);
    $this->assign("user", $AppUI->user_id); // shouldn't be necessary
    $this->assign("version", $version); 
    $this->assign("suppressHeaders", $suppressHeaders);
    $this->assign("can", $can);
    $this->assign("m", $m);
    $this->assign("a", $a);
    $this->assign("tab", $tab);
    $this->assign("action", $action);
    $this->assign("actionType", $actionType);
    $this->assign("g", $g);
    $this->assign("dialog", $dialog);
    $this->assign("ajax", $ajax);
    $this->assign("modules",$modules);

  }
  
  /**
   * Show debug spans
   *
   * @param string $tpl_file
   * @param string $vars
   */
  function showDebugSpans($tpl_file, $vars) {
    // The span
	  echo "\n<span class='smarty-include'>";
	  echo "\n$tpl_file";
	  foreach ($vars as $var => $value) {
	    $show = $value;
	    if ($value instanceof CMbObject) {
	      $show = $value->_guid;
	    }
	
	    if (is_array($value)) {
	      $count = count($value);
	      $show = "array ($count)";
	    }
	
	     
	    echo "\n<br />$var: $show";
	  }

	  echo "\n</span>\n";
	}
  
  /**
   * called for included templates
   *
   * @param string $params["smarty_include_tpl_file"]
   * @param string $params["smarty_include_vars"]
   */
  function _smarty_include($params) {
  	$tpl_file = $params["smarty_include_tpl_file"];
    $vars     = $params["smarty_include_vars"];
    
    // Only at debug time
    if (!CAppUI::conf("debug") || 
        isset($params["smarty_include_vars"]['nodebug']) ||
        in_array(basename($tpl_file), array("common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::_smarty_include($params);
      return;
    }
    
    $this->showDebugSpans($tpl_file, $vars);
    
    echo "\n<!-- Start include: $tpl_file -->\n";
    parent::_smarty_include($params);
    echo "\n<!-- Stop include: $tpl_file -->\n";
  }

  /**
   * executes & displays the template results
   *
   * @param string $resource_name
   * @param string $cache_id
   * @param string $compile_id
   */
  function display($resource_name, $cache_id = null, $compile_id = null) {
    // Only at debug time
    if (!CAppUI::conf("debug") || 
        isset($this->_tpl_vars['nodebug']) ||
        in_array(basename($resource_name), array("common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::display($resource_name, $cache_id, $compile_id);
      return;
    }
    
    $this->showDebugSpans($resource_name, array());
   
    echo "\n<!-- Start display: $resource_name -->\n";
    parent::display($resource_name, $cache_id, $compile_id);
    echo "\n<!-- Stop display: $resource_name -->\n";
  }
}
?>
