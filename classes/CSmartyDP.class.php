<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("smarty/libs/Smarty.class");
CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.escape");

/**
 * Delegates the actual translation to CAppUI framework object
 */
function do_translation($params, $content, &$smarty, &$repeat) {
  if (isset($content)) {
    $content = CAppUI::tr($content);
    
    foreach ($params as $_key => $_val) {
      switch ($_key) {
        case "escape":
        if ($_val === "JSAttribute"){
          $content = JSAttribute($content);
          break;
        }

        $content = smarty_modifier_escape($content, $_val);
        break;
          
        default:
      }
    }
    return $content;
  }
}

/*
 * Diplays veritcal text
 */
function smarty_vertical($params, $content, &$smarty, &$repeat) {
  if (isset($content)) {
  	$orig = $content;
    $content = strip_tags($content);
		$content = preg_replace("/\s+/", chr(0xA0), $content); // == nbsp
		
		$letters = str_split($content);
		
		$html = "";
		foreach($letters as $_letter) {
			$html .= "<i>$_letter</i>";
		}
		
		return "<span class=\"vertical\"><span class=\"nowm\">$html</span><span class=\"orig\">$orig</span></span>";
  }
}

function script_main($params, $content, &$smarty, &$repeat){
  // Let the whitespace around $content
  return "
    <script type=\"text/javascript\">
      Main.add(function(){ $content });
    </script>";
}

function mb_form($params, $content, &$smarty, &$repeat){
  $fields = array(
    "m"     => CMbArray::extract($params, "m", null, true),
    "dosql" => CMbArray::extract($params, "dosql"),
    "tab"   => CMbArray::extract($params, "tab"),
    "a"     => CMbArray::extract($params, "a"),
  );
  
  $attributes = array(
    "name"   => CMbArray::extract($params, "name", null, true),
    "method" => CMbArray::extract($params, "method", "get"),
    "action" => CMbArray::extract($params, "action", "?"),
    "class"  => CMbArray::extract($params, "className", ""),
  );
  
  $attributes += $params;
  
  $fields = array_filter($fields);
  
  $_content = "";
  foreach($fields as $name => $value) {
    $_content .= "\n".CHTMLResourceLoader::getTag("input", array(
      "type"  => "hidden",
      "name"  => $name,
      "value" => $value,
    ));
  }
  
  $_content .= $content;
  
  return CHTMLResourceLoader::getTag("form", $attributes, $_content);
}

/**
 * Render an image using phpThumb
 */
function thumb($params, &$smarty) {
  $finUrl = "";
  foreach ($params as $_key => $_val) {
    if($_key === "src") {
      $src = urlencode(CAppUI::conf("root_dir")."/".$_val);
    } else {
      $finUrl .= ("&amp;$_key=$_val");
    }
  }
  
  return "<img src=\"lib/phpThumb/phpThumb.php?src=$src$finUrl\" />";
}

/**
 * @author   Pablo Dias <pablo at grafia dot com dot br>
 * @abstract pad a string to a certain length with another string. like php/str_pad
 *
 * Example:  {$text|pad:20:'.':'both'}
 *    will pad $string with dots, in both sides
 *    until $text length equal to 20 characteres
 *    (assuming that $text has less than 20 characteres)
 *
 * @param string $string The string to be padded
 * @param int $length Desired string length
 * @param string $pad_string - string used to pad
 * @param enum $pad_type - both, left or right
 */
function smarty_modifier_pad($string, $length, $pad_string = ' ', $pad_type = 'left') {
  static $pads = array(
    'left' => STR_PAD_LEFT, 
    'right'=> STR_PAD_RIGHT, 
    'both' => STR_PAD_BOTH
  );
  return str_pad($string, $length, $pad_string, $pads[$pad_type]);
} 

/**
 * @abstract JSON encode an object for Javascript use
 *
 * Example:  {$object|json}
 * @param any $object The object to be encoded
 */
function smarty_modifier_json($object, $force_object = false) {
	// $options = $force_object ? JSON_FORCE_OBJECT : 0; // Only PHP 5.3 !!
	
	if ($force_object && is_array($object) && empty($object)) {
		return "{}";
	}
	
  return json_encode($object);
}

