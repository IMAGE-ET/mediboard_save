<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("now", mbDateTime());

$smarty->display("configure.tpl");

?>