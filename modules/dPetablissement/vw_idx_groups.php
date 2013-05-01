<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// R�cup�ration du groupe selectionn�
$group_id = CValue::getOrSession("group_id");

// R�cup�ration des fonctions
$groups = CGroups::loadGroups(PERM_READ);
foreach ($groups as $_group) {
  $_group->loadFunctions();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group_id", $group_id);
$smarty->assign("groups"  , $groups);

$smarty->display("vw_idx_groups.tpl");
