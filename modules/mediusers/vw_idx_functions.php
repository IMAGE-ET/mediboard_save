<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// R�cup�ration des fonctions
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

foreach($listGroups as $key => $value) {
  $listGroups[$key]->loadRefs();
  foreach($listGroups[$key]->_ref_functions as $key2 => $value2) {
    $listGroups[$key]->_ref_functions[$key2]->loadRefs();
  }
}

// R�cup�ration de la fonction selectionn�e
$userfunction = new CFunctions;
$userfunction->load(mbGetValueFromGetOrSession("function_id", 0));
$userfunction->loadRefsFwd();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("userfunction", $userfunction);
$smarty->assign("listGroups"  , $listGroups  );

$smarty->display("vw_idx_functions.tpl");

?>