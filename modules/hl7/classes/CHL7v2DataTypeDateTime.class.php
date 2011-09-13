<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeDateTime extends CHL7v2DataType {
  function toMB($value, CHLv2Field $field){
    $parsed = $this->parseHL7($value, $field);
		
		// empty value
    if ($parsed === "") {
      return "";
    }
    
		// invalid value
    if ($parsed === false) {
      return;
    }
    
    return $parsed["year"].
            "-".CValue::read($parsed, "month",  "00").
            "-".CValue::read($parsed, "day",    "00").
            " ".CValue::read($parsed, "hour",   "00").
            ":".CValue::read($parsed, "minute", "00").
            ":".CValue::read($parsed, "second", "00");
  }
  
  function toHL7($value, CHLv2Field $field) {
    $parsed = $this->parseMB($value, $field);
    
    // empty value
    if ($parsed === "") {
      return "";
    }
    
    // invalid value
    if ($parsed === false) {
      return;
    }
    
    return  CValue::read($parsed, "year").
           (CValue::read($parsed, "month") === "00" ? "" : CValue::read($parsed, "month")).
           (CValue::read($parsed, "day")   === "00" ? "" : CValue::read($parsed, "day")).
            CValue::read($parsed, "hour").
            CValue::read($parsed, "minute").
            CValue::read($parsed, "second");
  }
}