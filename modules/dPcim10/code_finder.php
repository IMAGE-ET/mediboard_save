<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$code = dPgetParam($_GET, "code");

$cim10 = new CCodeCIM10($code);
$cim10->load();
$cim10->loadRefs();
foreach($cim10->_exclude as $key => $value) {
  $cim10->_exclude[$key]->loadRefs();
}
foreach($cim10->_levelsInf as $key => $value) {
  $cim10->_levelsInf[$key]->loadRefs();
}

$up = null;
$i = count($cim10->_levelsSup);
$i -= 1;
if($i >= 0)
  $up =& $cim10->_levelsSup[$i];

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('up', $up);
$smarty->assign('cim10', $cim10);

$smarty->display('code_finder.tpl');