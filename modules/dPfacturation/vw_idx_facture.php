<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$factureitem = new CFactureItem();

// R�cup�ration de la liste des factures
$itemFacture = new CFacture;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("factureitem", $factureitem);
$smarty->display("vw_idx_facture.tpl");
?>
