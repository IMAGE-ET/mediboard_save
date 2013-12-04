<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$code = CValue::get("code");

$cim10 = CCodeCIM10::get($code, CCodeCIM10::FULL);

foreach ($cim10->_exclude as $key => $value) {
  $cim10->_exclude[$key]->loadRefs();
}
foreach ($cim10->_levelsInf as $key => $value) {
  $cim10->_levelsInf[$key]->loadRefs();
}

$up = null;
$i = count($cim10->_levelsSup);
$i--;
if ($i >= 0) {
  $up =& $cim10->_levelsSup[$i];
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('up', $up);
$smarty->assign('cim10', $cim10);

$smarty->display('code_finder.tpl');
$smarty->display('code_finder.tpl');