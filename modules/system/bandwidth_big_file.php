<?php /* $Id: about.php 10973 2010-12-27 21:51:09Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10973 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::check();

// Size in KB
$size = CValue::get("size", 100);
$size = min($size, 10*1024); // Cap it to 10MB MAX

$big_file = CAppUI::getTmpPath("bandwidth_test/big.bin");
CMbPath::forceDir(dirname($big_file));
file_put_contents($big_file, str_pad("", 1024*$size, "a")); // Must be a "normal" char so that it's not url encoded

$empty_file = CAppUI::getTmpPath("bandwidth_test/empty.bin");
file_put_contents($empty_file, "");
