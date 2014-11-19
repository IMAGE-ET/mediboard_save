<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


$birthDate = CValue::get("birthDate", "");
$firstName = CValue::get("firstName", "");
$nir       = CValue::get("nir"      , "");
$nirKey    = CValue::get("nirKey"   , "");
$insc      = "";

if ($nir && $nirKey) {
  $firstName = CINSPatient::formatString($firstName);
  $insc      = CINSPatient::calculInsc($nir, $nirKey, $firstName, $birthDate);
}

$smarty = new CSmartyDP();

$smarty->assign("birthDate", $birthDate);
$smarty->assign("firstName", $firstName);
$smarty->assign("nir"      , $nir);
$smarty->assign("nirKey"   , $nirKey);
$smarty->assign("insc"     , $insc);

$smarty->display("ins/inc_test_insc_saisi.tpl");