<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

// R�cup�ration des fonctions
$group = new CGroups;
$listGroups = $group->loadListWithPerms(PERM_READ);

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
}

// R�cup�ration du groupe selectionn�
$usergroup = new CGroups;
$usergroup->load(CValue::getOrSession("group_id", 0));
$usergroup->loadRefs();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("usergroup"   , $usergroup);
$smarty->assign("listGroups"  , $listGroups);

$smarty->display("vw_idx_groups.tpl");

?>