<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
 
CCanDo::checkEdit();

$societe_id = CValue::getOrSession('societe_id');

// Loads the expected Societe
$societe = new CSociete();
$societe->load($societe_id);
$societe->loadRefsBack();

// Loads every reference supplied by this societe
foreach ($societe->_ref_product_references as $key => $value) {
  $value->loadRefsFwd();
}

// Loads every product made by this societe
foreach ($societe->_ref_products as $key => $value) {
  $value->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign('societe', $societe);
$smarty->display('inc_form_societe.tpl');
