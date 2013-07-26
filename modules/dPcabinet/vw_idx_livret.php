<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

if (!CModule::getActive('bcb')) {
  CAppUI::stepMessage(
    UI_MSG_ERROR,
    "Le module de m�dicament autonome est en cours de developpement.
    Pour �tre utilis�, ce module a pour le moment besoin d'�tre connect� � une base de donn�es de m�dicaments externe"
  );
  return;
}

$lettre = CValue::get("lettre");
$category_id  = CValue::getOrSession("category_id", CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id'));

$listProduits = array();

// Chargement des functions
$function = new CFunctions;
$functions = $function->loadSpecialites(PERM_EDIT);


// Si on est admin ou que $functions contient plus d'un �l�ment,
// on r�cup�re la fonction en session
if (CAppUI::$user->isAdmin() || count($functions) > 1) {
  $function_guid = CValue::getOrSession("function_guid", reset($functions)->_guid);
  
}
else {
  // Sinon, c'est la fonction de l'utilisateur
  $function_guid = "CFunctions-".CAppUI::$user->function_id;
}


$function = CMbObject::loadFromGuid($function_guid);

// Chargement des produits du livret therapeutique
$produits_livret = CBcbProduit::loadRefLivretTherapeutique($function->_guid);

$tabLettre = range('A', 'Z');

// --- Chargement de l'arbre ATC ---
$codeATC     = CValue::get("codeATC");
$classeATC   = new CBcbClasseATC();
$chapitreATC = $codeATC ? $classeATC->getLibelle($codeATC) : ''; // Nom du chapitre selectionn�
$arbreATC    = $classeATC->loadArbre($codeATC); // Chargements des sous chapitres

$categories = array();

if (CModule::getActive("dPstock")) {
  $category = new CProductCategory;
  $categories = $category->loadList(null, "name");
}
 
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listProduits", $listProduits);
$smarty->assign("arbreATC"    , $arbreATC);
$smarty->assign("codeATC"     , $codeATC);
$smarty->assign("chapitreATC" , $chapitreATC);
$smarty->assign("lettre"      , $lettre);
$smarty->assign("produits_livret", $produits_livret);
$smarty->assign("tabLettre"   , $tabLettre);
$smarty->assign("category_id" , $category_id);
$smarty->assign("categories"  , $categories);
$smarty->assign("livret_cabinet", 1);
$smarty->assign('functions'   , $functions);
$smarty->assign("function_guid" , $function_guid);

$smarty->display("../../dPmedicament/templates/vw_idx_livret.tpl");
