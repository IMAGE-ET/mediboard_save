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
$group = new CGroups;
$group->load(CValue::getOrSession("group_id"));
$group->loadFunctions();
$group->loadRefsNotes();

// R�cup�ration des fonctions
$groups = $group->loadListWithPerms(PERM_READ);
foreach ($groups as $_group) {
  $_group->loadFunctions();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group" , $group);
$smarty->assign("groups", $groups);

$smarty->display("vw_idx_groups.tpl");

?>