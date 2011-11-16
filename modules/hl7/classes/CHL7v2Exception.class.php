<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Exception extends Exception {  
  const EMPTY_MESSAGE              = 1;
  const INVALID_SEPARATOR          = 3;
  const SEGMENT_INVALID_SYNTAX     = 4;
  const TOO_FEW_SEGMENT_FIELDS     = 6;
  const TOO_MANY_FIELDS            = 7;
  const SPECS_FILE_MISSING         = 8;
  const VERSION_UNKNOWN            = 10;
  const INVALID_DATA_FORMAT        = 11;
  const FIELD_EMPTY                = 12;
  const TOO_MANY_FIELD_ITEMS       = 13;
  const SEGMENT_MISSING            = 14;
  const MSG_CODE_MISSING           = 15;
  const UNKNOWN_AUTHORITY          = 16;
  const UNEXPECTED_DATA_TYPE       = 17;
  const DATA_TOO_LONG              = 18;
  const UNKNOWN_TABLE_ENTRY        = 19;
  const EVENT_UNKNOWN              = 20;
  
  // argument 2 must be named "code" ...
  public function __construct($id, $code = 0) {
    $args = func_get_args();
    $args[0] = "CHL7v2Exception-$id";

    $message = call_user_func_array(array("CAppUI", "tr"), $args);

    parent::__construct($message, $id); 
  }
}
