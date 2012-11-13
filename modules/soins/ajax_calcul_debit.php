<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$line_id      = CValue::get("line_id");
$line_item_id = CValue::get("line_item_id");

$line = new CPrescriptionLineMix();
$line->load($line_id);
$line->calculQuantiteTotal();

$line_item = new CPrescriptionLineMixItem();
$line_item = $line->_ref_lines[$line_item_id];
$line_item->loadRefsFwd();
$line_item->loadRefProduitPrescription();

$line_item->_unite_administration = $line_item->_ref_produit->libelle_unite_presentation;

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("line"     , $line);
$smarty->assign("line_item", $line_item);

$smarty->display("inc_calcul_debit.tpl");
