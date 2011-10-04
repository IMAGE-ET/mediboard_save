<?php /* $Id: xmlSpec.class.php 6043 2009-04-09 08:15:26Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 6043 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("geshi/geshi");

class CPhpSpec extends CMbFieldSpec {
  function getSpecType() {
    return "php";
  }
  
  function getDBSpec() {
    return "MEDIUMTEXT";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    $propValue = (!empty($params['export']) ? var_export($propValue, true) : $propValue);
    
    $geshi = new Geshi($propValue, "php");
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style("max-height: 100%; white-space:normal;");
    
    return utf8_decode($geshi->parse_code());
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = 
'<?php
$file = fopen("welcome.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file)) {
  echo fgets($file). "<br />";
}
fclose($file);
?>';
  }
}

?>