/**
 * @abstract Format to ISO DATE
 * Example:  {$datetime|iso_date}
 * @param datetime $datetime The date to format
 */
function smarty_modifier_iso_date($datetime) {
  return strftime("%Y-%m-%d", strtotime($datetime));
}

/**
 * @abstract Format to ISO TIME
 * Example:  {$datetime|iso_time}
 * @param datetime $datetime The date to format
 */
function smarty_modifier_iso_time($datetime) {
  return strftime("%H:%M:%S", strtotime($datetime));
}

/**
 * @abstract Format to ISO DATETIME
 * Example:  {$datetime|iso_datetime}
 * @param datetime $datetime The date to format
 */
function smarty_modifier_iso_datetime($datetime) {
  return strftime("%Y-%m-%d %H:%M:%S", strtotime($datetime));
}


/**
 * @abstract Currency format modifier
 *
 * Example:  {$value|currency}
 * @param float $value The value to format
 */
function smarty_modifier_currency($value) {
  return number_format($value, 2, ",", " ") . " " . CAppUI::conf("currency_symbol");
}

/**
 * @abstract Truncate a string, with a full string titled span if actually truncated 
 *
 * Example:  {$value|spancate}
 * @param float $value The value to format
 */
function smarty_modifier_spancate($string, $length = 80, $etc = '...', $break_words = true, $middle = false) {
  CAppUI::requireLibraryFile("smarty/libs/plugins/modifier.truncate");
  $string = html_entity_decode($string);
  $truncated = smarty_modifier_truncate($string, $length, $etc, $break_words, $middle);
  $string = htmlentities($string);
  return strlen($string) > $length ? "<span title=\"$string\">$truncated</span>" : $truncated;
}

/**
 * @abstract Converts a value to decabinary format
 *
 * Example:  {$value|decabinary}
 * @param float $value The value to format
 */
function smarty_modifier_decabinary($value) {
  $decabinary = CMbString::toDecaBinary($value);
  return "<span title=\"$value\">$decabinary</span>";
}

/**
 * @abstract Percentage 2-digit format modifier
 *
 * Example:  {$value|percent}
 * @param float $value The value to format
 */
function smarty_modifier_percent($value) {
  return  !is_null($value) ? number_format($value*100, 2) . "%" : "";
}

function smarty_modifier_const($object, $name) {
	// If the first arg is an instance, we get its class name
	if (!is_string($object)) {
		$object = get_class($object);
	}
  return constant("$object::$name");
}

function smarty_modifier_static($object, $name) {
  if (!is_string($object)) {
    $object = get_class($object);
  }
  
  $class = new ReflectionClass($object);
  $statics = $class->getStaticProperties();
  if (!isset($statics[$name])) {
    trigger_error("Static variable '$name' for class '$class->name' does not exist", E_USER_WARNING);
  }
  else {
  	$static = $statics[$name];
  }
  return $static;
}

/**
 * @abstract True if the module is installed
 * Example:  {"dPfiles"|module_installed}
 * @param string $module The module name
 */
function smarty_modifier_module_installed($module) {
  return CModule::getInstalled($module);
}

/**
 * @abstract True if the module is active
 * Example:  {"dPfiles"|module_active}
 * @param string $module The module name
 */
function smarty_modifier_module_active($module) {
  return CModule::getActive($module);
}

function JSAttribute($string){
	return str_replace(
	  array('\\',   "'",   '"',      "\r",  "\n",  '</'), 
		array('\\\\', "\\'", '&quot;', '\\r', '\\n', '<\/'), 
		$string
	);
}

function smarty_modifier_cleanField($string){
  if (!is_scalar($string)) {
    return $string;
  }

  return htmlspecialchars($string, ENT_QUOTES);
}

function smarty_modifier_stripslashes($string){
  return stripslashes($string);
}

/**
 * @abstract Emphasize a text, putting <em> nodes around found tokens
 *
 * Example:  {$text|emphasize:$tokens}
 * @param string $text The text subject
 * @param array|string $tokens The string tokens to emphasize, space seperated if string
 */
