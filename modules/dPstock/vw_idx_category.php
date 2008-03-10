<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession('category_id');

// Loads the expected Category
$category = new CProductCategory();
$category->load($category_id);

// Categories list
$list_categories = $category->loadList();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('category',        $category);
$smarty->assign('list_categories', $list_categories);

$smarty->display('vw_idx_category.tpl');

?>