<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// S�jours concern�s
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
if (!$sejour->_id) {
	CAppUI::stepAjax("S�jour inexistant", UI_MSG_ERROR);
}

$rhss = CRHS::getAllRHSsFor($sejour);
foreach($rhss as $_rhs) {
	$_rhs->loadRefSejour();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("rhss", $rhss);
$smarty->display("inc_cotation_rhs.tpl");
?>