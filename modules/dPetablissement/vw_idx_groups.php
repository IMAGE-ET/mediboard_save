<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// R�cup�ration du groupe selectionn�
$group_id = CValue::getOrSession("group_id");

// R�cup�ration des fonctions
$group = new CGroups;
$groups = $group->loadListWithPerms(PERM_READ);
foreach ($groups as $_group) {
  $_group->loadFunctions();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group_id" , $group_id);
$smarty->assign("groups"   , $groups);

$smarty->display("vw_idx_groups.tpl");

?>