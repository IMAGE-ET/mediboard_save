<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbFieldSpec {
  var $object         = null;
  var $spec           = null;
  var $className      = null; // @TODO: rename to $owner
  var $fieldName      = null; // @TODO: rename to $field
  var $prop           = null;
  
  var $default        = null;
  var $notNull        = null;
  var $confidential   = null;
  var $moreThan       = null;
  var $moreEquals     = null;
  var $sameAs         = null;
  var $notContaining  = null;
  var $notNear        = null;
  var $alphaAndNum    = null;
  var $mask           = null;
  var $format         = null;
  var $autocomplete   = null;
  var $perm           = null; // Used by autocomplete
  var $dependsOn      = null;
  var $helped         = null;
  var $seekable       = null;
  var $show           = null;
  var $reported       = null;
  var $pattern        = null;
  var $derived        = null;

  static $chars  = array();
  static $nums   = array();
  static $months = array();
  static $days   = array();
  static $hours  = array();
  static $mins   = array();
  static $charmap = array(
    '9' => '[0-9]',
    'a' => '[A-Za-z]',
    '*' => '[A-Za-z0-9]',
    'x' => '[A-Fa-f0-9]',
    '~' => '[+-]',
  );

  protected $_defaultLength = null;

  function __construct($className, $field, $prop = null, $aProperties = array()) {
    $this->className = $className;
    $this->fieldName = $field;
    $this->prop      = $prop;

    $aObjProperties = get_object_vars($this);

    foreach($aProperties as $k => $v) {
      if (array_key_exists($k, $aObjProperties)){
        $this->$k = $v;
      } else {
        trigger_error("La sp�cification '$k' trouv�e dans '$className::$field' est inexistante dans la classe '".get_class($this)."'", E_USER_WARNING);
      }
    }

    $this->_defaultLength = 11;
    
    // Helped fields
    if (is_string($this->helped)) {
      $this->helped = explode("|", $this->helped);
    }
    
    $this->checkValues();
  }
  
  function getOptions(){
    return array(
      'default'       => 'str',
      'notNull'       => 'bool',
      'confidential'  => 'bool',
      'moreThan'      => 'field',
      'moreEquals'    => 'field',
      'sameAs'        => 'field',
      'notContaining' => 'field',
      'notNear'       => 'field',
      'alphaAndNum'   => 'bool',
      'mask'          => 'str',
      'format'        => 'str',
      'autocomplete'  => 'bool',
      'aidesaisie'    => 'bool',
      'dependsOn'     => 'field',
      'perm'          => 'str',
      'helped'        => 'bool',
      'seekable'      => 'bool',
      'reported'      => 'bool',
      'pattern'       => 'str',
    );
  }
  
  function __toString(){
    return $this->prop;
  }

  /**
   * Check whether prop has been declared in parent class
   * @return bool true if prop is inherited, false otherwise
   */
  function isInherited() {
    if ($parentClass = get_parent_class($this->className)) {
      if ($parent = @new $parentClass) {
        return isset($parent->_prop[$this->fieldName]);
      }
    }
    
    return false;
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    if ($propValue && $this->mask) {
      $propValue = self::formattedToMasked($propValue, $this->mask, $this->format);
    }
    
    return htmlspecialchars($propValue);
  }

  function checkParams($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;

    // NotNull
    if($this->notNull && !$this->default && ($propValue === null || $propValue === "")){
      return "Ne peut pas avoir une valeur nulle";
    }

    if ($propValue === null || $propValue === ""){
      return null;
    }
    
    // moreThan
    if($field = $this->moreThan){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if ($propValue <= $targetPropValue) {
        return "'$propValue' n'est pas strictement sup�rieur � '$targetPropValue'";
      }
    }

    // moreEquals
    if($field = $this->moreEquals){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if ($propValue < $targetPropValue) {
        return "'$propValue' n'est pas sup�rieur ou �gal � '$targetPropValue'";
      }
    }

    // sameAs
    if($field = $this->sameAs){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if ($propValue !== $targetPropValue) {
        return "Doit �tre identique � '$field->fieldName'";
      }
    }

    // notContaining
    if($field = $this->notContaining){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;
      if (stristr($propValue, $targetPropValue)) {
        return "Ne doit pas contenir '$field->fieldName'";
      }
    }
    
      // notNear
    if($field = $this->notNear){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (levenshtein($propValue, $targetPropValue) < 2) {
        return "Le mot de passe ressemble trop � '$field->fieldName'";
      }
    }

    // alphaAndNum
    if($this->alphaAndNum){
      if (!preg_match("/[a-z]/", $propValue) || !preg_match("/\d+/", $propValue)) {
        return 'Doit contenir au moins un chiffre ET une lettre';
      }
    }
    
    // input mask
    if ($this->mask) {
      $regex = self::maskToRegex($this->mask);
      $formatted = self::maskedToFormatted($propValue, $this->mask, $this->format);
      $masked = self::formattedToMasked($propValue, $this->mask, $this->format);

      if (!preg_match($regex, $propValue)) {
        if (!preg_match($regex, $masked)) {
          return "La donn�e '$propValue' ne respecte pas le masque '$this->mask'";
        } // else, that means the value is already the formatted value
      } else {
        $object->{$this->fieldName} = $formatted;
      }
    }

    // pattern
    if($this->pattern){
      if (!preg_match('/^'.preg_quote($this->pattern).'$/', $propValue)) {
        return 'Ne correspond pas au format attendu';
      }
    }

    return null;
  }

 /**
  *  99/99/9999 >> 
    array  (
      array('[0-9]', 2),
      '/',
      array('[0-9]', 2),
      '/',
      array('[0-9]', 4)
    )
  * 
  */
  static function maskToLexemes($mask) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $lexemes = array();
    $prevChar = null;
    $count = 0;

    for ($i = 0; $i <= strlen($mask); $i++) {
      $c = (isset($mask[$i]) ? $mask[$i] : null); // To manage the latest char

      if (!isset(self::$charmap[$c])) {
        if (isset(self::$charmap[$prevChar])) {
          $lexemes[] = array(self::$charmap[$prevChar], $count);
        }
        if($c !== null) {
          $lexemes[] = $c;
        }
        $prevChar = $c;
        $count = 0;
      }
      else if ($prevChar !== $c) {
        if (isset(self::$charmap[$prevChar])) {
          $lexemes[] = array(self::$charmap[$prevChar], $count);
          $prevChar = $c;
          $count = 0;
        }
        else {
          $prevChar = $c;
          $count++;
        }
      }
      else if ($prevChar === $c) {
        $count++;
      }
    }
    
    return $lexemes;
  }
  
 /**
  *  99/99/9999 >> /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/ 
  */
  static function maskToRegex($mask) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $regex = '/^';
    $lexemes = self::maskToLexemes($mask);
    
    foreach ($lexemes as $lex) {
      $regex .= is_array($lex) ? 
                  ('('.$lex[0].'{'.max(1, $lex[1]).'})') : 
                  (preg_match('`[A-Za-z0-9]`', $lex) ? '' : '\\').$lex;
    }
    $regex .= '$/';
    
    return $regex;
  }
  
 /** Removes the mask from the string 
  *   Example : 06-85-98-45-26  >> 0685984526
  *   Or        31/10/1985      >> 1985-10-31 with the format $3-$2-$1
  */
  static function maskedToFormatted($data, $mask, $format = null) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $formatted = '';
    
    // If no format is provided, this is the raw value
    if (!$format) {
      // Could be shorter, using str_replace
      for ($i = 0; $i < strlen($mask); $i++) {
        if (isset(self::$charmap[$mask[$i]]) && isset($data[$i])) {
          $formatted .= $data[$i];
        }
      }
    } 
    // else, we match the data to the format
    else {
      $regex = self::maskToRegex($mask);
      $formatted = $format;
      
      $matches = array();
      preg_match($regex, $data, $matches);
      for ($i = 1; ($i < count($matches) && $i < 10); $i++) {
        $formatted = str_replace('$'.$i, $matches[$i], $formatted);
      }
    }
    
    return $formatted;
  }

 /** Applies the mask to the string 
  *   Example : 0685984526 >> 06-85-98-45-26
  *   Or        1985-10-31 >> 31/10/1985 with the format $3-$2-$1
  */
  static function formattedToMasked($rawdata, $mask, $format = null) {
    $mask = str_replace(array('S', 'P'), array(' ', '|'), $mask);
    $masked = '';
    
    if (!$format) {
      $n = 0;
      for ($i = 0; $i < strlen($mask); $i++) {
        $masked .= isset(self::$charmap[$mask[$i]]) && isset($rawdata[$n]) ? 
                     $rawdata[$n++] :
                     $mask[$i];
      }
    } 
    else {
      $lexemes = self::maskToLexemes($mask);
      $areas = array();
      $placeToLexeme = array(); // Makes the correspondance between the $1, $2, $3... in the format and the lexemes
      
      // We collect only the variable lexemes
      $n = 0;
      for ($i = 0; $i < count($lexemes); $i++) {
        if (is_array($lexemes[$i])) {
          $areas[++$n] = $lexemes[$i];
          $placeToLexeme[$n] = $i;
        }
      }

      $positions = array();
      $formatRegex = "/^$format$/";
      for ($i = 1; $i <= count($areas); $i++) {
        $pos = strpos($formatRegex, '$'.$i);
        $positions[$pos] = $i;
        $formatRegex = str_replace('$'.$i, ('('.$areas[$i][0].'{'.$areas[$i][1].'})'), $formatRegex);
      }
      
      ksort($positions); // sort by key
      $positions = array_values($positions); // to make keys contiguous
    
      $matches = array();
      preg_match($formatRegex, $rawdata, $matches);
      if (count($matches)) {
        foreach ($areas as $key => $area) {
          $lexemes[$placeToLexeme[$key]] = $matches[$positions[$key-1]];
        }
        $masked = implode('', $lexemes);
      }
    }
    
    return $masked;
  }

  function checkTargetPropValue($object, $field){
    $aObjProperties = get_object_vars($object);
    if(!$field || $field === true || !is_scalar($field) || !array_key_exists($field ,$aObjProperties)){
      trigger_error("El�ment cible '$field' invalide ou inexistant dans la classe '".get_class($this)."'", E_USER_WARNING);
      return "Erreur syst�me";
    }
    return null;
  }

  function checkPropertyValue($object){
    $propValue = $object->{$this->fieldName};

    if($msg = $this->checkParams($object)){
      return $msg;
    }

    if ($propValue === null || $propValue === "") {
      return null;
    }

    if($msg = $this->checkProperty($object)){
      return $msg;
    }
  }

  static function randomString($array, $length) {
    $string = "";
    $count = count($array) - 1;
    for($i = 0; $i < $length; $i++)  {
      $string .= $array[rand(0, $count)];
    }
    return $string;
  }

  static function checkNumeric(&$value, $int = true){
    $value = preg_replace(array('/\s/', '/,/'), array('', '.'), $value);
    if (!is_numeric($value)) return null;
    return $value = ($int ? intval($value) : floatval($value));
  }

  static function checkLengthValue($length){
    if(!$length = CMbFieldSpec::checkNumeric($length)){
      return null;
    }
    if ($length < 1 || $length > 255) {
      return null;
    }
    return $length;
  }

  function checkConfidential(&$object){
    if(!$this->confidential || $object->{$this->fieldName} === null){
      return null;
    }

    $this->sample($object);
  }

  function getFormElement($object, $params){
    $hidden    = CMbArray::extract($params, "hidden");
    $className = CMbArray::extract($params, "class");
    if($object->_locked) {
      $params["readonly"] = "readonly";
    }
    $value = $this->mask ? $this->getValue($object) : $object->{$this->fieldName};
    if ($hidden) {
      return $this->getFormHiddenElement($object, $params, $value, $className);
    }
    return $this->getFormHtmlElement($object, $params, $value, $className);
  }

  /**
   * Produit le code HTML pour une label de champ de formulaire
   * pour le champ de la sp�cification
   *
   * @param CMbObject $object Objet concern�
   * @param array $params Extra parameters
   *   - defaultFor : name of a radio option to associate label with
   * @return string Rendered HTML
   */
  function getLabelElement($object, $params = array()) {
    $defaultFor = CMbArray::extract($params, "defaultFor");
    $forName = $defaultFor ? $defaultFor : $this->getLabelForElement($object, $params);
    $className = $object->_specs[$this->fieldName]->notNull ? "checkNull" : "";
    
    $extra  = CMbArray::makeXmlAttributes($params);

    $desc  = CAppUI::tr("$object->_class-$this->fieldName-desc");
    $desc = htmlentities($desc);
    $sHtml  = "<label for=\"$forName\" class=\"$className\" title=\"$desc\" $extra>";
    $sHtml .= CAppUI::tr("$object->_class-$this->fieldName");
    $sHtml .= "</label>";

    return $sHtml;
  }

  /**
   * Produit le code HTML pour un titre de colonne
   * pour le champ de la sp�cification
   *
   * @param CMbObject $object Objet concern�
   * @param array $params Extra parameters
   * @return string Rendered HTML
   */
  function getTitleElement($object, $params) {
    $desc  = CAppUI::tr("$object->_class-$this->fieldName-desc");
    $desc = htmlentities($desc);
    $title = CAppUI::tr("$object->_class-$this->fieldName-court");
    return "<label title=\"$desc\" >$title</label>";
  }

  function getLabelForElement($object, &$params){
    return $this->fieldName;
  }

  function getFormHiddenElement($object, $params, $value, $className) {
    $field = $this->fieldName;
    $form = CMbArray::extract($params, "form"); // needs to be extracted
    $extra = CMbArray::makeXmlAttributes($params);
    $sHtml = "<input type=\"hidden\" name=\"".htmlspecialchars($field)."\" value=\"".htmlspecialchars($value)."\"";
    if($this->prop){
      $sHtml .= " class=\"".htmlspecialchars($this->prop)."\"";
    }
    $sHtml  .= " $extra/>";

    return $sHtml;
  }

  function getFormElementText($object, $params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $protected    = $value && $object->_id && isset($this->protected) && $this->protected;
    
    if ($protected) {
      $params["readonly"] = "readonly";
    }
    
    $autocomplete = CMbArray::extract($params, "autocomplete", "true,2,30,false,false");
    $form         = CMbArray::extract($params, "form");
    $multiline    = CMbArray::extract($params, "multiline");
    $extra        = CMbArray::makeXmlAttributes($params);
    $spec         = $object->_specs[$field];
    $ref = false;

    // @todo: use a better way of getting options
    @list($activated, $minChars, $limit, $wholeString, $dropdown) = explode(',', $autocomplete);
    
    if ($this->autocomplete && $form && $activated === 'true') {
      if ($minChars    === null || $minChars    === "")    $minChars = 2;
      if ($limit       === null || $limit       === "")       $limit = 30;
      if ($wholeString === null || $wholeString === "") $wholeString = false;
      if ($dropdown    === null || $dropdown    === "" || $dropdown === "false") $dropdown = false;
      
      $options = explode('|', $this->autocomplete);
      $view_field = reset($options);
      $show_view = isset($options[1]);
      
      if ($spec instanceof CRefSpec && $this->autocomplete) {
        $ref_object = new $spec->class;
        $ref_object->load($value);
        $view = $ref_object->$view_field;
        
        $sHtml  = "<input type=\"hidden\" name=\"$field\" value=\"".htmlspecialchars($value)."\" 
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra />";
        $sHtml .= "<input type=\"text\" name=\"{$field}_autocomplete_view\" value=\"".htmlspecialchars($view)."\" 
                    class=\"autocomplete\" onchange='if(!this.value){this.form[\"".$field."\"].value=\"\"}' $extra />";
        $ref = true;
      }
      else {
        $sHtml  = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra />";
      }
      
      $id = $form.'_'.$field.($ref ? '_autocomplete_view' : '');
      $sHtml .= '<script type="text/javascript">
      Main.add(function(){
        var input = $("'.$id.'");
        var url = new Url("system", "httpreq_field_autocomplete");
        url.addParam("class", "'.$object->_class.'");
        url.addParam("field", "'.$field.'");
        url.addParam("limit", '.$limit.');
        url.addParam("view_field", "'.$view_field.'");
        url.addParam("show_view", '.($show_view ? 'true' : 'false').');
        url.addParam("input_field", "'.$field.($ref ? '_autocomplete_view' : '').'");
        url.addParam("wholeString", '.$wholeString.');
        url.autoComplete(input, "'.$id.'_autocomplete", {
          minChars: '.$minChars.',
          method: "get",
          select: "view",
           dropdown: '.(!$ref || $dropdown ? 'true' : 'false');
      
      if ($ref) {
        $sHtml .= ',
          afterUpdateElement: function(field,selected){
            $V(field.form["'.$field.'"], selected.getAttribute("id").split("-")[2]);
          }';
      }
      if ($this->dependsOn) {
        $wheres = explode("|", $this->dependsOn);
        $sHtml .= ',
          callback: function(element, query){
            var field;';
        
        foreach($wheres as $_where) {
          $sHtml .= "field = input.form.elements[\"".$_where."\"];
          if (field) query += \"&where[$_where]=\" + \$V(field);";
        }
        
        $sHtml .= "  return query;";
        $sHtml .= '}';
      }
      
      $sHtml .= '});});</script>';
      $sHtml .= '<div style="display:none; width:0;" class="autocomplete" id="'.$id.'_autocomplete"></div>';
    }
    else {
      if ($multiline) {
        $has_CR = strpos($value, "\n") !== false;
        
        if ($has_CR) {
          if (!isset($params["style"])) $params["style"] = "";
          $params["style"] .= "width: auto;";
          $extra = CMbArray::makeXmlAttributes($params);
          
          $sHtml = "<textarea name=\"$field\" class=\"".htmlspecialchars("$className $this->prop")." noresize\" $extra>".
                      htmlspecialchars($value).
                   "</textarea>";
        }
        else{
          $sHtml = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                    class=\"".htmlspecialchars("$className $this->prop")."\" $extra/>";
        }
        $sHtml .= '<button type="button" class="'.($has_CR ? "singleline" : "multiline").' notext" tabIndex="10000"
                           onclick="$(this).previous(\'input,textarea\').switchMultiline(this)"></button>';
      }
      else {
        $sHtml = "<input type=\"text\" name=\"$field\" value=\"".htmlspecialchars($value)."\"
                  class=\"".htmlspecialchars("$className $this->prop")."\" $extra/>";
      }
    }
    
    if ($protected) {
      $sHtml .= '<button type="button" onclick="var p=$(this).previous(\'input,textarea\');p.readOnly=!p.readOnly;" class="notext lock" title="'.CAppUI::tr("Unlock").'"></button>';
    }
    
    return $sHtml;
  }

  function getFormElementTextarea($object, &$params, $value, $className){
    $field = htmlspecialchars($this->fieldName);
    $rows  = CMbArray::extract($params, "rows", "3");
    $form  = CMbArray::extract($params, "form"); // needs to be extracted
    $aidesaisie = CMbArray::extract($params, "aidesaisie");
    $extra = CMbArray::makeXmlAttributes($params);
    $sHtml = "<textarea name=\"$field\" rows=\"$rows\" class=\"".htmlspecialchars(trim("$className $this->prop"))."\" $extra>".htmlspecialchars($value)."</textarea>";
    
    if ($form) {
      $params_aidesaisie = array();
      $params_aidesaisie[] = "objectClass: '".get_class($object) . "'";
      $depend_fields = $object->_specs[$field]->helped;
      
      if (!isset($params["dependField1"])) {
        if (isset($depend_fields["0"])) {
          $params_aidesaisie[] = "dependField1: getForm('$form').elements['".$depend_fields["0"]."']";
        }
      }
      if (!isset($params["dependField2"])) {
        if (isset($depend_fields["1"])) {
          $params_aidesaisie[] = "dependField2: getForm('$form').elements['".$depend_fields["1"]."']";
        }
      }
      
      $params_aidesaisie = "{".implode(",", $params_aidesaisie);
      
      if ($aidesaisie) {
        $params_aidesaisie .= ", $aidesaisie";
      }
      $params_aidesaisie .= "}";
      
      $sHtml .=
      "<script type='text/javascript'>
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm('$form').elements['$field'], $params_aidesaisie);
        });
       </script>";
    }
    
    return $sHtml;
  }

  function getFormElementDateTime($object, &$params, $value, $className, $format = "%d/%m/%Y %H:%M") {
    if ($object->_locked) {
      $params["readonly"] = "readonly";
    }

    $class = htmlspecialchars(trim("$className $this->prop"));
    $field = htmlspecialchars($this->fieldName);
    $date  = ($value && $value != '0000-00-00' && 
                        $value != '00:00:00' && 
                        $value != '0000-00-00 00:00:00') ? (($this instanceof CDateSpec && $this->progressive) ? $this->getValue($object, null, $params) : mbTransformTime(null, $value, $format)) : "";
    
    $form     = CMbArray::extract($params, "form");
    $register = CMbArray::extract($params, "register");
    
    // Tab index in display input
    $tabindex = CMbArray::extract($params, "tabindex");
  
    $extra = CMbArray::makeXmlAttributes($params);
    $html = array();
    $html[] = '<input name="'.$field.'_da" type="text" value="'.$date.'" class="'.$class.'" 
                      readonly="readonly" '.(isset($tabindex) ? 'tabindex="'.$tabindex.'" ' : '').'/>';
    $html[] = '<input name="'.$field.'" type="hidden" value="'.$value.'" class="'.$class.'" '.$extra.' />';

    if ($form && ($register || $this instanceof CTimeSpec)) {
      $html[] = '<script type="text/javascript">
        Main.add(function(){Calendar.reg'.($this instanceof CDateSpec && $this->progressive ? 'Progressive' : '').'Field(getForm("'.$form.'").elements["'.$field.'"])})
      </script>';
    }
    
    return implode("\n", $html);
  }

  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }

  function getSpecType() {
    return "mbField";
  }

  /**
   * Check whether property value bound to objects is compliant to the specification
   * @param $object object bound to property
   * @return string Store-like message
   */
  function checkProperty($object) {}

  // Return a sample value.
  // If consistent, the random value stay the same for a given initial value
  function sample(&$object, $consistent = true){
    if($consistent)
      srand(crc32($object->{$this->fieldName}));
  }

  function getDBSpec(){}
  
  static function parseDBSpec($db_spec, $reduce_strings = false) {
    $props = array(
      'type' => null,
      'params' => null,
      'unsigned' => null,
      'zerofill' => null,
    );
    
    $props['type']    = $db_spec;
    $props['unsigned'] = stristr($db_spec, 'unsigned') != false;
    $props['zerofill'] = stristr($db_spec, 'zerofill') != false;
    $props['type'] = trim(str_ireplace(array('unsigned', 'zerofill'), '', $props['type']));
    $props['params']  = null;
    
    if ($pos = strpos($props['type'], '(')) {
      $props['params'] = explode(',', substr($props['type'], $pos+1, strpos($props['type'], ')')-$pos-1));
      $props['params'] = array_map('trim', $props['params']);
      
      if ($reduce_strings) {
        foreach($props['params'] as &$v) {
          if ($v[0] === "'") 
            $v = trim($v, "'");
          else 
            $v = (int)$v;
        }
      }
      
      $props['type']   = substr($props['type'], 0, $pos);
    }
    
    $props['type'] = strtoupper(trim($props['type']));
    
    return $props;
  }
  
  function isTextBlob(){
    return in_array($this->getSpecType(), array("text", "html", "php", "xml", "set"));
  }

  function getFullDBSpec(){
    $object = new $this->className;
    $is_key = $object->_spec->key == $this->fieldName;
    $props = array(
     'type' => $this->getDBSpec(),
     //'unsigned' => $this instanceof CRefSpec ? 'UNSIGNED' : '',
     //'zerofill' => isset($this->zerofill) ? 'ZEROFILL' : '',
     'notnull' => isset($this->notNull) || $is_key ? 'NOT NULL' : '',
     /*'index' => ($this instanceof CRefSpec || 
                 $this instanceof CDateTimeSpec || 
                 $this instanceof CDateSpec || 
                 $this instanceof CTimeSpec ||
                 isset($this->index)) ? "INDEX" : '',*/
     'extra' => $is_key ? 'auto_increment' : null,
    );
    
    // @todo mettre dans dans les classes elles-m�mes
    if (!$this->isTextBlob()) {
      if (isset($this->default) && !($this instanceof CBoolSpec && $this->default === "")) {
        $props['default'] = "DEFAULT '$this->default'";
      }
    }
    
    return implode(' ', $props);
  }

  function checkValues(){}
  
  function trim($value){
    return trim($value);
  }
}

CMbFieldSpec::$chars  = range("a","z");
CMbFieldSpec::$nums   = range(0, 9);
CMbFieldSpec::$months = range(1, 12);
CMbFieldSpec::$days   = range(1, 29);
CMbFieldSpec::$hours  = range(9, 19);
CMbFieldSpec::$mins   = range(0, 60, 10);

?>