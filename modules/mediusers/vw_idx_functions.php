<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("mediusers"      , "functions"));
require_once($AppUI->getModuleClass("dPetablissement", "groups"   ));

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
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("userfunction", $userfunction);
$smarty->assign("listGroups"  , $listGroups  );

$smarty->display("vw_idx_functions.tpl");

?>