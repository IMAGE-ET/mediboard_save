<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien M�nager
 */

global $AppUI, $can, $m;

$can->needsRead();

$track = array();
$orderby = 'date_dispensation DESC';
$where['code'] = 'IS NOT NULL';

$product = new CProduct();
$product->loadList();

$list = new CProductDelivery;
$list = $list->loadList($where, $orderby);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('list', $list);

$smarty->display('vw_traceability.tpl');

?>