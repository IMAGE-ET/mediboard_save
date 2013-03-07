<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$date = CValue::getOrSession("date", CMbDT::date());
$praticien_id = CValue::getOrSession("praticien_id", CAppUI::$instance->user_id);

// Chargement de la liste des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

// Chargement du praticien selectionn�
$praticien->load($praticien_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("praticiens", $praticiens);
$smarty->assign("date", $date);
$smarty->assign("praticien", $praticien);
$smarty->display("vw_aed_replacement.tpl");
?>