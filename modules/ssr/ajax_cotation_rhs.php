<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// S�jour concern�s
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
if (!$sejour->_id) {
  CAppUI::stepAjax("S�jour inexistant", UI_MSG_ERROR);
}

if ($sejour->type != "ssr") {
  CAppUI::stepAjax("Le s�jour s�lectionn� n'est pas un s�jour de type SSR (%s)", UI_MSG_ERROR, $sejour->type);
}

// Chargment du bilan
$bilan = $sejour->loadRefBilanSSR();

// Liste des RHSs du s�jour
$_rhs = new CRHS();
$rhss = CRHS::getAllRHSsFor($sejour);
foreach ($rhss as $_rhs) {
  $_rhs->loadRefSejour();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"        , $sejour);
$smarty->assign("bilan"         , $bilan);
$smarty->assign("rhss"          , $rhss);

$smarty->display("inc_cotation_rhs.tpl");

?>