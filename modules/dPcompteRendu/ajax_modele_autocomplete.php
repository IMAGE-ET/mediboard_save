<?php

/**
 * Autocomplete des mod�les
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$user_id      = CValue::get("user_id");
$object_class = CValue::get("object_class");
$keywords     = CValue::post("keywords_modele");
$fast_edit    = CValue::get("fast_edit", 1);

$user = new CMediusers();
$user->load($user_id);
$user->loadRefFunction();

$compte_rendu = new CCompteRendu;
$modeles      = array();
$order        = "nom";

$where = array();

if (!$fast_edit) {
  $where["fast_edit"] = " = '0'";
  $where["fast_edit_pdf"] = " = '0'";
}

$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";
$where["user_id"] = " = '".CAppUI::$user->_id."'";
if ($user->canEdit()) {
  $where["user_id"] = "IN ('$user->_id', '".CAppUI::$user->_id."')";
}
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

unset($where["user_id"]);

$where["type"] = "= 'body'";

// Inclusion des fonctions secondaires de l'utilisateur connect�
// et de l'utilisateur concern�

$sec_function = new CSecondaryFunction();
$whereSecFunc = array();
$whereSecFunc["user_id"] = " = '".CAppUI::$user->_id."'";
if ($user->canEdit()) {
  $whereSecFunc["user_id"] = "IN ('$user->_id', '".CAppUI::$user->_id."')";
}
$sec_functions = $sec_function->loadList($whereSecFunc);

$functions_ids = CMbArray::pluck($sec_functions, "function_id");

$functions_ids = array_merge($functions_ids, array($user->function_id, CAppUI::$user->function_id));
$where["function_id"] = CSQLDataSource::prepareIn($functions_ids);
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

unset($where["function_id"]);

$where["type"] = "= 'body'";
$where["group_id"] = " = '$user->_group_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, null, null, null, $order));

$modeles = CModelObject::naturalSort($modeles, array("nom"), true);

$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("nodebug", true);
$smarty->assign("keywords", $keywords);
$smarty->display("inc_modele_autocomplete.tpl");