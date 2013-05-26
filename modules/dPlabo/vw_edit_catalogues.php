<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Fonction de l'utilisateur courant
$user = CMediusers::get();
$function_id = $user->function_id;

// Liste des fonctions disponibles
$functions = new CFunctions();
$order = "text";
$functions = $functions->loadListWithPerms(PERM_EDIT, null, $order);

// Chargement du catalogue demand�
$catalogue = new CCatalogueLabo;
$catalogue->load(CValue::getOrSession("catalogue_labo_id"));
if ($catalogue->_id && $catalogue->getPerm(PERM_EDIT)) {
  $catalogue->loadRefs();
  $function_id = $catalogue->function_id;
}
else {
  $catalogue = new CCatalogueLabo;
}

// Chargement de tous les catalogues
$where = array();
$where["pere_id"] = "IS NULL";
$where[] = "function_id IS NULL OR function_id ".CSQLDataSource::prepareIn(array_keys($functions));

$order = "identifiant";
$listCatalogues = $catalogue->loadList($where, $order);

foreach ($listCatalogues as &$_catalogue) {
  $_catalogue->loadRefsDeep();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));

$smarty->assign("listCatalogues", $listCatalogues);
$smarty->assign("catalogue"     , $catalogue     );
$smarty->assign("function_id"   , $function_id   );
$smarty->assign("functions"     , $functions     );

$smarty->display("vw_edit_catalogues.tpl");