function smarty_modifier_emphasize($text, $tokens, $tag = "em") {
  if (!is_array($tokens)) {
    $tokens = explode(" ", $tokens);
  }
  CMbArray::removeValue("", $tokens);
  
  if (count($tokens) == 0) {
    return $text;
  }

  foreach ($tokens as &$token) {
    $token = preg_quote($token);
    $token = CMbString::allowDiacriticsInRegexp($token);
  }

  $regexp = str_replace("/", "\\/", implode("|", $tokens));
  return preg_replace("/($regexp)/i", "<$tag>$1</$tag>", $text);	
}

/**
 * A ternary operator
 * @todo Use this instead of mb_ternary
 * @param object $value The condition
 * @param object $option1 the value if the condition evaluates to true
 * @param object $option2 the value if the condition evaluates to false
 * @return object $option1 or $option2
 */
function smarty_modifier_ternary($value, $option1, $option2) {
  return $value ? $option1 : $option2;
}

/**
 * Trace modifier
 * @param object $value The condition
 * @return void
 */
function smarty_modifier_trace($value) {
  mbTrace($value);
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
  if (CAppUI::conf("readonly")) {
    $params["readonly"] = 1;
  }
  
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
    $object = new $class;
  }
  
  $field   = CMbArray::extract($params, "field" , null, true);
  $prop    = CMbArray::extract($params, "prop");
  $canNull = CMbArray::extract($params, "canNull");
  
  if (null !== $value = CMbArray::extract($params, "value")) {
    $object->$field = $value;
  }
    
  // Get spec, may create it
  $spec = $prop !== null ? 
    CMbFieldSpecFact::getSpec($object, $field, $prop) : 
    $object->_specs[$field];
  
  if ($canNull === "true" || $canNull === true) {
    $spec->notNull = 0;
    $tabSpec = explode(" ",$spec->prop);
    CMbArray::removeValue("notNull", $tabSpec);
    $spec->prop = implode(" ", $tabSpec);
  }
  
  if ($canNull === "false" || $canNull === false) {
    $spec->notNull = 1;
    $spec->prop = "canNull notNull $spec->prop";
  }

  return $spec->getFormElement($object, $params);
}

/**
 * @param array params tableau des parametres
 * Cette fonction prend les m�mes param�tres que smarty_function_mb_field, mais seul object est requis.
 */
function smarty_function_mb_key($params, &$smarty) {
	$params['field'] = $params["object"]->_spec->key;
	$params['prop'] = 'ref';
	$params['hidden'] = true;
  return smarty_function_mb_field($params, $smarty);
}

/**
 * @param array params tableau des parametres
 * Cette fonction prend les m�mes param�tres que smarty_function_mb_field, mais seul object est requis.
 */
function smarty_function_mb_class($params, &$smarty) {
  if (null == $object = CMbArray::extract($params, "object")) {
    $class = CMbArray::extract($params, "class" , null, true);
  } else {
    $class = $object->_class;
  }
  
  return "<input type=\"hidden\" name=\"@class\" value=\"$class\" />";
}

/**
 * Show a value if different from previous cached one
 * @param array params Smarty parameters
 * - name  : Name of the cached value
 * - value : Value to show, empty string to clear out cache
 */
function smarty_function_mb_ditto($params, &$smarty) {
  static $cache = array();
  $name   = CMbArray::extract($params, "name",  null, true);
  $value  = CMbArray::extract($params, "value", null, true);
  $reset  = CMbArray::extract($params, "reset", false, false);
  $old = '';
  if (!$reset) {
    $old = CMbArray::get($cache, $name, "");
  }
  $cache[$name] = $value;
  return $old != $value ? $value : "|";
}

/**
 * Fonction that return the value of an object field
 */
