<?php /* $Id: vw_idx_groups.php,v 1.9 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 1.9 $
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('mediusers', 'groups') );

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
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('usergroup', $usergroup);
$smarty->assign('listGroups', $listGroups);

$smarty->display('vw_idx_groups.tpl');

?>