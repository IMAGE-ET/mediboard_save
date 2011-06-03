<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$canSante400 = CModule::getCanDo("dPsante400");
$dialog = CValue::get("dialog");

// Chargement du filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::get("object_id"   );
$filter->object_class = CValue::get("object_class");
$filter->tag          = CValue::get("tag"         );
$filter->id400        = CValue::get("id400");
$filter->nullifyEmptyFields();

// Par d�faut, �dition du premier id400
$list = $filter->loadMatchingList("last_update DESC", 30);
$firstId400 = reset($list);

// R�cup�ration de la liste des classes disponibles
$listClasses = CApp::getInstalledClasses();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("filter", $filter);
$smarty->assign("idSante400_id", $firstId400 ? $firstId400->_id : 0);
$smarty->assign("canSante400", $canSante400);
$smarty->assign("dialog", $dialog);
$smarty->assign("listClasses", $listClasses);
$smarty->display("view_identifiants.tpl");

?>