function smarty_function_mb_value($params, &$smarty) {
  if (empty($params["field"])) return $params["object"]->_view;
	$field = $params["field"];
	$object = $params["object"];
	$spec = $params["object"]->_specs[$field];
	
	if (null !== $value = CMbArray::extract($params, "value")) {
		
		$object->$field = $value;
		
		// Empties cache for forward references
		if (isset($object->_fwd[$field])) {
      unset($object->_fwd[$field]);	
		}
	}
  return $spec->getValue($object, $smarty, $params);
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
  
  $field = CMbArray::extract($params, "field" , null, true);
  
  if (!array_key_exists($field, $object->_specs)) {
     $object->_specs[$field] = CMbFieldSpecFact::getSpec($object, $field, "");
     trigger_error("Spec missing for class '$object->_class' field '$field'", E_USER_WARNING);
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
  $test  = CMbArray::extract($params, "test"  , null, true);
  $value = CMbArray::extract($params, "value" , null, true);
  $other = CMbArray::extract($params, "other" , null, true);
  
  $result =  $test ? $value : $other;
  
  if ($var = CMbArray::extract($params, "var", null)) {
    $smarty->assign($var, $result);
  }
  else {
    return $result;
  }
}

function smarty_function_mb_colonne($params, &$smarty) {
  $class         = CMbArray::extract($params, "class"        , null, true);
  $field         = CMbArray::extract($params, "field"        , null, true);
  $order_col     = CMbArray::extract($params, "order_col"    , null, true);
  $order_way     = CMbArray::extract($params, "order_way"    , null, true);
  $order_suffixe = CMbArray::extract($params, "order_suffixe", ""  , false);
  $url           = CMbArray::extract($params, "url"          , null, false);
  $function      = CMbArray::extract($params, "function"     , null, false);
  
  $sHtml  = "<label for=\"$field\" title=\"".CAppUI::tr("$class-$field-desc")."\">";
  $sHtml .= CAppUI::tr("$class-$field-court");
  $sHtml .= "</label>";
    
  $css_class = ($order_col == $field) ? "sorted" : "sortable";
  $order_way_inv = ($order_way == "ASC") ? "DESC" : "ASC";
  
  if($url){
	  if($css_class == "sorted"){
	  	return "<a class='$css_class $order_way' href='$url&amp;order_col$order_suffixe=$order_col&amp;order_way$order_suffixe=$order_way_inv'>$sHtml</a>";
	  }
	  if($css_class == "sortable"){
	  	return "<a class='$css_class' href='$url&amp;order_col$order_suffixe=$field&amp;order_way$order_suffixe=ASC'>$sHtml</a>";
	  }
  }
  
  if($function){
    if($css_class == "sorted"){
	  	return "<a class='$css_class $order_way' onclick=$function('$order_col','$order_way_inv');>$sHtml</a>";
	  }
	  if($css_class == "sortable"){
	  	return "<a class='$css_class' onclick=$function('$field','ASC');>$sHtml</a>";
	  }    
  }
}

/**
 * Javascript HTML inclusion
 * @param array params 
 * - path   : Direct script file path with extension
 * - script : Script name, without extension, supersedes 'path' and depends on 'module'
 * - module : Module name to find script, if not provided, use global includes
 * @return HTML script node
 */
function smarty_function_mb_script($params, &$smarty) {
  // Path provided
  $extraPath = "";
  $path = CMbArray::extract($params, "path");
  $ajax = CMbArray::extract($params, "ajax");
	
	// Script name providied
  if ($script = CMbArray::extract($params, "script")) {
    $module = CMbArray::extract($params, "module");
  	
    if(CMbArray::extract($params, "mobile")){
      $extraPath = "mobile/";
    }
    $dir = $module ? $extraPath."modules/$module/javascript" : "includes/javascript";
  	$path = "$dir/$script.js";
  }
	
	// Render HTML with build version
  if ($ajax && !empty($smarty->_tpl_vars["ajax"])) {
    $script = file_get_contents($path);
    return "<script type=\"text/javascript\">$script</script>";
  }
  else {
    global $version;
    $version_build = $version['build'];
    return "<script type=\"text/javascript\" src=\"$path?build=$version_build\"></script>";
  }
}

/**
 * Module/Style aware include alternative
 * @param array params 
 * - module    : Module where template is located 
 * - style     : Style where template is located
 * - $template : Template name (no extension)
 * @return void
 */
function smarty_function_mb_include($params, &$smarty) {
  $template = CMbArray::extract($params, "template");
  
  // Module pr�cis�
  if ($module = CMbArray::extract($params, "module")) {
  	$template = "../../$module/templates/$template";
  }

  // Style pr�cis�
  if ($style = CMbArray::extract($params, "style")) {
    $template = "../../../style/$style/templates/$template";
  }

  $path = "$template.tpl";
  
	$tpl_vars = $smarty->_tpl_vars;
	$smarty->_smarty_include(array(
	  'smarty_include_tpl_file' => $path,
	  'smarty_include_vars' => $params
	));
	$smarty->_tpl_vars = $tpl_vars;
}

/**
 * Assign a template var to default value if undefined
 * @param array params 
 * - name  : Name of the var
 * - value : Default value of the var
 * @return void
 */
function smarty_function_mb_default($params, &$smarty) {
  $var   = CMbArray::extract($params, "var"  , true);
  $value = CMbArray::extract($params, "value", true);
	
	if (!isset($smarty->_tpl_vars[$var])) {
		$smarty->assign($var, $value);
	}
}

/**
 * Assigns a unique id to a variable
 * @param array params 
 * - var: Name of the var
 * @return void
 */
function smarty_function_unique_id($params, &$smarty) {
  $var = CMbArray::extract($params, "var", null, true);
  // The dot is removed to get valide CSS ID identifiers
  $smarty->assign($var, str_replace(".", "", uniqid("", true)));
}

/**
 * dotProject integration of Smarty engine main class
 *
 * Provides an extension of smarty class with directory initialization
 * integrated to dotProject framework as well as standard data assignment
 */
class CSmartyDP extends Smarty {
	static $extraPath = "";
	
  /**
   * Construction
   *
   * Directories initialisation
   * Standard data assignment
   */
  function CSmartyDP($dir = null) {
    global $version, $can, $m, $a, $tab, $g, $action, $actionType, $dialog, $ajax, $suppressHeaders, $uistyle;
    
    $rootDir = CAppUI::conf("root_dir");
    $extraPath = self::$extraPath;

    $root = $extraPath ? "$rootDir/$extraPath" : $rootDir;

    $tmpDir = "$rootDir/tmp";
    
    if (!$dir) {
      $dir = "$root/modules/$m"; 
      $this->compile_dir = "$tmpDir/templates_c/{$extraPath}modules/$m/";
    }
    else {
      $this->compile_dir = "$tmpDir/templates_c/{$extraPath}$dir/";
    }
    
    // Directories initialisation
    $this->template_dir = "$dir/templates/";
    
    // Check if the cache dir is writeable
    if (!is_dir($this->compile_dir)) {
      CMbPath::forceDir($this->compile_dir);
    }

    // Delimiter definition
    $this->left_delimiter = "{{";
    $this->right_delimiter = "}}";
    
    // Default modifier for security reason
    $this->default_modifiers = array("@cleanField");
    
    // Register mediboard functions
    $this->register_block   ("tr"                , "do_translation"); 
    $this->register_block   ("main"              , "script_main"); 
    $this->register_block   ("mb_form"           , "mb_form"); 
    $this->register_block   ("vertical"          , "smarty_vertical"); 
    $this->register_function("thumb"             , "thumb");
    $this->register_function("unique_id"         , "smarty_function_unique_id");
    $this->register_function("mb_default"        , "smarty_function_mb_default");
    $this->register_function("mb_ditto"          , "smarty_function_mb_ditto");
    $this->register_function("mb_field"          , "smarty_function_mb_field");
    $this->register_function("mb_class"          , "smarty_function_mb_class");
    $this->register_function("mb_key"            , "smarty_function_mb_key");
    $this->register_function("mb_value"          , "smarty_function_mb_value");
    $this->register_function("mb_label"          , "smarty_function_mb_label");
    $this->register_function("mb_title"          , "smarty_function_mb_title");
    $this->register_function("mb_ternary"        , "smarty_function_mb_ternary");
    $this->register_function("mb_colonne"        , "smarty_function_mb_colonne");
    $this->register_function("mb_include"        , "smarty_function_mb_include");
    $this->register_function("mb_script"         , "smarty_function_mb_script");
    $this->register_modifier("pad"               , "smarty_modifier_pad");
    $this->register_modifier("json"              , "smarty_modifier_json");
    $this->register_modifier("iso_date"          , "smarty_modifier_iso_date");
    $this->register_modifier("iso_time"          , "smarty_modifier_iso_time");
    $this->register_modifier("iso_datetime"      , "smarty_modifier_iso_datetime");
    $this->register_modifier("const"             , "smarty_modifier_const");
    $this->register_modifier("static"            , "smarty_modifier_static");
    $this->register_modifier("cleanField"        , "smarty_modifier_cleanField");
    $this->register_modifier("stripslashes"      , "smarty_modifier_stripslashes");
    $this->register_modifier("emphasize"         , "smarty_modifier_emphasize");
    $this->register_modifier("ternary"           , "smarty_modifier_ternary");
    $this->register_modifier("trace"             , "smarty_modifier_trace");
    $this->register_modifier("currency"          , "smarty_modifier_currency");
    $this->register_modifier("percent"           , "smarty_modifier_percent");
    $this->register_modifier("spancate"          , "smarty_modifier_spancate");
    $this->register_modifier("decabinary"        , "smarty_modifier_decabinary");
    $this->register_modifier("module_installed"  , "smarty_modifier_module_installed");
    $this->register_modifier("module_active"     , "smarty_modifier_module_active");
    $this->register_modifier("JSAttribute"       , "JSAttribute");
    
    $modules = CModule::getActive();
    foreach ($modules as $mod) {
    	$mod->canDo();
    }
    
    // Standard data assignment
    $this->assign("style", $uistyle);
    $this->assign("app", CAppUI::$instance);
    $this->assign("conf", CAppUI::conf());
    $this->assign("user", CAppUI::$instance->user_id); // shouldn't be necessary
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
    $this->assign("modules", $modules);
    $this->assign("base_url", CApp::getBaseUrl());
  }
  
  /**
   * get a concrete filename for automagically created content
   *
   * @param string $auto_base
   * @param string $auto_source
   * @param string $auto_id
   * @return string
   * @staticvar string|null
   * @staticvar string|null
   */
  function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null){
    $_compile_dir_sep =  $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
    $_return = $auto_base . DIRECTORY_SEPARATOR;

    if(isset($auto_id)) {
      // make auto_id safe for directory names
      $auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
      // split into separate directories
      $_return .= $auto_id . $_compile_dir_sep;
    }

    if(isset($auto_source)) {
      // make source name safe for filename
      $_filename = urlencode(basename($auto_source));
      $_crc32 = sprintf('%08X', crc32($auto_source));
      // prepend %% to avoid name conflicts with
      // with $params['auto_id'] names
      $_return .=  "$_filename.%$_crc32%";
    }

    return $_return;
  }
  
  /**
   * Show debug spans
   *
   * @param string $tpl_file
   * @param string $vars
   */
  function showDebugSpans($tpl_file, $params) {
    // The span
	  echo "\n<span class='smarty-include ".(empty($params['ajax']) ? '' : 'ajax')."'>\n$tpl_file";
	  
	  $vars = isset($params["smarty_include_vars"]) ? $params["smarty_include_vars"] : array();
	  
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
    if (!CAppUI::pref("showTemplateSpans") || 
        isset($params["smarty_include_vars"]['nodebug']) ||
        in_array(basename($tpl_file), array("login.tpl", "common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::_smarty_include($params);
      return;
    }
    
    $this->showDebugSpans($tpl_file, $params);
    
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
    if (isset($this->_tpl_vars['nodebug']) ||
        !CAppUI::pref("showTemplateSpans") || 
        in_array(basename($resource_name), array("login.tpl", "common.tpl", "header.tpl", "footer.tpl", "tabbox.tpl", "ajax_errors.tpl"))) {
      parent::display($resource_name, $cache_id, $compile_id);
      return;
    }
    
    $this->showDebugSpans($resource_name, $this->_tpl_vars);
   
    echo "\n<!-- Start display: $resource_name -->\n";
    parent::display($resource_name, $cache_id, $compile_id);
    echo "\n<!-- Stop display: $resource_name -->\n";
  }
}
