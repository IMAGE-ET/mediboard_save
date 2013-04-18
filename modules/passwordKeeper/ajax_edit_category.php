<?php
/**
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$category_id        = CValue::getOrSession("category_id");
$password_keeper_id = CValue::getOrSession("password_keeper_id");

// R�cup�ration du trousseau
$keeper = new CPasswordKeeper();
$keeper->load($password_keeper_id);
$keeper->loadBackRefs("categories", "category_name");

// R�cup�ration de la cat�gorie et de ses �l�ments
$category = new CPasswordCategory();
$category->load($category_id);
//$category->getPasswords();

$smarty = new CSmartyDP();
$smarty->assign("keeper"  , $keeper);
$smarty->assign("category", $category);
$smarty->display("inc_edit_category.tpl");