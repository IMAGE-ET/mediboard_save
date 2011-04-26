<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$object = mbGetObjectFromGet("object_class", "object_id", "object_guid");
$identifiers = $object->loadBackRefs("identifiants", "tag ASC, last_update DESC");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("identifiers", $identifiers);

$smarty->display("ajax_tooltip_identifiers.tpl");
?>