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

$modele_etiquette_id = CValue::getOrSession("modele_etiquette_id");

$modele_etiquette = new CModeleEtiquette();
$group_id = CGroups::loadCurrent()->_id;

if ($modele_etiquette_id) {
  $modele_etiquette->load($modele_etiquette_id);
  $modele_etiquette->loadRefsNotes();
}

// Nouveau mod�le d'�tiquette dans le cas d'un changement d'�tablissement
if (!$modele_etiquette_id || $modele_etiquette->group_id != $group_id) {
  // Chargement des valeurs par d�faut si pas de modele_etiquette_id
  $modele_etiquette = new CModeleEtiquette();
  $modele_etiquette->valueDefaults();
  $modele_etiquette->group_id = $group_id;
}

$classes = CCompteRendu::getTemplatedClasses();
$classes["CRPU"] = CAppUI::tr("CRPU");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("modele_etiquette" , $modele_etiquette);
$smarty->assign("classes"          , $classes);
$smarty->assign("fields"           , CModeleEtiquette::$fields);
$smarty->assign("listfonts"        , CModeleEtiquette::$listfonts);

$smarty->display("inc_edit_modele_etiquette.tpl");
