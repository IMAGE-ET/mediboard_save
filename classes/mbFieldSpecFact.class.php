<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


/**
 * @abstract Fabrique de CFieldSpec en fonction des sp�cifications de propri�t�s
 */
class CMbFieldSpecFact {
  
  static $classes = array(
    "enum"         => "CEnumSpec",
    "str"          => "CStrSpec",
    "text"         => "CTextSpec",
    "num"          => "CNumSpec",
    "float"        => "CFloatSpec",
    "date"         => "CDateSpec",
    "time"         => "CTimeSpec",
    "dateTime"     => "CDateTimeSpec",
    "bool"         => "CBoolSpec",
    "code"         => "CCodeSpec",
    "pct"          => "CPctSpec",
    "birthDate"    => "CBirthDateSpec",
    "ref"          => "CRefSpec",
    "numchar"      => "CNumcharSpec",
    "currency"     => "CCurrencySpec",
    "email"        => "CEmailSpec",
    "password"     => "CPasswordSpec",
    "html"         => "CHtmlSpec",
    "xml"          => "CXmlSpec",
    "php"          => "CPhpSpec", /** @todo: Make a sourceCode spec */
    "ipAddress"    => "CIpAddressSpec",
  );
   
  /**
   * Returns a spec object for an objects's field
   * @param $object CMbObject The object
   * @param $fieldName string The field
   * @param $strSpec string The spec as string
   * @return CMbFieldSpec
   */
  static function getSpec(CMbObject $object, $fieldName, $strSpec = null) {
    $className = $object->_class_name;
        
    $specFragments = explode(" ", $strSpec);
    $specClassName = "CMbFieldSpec";
    if ($specName = array_shift($specFragments)) {
	    if (null == $specClassName = CMbArray::get(self::$classes, $specName)) {
	      trigger_error("No spec class name for '$className'::'$fieldName' = '$strSpec'", E_USER_ERROR);
	    }
    }
    
    $specOptions = array();
    foreach ($specFragments as $specFragment) {
      $options = explode("|", $specFragment);
      $specOptions[array_shift($options)] = count($options) ? implode("|", $options) : true;
    }

    return new $specClassName($className, $fieldName, $strSpec, $specOptions);
  }
}

?>