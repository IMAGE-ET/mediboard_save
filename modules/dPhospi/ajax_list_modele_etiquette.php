<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$filter_class = CValue::getOrSession("filter_class");
$modele_etiquette_id = CValue::getOrSession("modele_etiquette_id");

// R�cup�ration de la liste suivant l'object_class
$modele_etiquette = new CModeleEtiquette();
$modele_etiquette->group_id = CGroups::loadCurrent()->_id;

if ($filter_class != "all") {
  $modele_etiquette->object_class = $filter_class;
}

$liste_modele_etiquette = $modele_etiquette->loadMatchingList("nom");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("filter_class"          , $filter_class);
$smarty->assign("modele_etiquette_id"   , $modele_etiquette_id);
$smarty->assign("liste_modele_etiquette", $liste_modele_etiquette);
$smarty->display("inc_list_modele_etiquette.tpl");
