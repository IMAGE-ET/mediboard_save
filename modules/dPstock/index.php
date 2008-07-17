<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_idx_order_manager', null, TAB_EDIT);
$module->registerTab('vw_idx_stock_group',   null, TAB_ADMIN);
$module->registerTab('vw_idx_stock_service', null, TAB_READ);
$module->registerTab('vw_idx_delivery',      null, TAB_EDIT);
$module->registerTab('vw_idx_reference',     null, TAB_ADMIN);
$module->registerTab('vw_idx_product',       null, TAB_ADMIN);
$module->registerTab('vw_idx_category',      null, TAB_ADMIN);
$module->registerTab('vw_idx_societe',       null, TAB_ADMIN);
$module->registerTab('vw_idx_report',        null, TAB_READ);
$module->registerTab('vw_traceability',      null, TAB_READ);

?>