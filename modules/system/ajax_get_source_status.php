<?php 
/**
 * Status exchange source
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// D�verrouiller la session pour rendre possible les requ�tes concurrentes.
session_write_close();

$source_guid = CValue::get("source_guid");

$status = null;

$source = CMbObject::loadFromGuid($source_guid);

$source->isReachable();
$source->getResponseTime();

$status = array ("reachable"     => $source->_reachable,
                 "message"       => utf8_encode($source->_message),
                 "name"          => $source->name,
                 "response_time" => $source->_response_time);

echo json_encode($status);

?>