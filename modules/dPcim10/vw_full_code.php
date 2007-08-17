<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $can;

$can->needsRead();

$lang = mbGetValueFromGetOrSession("lang", CCodeCIM10::LANG_FR);

$code = mbGetValueFromGetOrSession("code", "(A00-B99)");
$cim10 = new CCodeCIM10($code);
$cim10->load($lang);
$cim10->loadRefs();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("lang" , $lang);
$smarty->assign("cim10", $cim10);

$smarty->display("vw_full_code.tpl");

?>