<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$exchange_source_guid = CValue::get("exchange_source_guid");
$filename             = CValue::get("filename");

$exchange_source = CMbObject::loadFromGuid($exchange_source_guid);
$data = $exchange_source->getData($filename);

header("Content-Disposition: attachment; filename=".urlencode(basename($filename)));
header("Content-Type: text/plain; charset=".CApp::$encoding);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($data));
echo $data;
