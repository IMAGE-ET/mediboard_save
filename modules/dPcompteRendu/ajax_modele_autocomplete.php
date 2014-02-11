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

$user = CMediusers::get($user_id);
$user->loadRefFunction();

$curr_user = CMediusers::get();

$compte_rendu = new CCompteRendu();
$modeles      = array();

$where = array();
if (!$fast_edit) {
  $where["fast_edit"] = " = '0'";
  $where["fast_edit_pdf"] = " = '0'";
}

$where["object_class"] = "= '$object_class'";
$where["type"] = "= 'body'";

// Niveau utilisateur
$where["user_id"] = " = '$curr_user->_id'";
if ($user->canEdit()) {
  $where["user_id"] = "IN ('$user->_id', '$curr_user->_id')";
}
$modeles = $compte_rendu->seek($keywords, $where, 100, false, null, "nom");

// Niveau fonction
// Inclusion des fonctions secondaires de l'utilisateur connect�
// et de l'utilisateur concern�
unset($where["user_id"]);
$sec_function = new CSecondaryFunction();
$whereSecFunc = array();
$whereSecFunc["user_id"] = " = '$curr_user->_id'";
if ($user->canEdit()) {
  $whereSecFunc["user_id"] = "IN ('$user->_id', '$curr_user->_id')";
}

$function_sec = $sec_function->loadList($whereSecFunc);
$function_ids = array_merge(CMbArray::pluck($function_sec, "function_id"), array($user->function_id, $curr_user->function_id));
$where["function_id"] = CSQLDataSource::prepareIn($function_ids);
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, 100, false, null, "nom"));

// Niveau �tablissement
unset($where["function_id"]);
$where["group_id"] = " = '$user->_group_id'";
$modeles = array_merge($modeles, $compte_rendu->seek($keywords, $where, 100, false, null, "nom"));

$modeles = CModelObject::naturalSort($modeles, array("nom"), true);

$smarty = new CSmartyDP();

$smarty->assign("modeles" , $modeles);
$smarty->assign("nodebug" , true);
$smarty->assign("keywords", $keywords);

$smarty->display("inc_modele_autocomplete.tpl");