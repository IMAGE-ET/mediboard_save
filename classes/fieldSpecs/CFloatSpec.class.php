<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Float value
 */
class CFloatSpec extends CMbFieldSpec {
  public $min;
  public $max;
  public $pos;
  public $decimals;

  /**
   * Tells if the float values are equal
   *
   * @param float $value1 Value 1
   * @param float $value2 Value 2
   * @param float $spec   Spec
   *
   * @return bool
   */
  static function equals($value1, $value2, $spec) {
    if ($spec instanceof CCurrencySpec) {
      $precision = isset($spec->precise) ? 5 : 3;
      return round($value1, $precision) == round($value2, $precision);
    }
    
    return round($value1, 2) == round($value2, 2);
  }

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "float";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return 'FLOAT'.($this->pos || ($this->min !== null && $this->min >= 0) ? ' UNSIGNED' : '');
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'min' => 'num',
      'max' => 'num',
      'pos' => 'bool',
      'decimals' => 'num',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    if ($propValue !== null) {
      $decimals = CMbArray::extract($params, "decimals", $this->decimals);
      
      if ($decimals != null) {
        return number_format($propValue, $decimals, ',', ' ');
      }
    }
    
    if ($propValue && $this->mask) {
      $propValue = self::formattedToMasked($propValue, $this->mask, $this->format);
    }
    
    return CMbString::htmlSpecialChars($propValue);
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = CMbFieldSpec::checkNumeric($object->{$this->fieldName}, false);
    if ($propValue === null) {
      return "N'est pas une valeur d�cimale";
    }
    
    // pos
    if ($this->pos && $propValue <= 0) {
      return "Doit avoir une valeur positive";
    }
    
    // min
    if ($this->min) {
      if (!$min = CMbFieldSpec::checkNumeric($this->min, false)) {
        trigger_error("Sp�cification de minimum num�rique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      
      if ($propValue < $min) {
        return "Doit avoir une valeur minimale de $min";
      }
    }
      
    // max
    if ($this->max) {
      $max = CMbFieldSpec::checkNumeric($this->max, false);
      if ($max === null) {
        trigger_error("Sp�cification de maximum num�rique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      
      if ($propValue > $max) {
        return "Doit avoir une valeur maximale de $max";
      }
    }

    return null;
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = self::randomString(CMbFieldSpec::$nums, 2).".".self::randomString(CMbFieldSpec::$nums, 2);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $form         = CMbArray::extract($params, "form");
    $increment    = CMbArray::extract($params, "increment");
    $showPlus     = CMbArray::extract($params, "showPlus");
    $fraction     = CMbArray::extract($params, "fraction");
    $showFraction = CMbArray::extract($params, "showFraction");
    $deferEvent   = CMbArray::extract($params, "deferEvent");
    $bigButtons   = CMbArray::extract($params, "bigButtons");

    $field = CMbString::htmlSpecialChars($this->fieldName);
    
    $min = CMbArray::extract($params, "min");
    if ($min === null) {
      $min = CMbFieldSpec::checkNumeric($this->min, false);
    }
    
    $max = CMbArray::extract($params, "max");
    if ($max === null) {
      $max = CMbFieldSpec::checkNumeric($this->max, false);
    }
    
    $new_value = CMbArray::extract($params, "value");
    if ($new_value !== null) {
      $value = $new_value;
    }
    
    $decimals = CMbArray::extract($params, "decimals", $this->decimals);
    if ($decimals == null) {
      $decimals = isset($this->precise) ? 4 : 2;
    }

    $step = CMbArray::extract($params, "step");
    $step = CMbFieldSpec::checkNumeric($step, false);
    
    CMbArray::defaultValue($params, "size", 4);

    if ($form && $increment) {
      $sHtml  = $this->getFormElementText($object, $params, (($value>=0 && $showPlus)?'+':'').(($value==0&&$showPlus)?'0':$value), $className, "number");
      $sHtml .= '
    <script type="text/javascript">
      Main.add(function(){
        var element = $(document.forms["'.$form.'"]["'.$field.'"]);
        
        if ($(element.form).isReadonly()) return;
        
        element.addSpinner({';
          if ($step)          $sHtml .= "step: $step,";
          if ($decimals)      $sHtml .= "decimals: $decimals,";
          if ($this->pos)     $sHtml .= "min: 0,";
          elseif(isset($min)) $sHtml .= "min: $min,";
          if (isset($max))    $sHtml .= "max: $max,";
          if ($deferEvent)    $sHtml .= "deferEvent: true,";
          if ($bigButtons)    $sHtml .= "bigButtons: true,";
          if ($showPlus)      $sHtml .= "showPlus: true,";
          if ($fraction)      $sHtml .= "fraction: true,";
          if ($showFraction)  $sHtml .= "showFraction: true,";
          $sHtml .= '_:0 // IE rules
        });
      });
    </script>';
    }
    else {
      $sHtml = $this->getFormElementText($object, $params, $value, $className, "number");
    }
    return $sHtml;
  }
}
