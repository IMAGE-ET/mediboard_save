<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author St�phanie Subilia
 */

global $can, $g;
$can->needsRead();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("vw_elements.tpl");


?>