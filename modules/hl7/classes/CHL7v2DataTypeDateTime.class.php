<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CHL7v2DataTypeDateTime extends CHL7v2DataType {
  function toMB($value, CHL7v2Field $field){
    $parsed = $this->parseHL7($value, $field);
    
    // empty value
    if ($parsed === "") {
      return "";
    }
    
    // invalid value
    if ($parsed === false) {
      return;
    }

    $datetime = $parsed["year"].
                "-".CValue::read($parsed, "month",  "00").
                "-".CValue::read($parsed, "day",    "00");

    if (isset($parsed["hour"])) {
      $datetime .= " ".CValue::read($parsed, "hour",   "00").
                   ":".CValue::read($parsed, "minute", "00").
                   ":".CValue::read($parsed, "second", "00");
    }

    return $datetime;
  }
  
  function toHL7($value, CHL7v2Field $field) {
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