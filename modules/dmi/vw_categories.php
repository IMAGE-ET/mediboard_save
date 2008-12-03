<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

// R�cup�ration des groups
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

// Chargement de la cat�gorie s�lectionn�e
$category = new CDMICategory;
$category->load(mbGetValueFromGetOrSession("category_id"));

// Chargement des DMI de la categorie
$category->loadRefsDMI();

// Chargement de toutesles cat�goies
foreach ($groups as $_group) {
  $_group->loadRefsDMICategories();
  foreach ($_group->_ref_dmi_categories as $_category) {
    $_category->countRefsDMI();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("category", $category);
$smarty->assign("groups", $groups);

$smarty->display("vw_categories.tpl");
?>