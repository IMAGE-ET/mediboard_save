<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

// R�cup�ration des fonctions
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
}

// R�cup�ration du groupe selectionn�
$usergroup = new CGroups;
$usergroup->load(mbGetValueFromGetOrSession("group_id", 0));

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("usergroup", $usergroup);
$smarty->assign("listGroups", $listGroups);

$smarty->display("vw_idx_groups.tpl");

?>