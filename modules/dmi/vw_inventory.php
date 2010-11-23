<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$barcode = CValue::getOrSession("barcode");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("barcode", $barcode);
$smarty->display("vw_inventory.tpl");
