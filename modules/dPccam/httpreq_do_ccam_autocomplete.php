<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$keywords = CValue::request("_codes_ccam", "%%");

$codes = array();
$code = new CDatedCodeCCAM();
foreach ($code->findCodes($keywords, $keywords) as $_code) {
  $_code_value = $_code["CODE"];
  $codes[$_code_value] = CDatedCodeCCAM::get($_code_value);
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("keywords", $keywords);
$smarty->assign("codes"   , $codes);
$smarty->assign("nodebug" , true);

$smarty->display("httpreq_do_ccam_autocomplete.tpl");
