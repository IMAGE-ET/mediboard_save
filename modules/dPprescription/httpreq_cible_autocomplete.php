<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$libelle_cible = CValue::post("cible");

// Recherche dans les noms de categories
$category = new CCategoryPrescription();
$where = array();
$where["nom"] = "LIKE '%$libelle_cible%'";

if (CAppUI::conf("dPprescription CCategoryPrescription show_only_cible")) {
  $where["only_cible"] = " = '1'";
}

$cibles["cat"] = $category->loadList($where);

// Recherche dans les noms de classes ATC de niveau 2
$classe_ATC = new CBcbClasseATC();
$cibles["atc"] = $classe_ATC->searchClassesATC($libelle_cible);

// Cr�ation du template
$smarty = new CSmartyDP("modules/dPprescription");
$smarty->assign("libelle_cible", $libelle_cible);
$smarty->assign("cibles", $cibles);
$smarty->assign('nodebug', true);
$smarty->display("inc_cible_autocomplete.tpl");

?>