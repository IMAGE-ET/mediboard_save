<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkAdmin();

$bloc_id = CValue::getOrSession("bloc_id");
$salle_id = CValue::getOrSession("salle_id");

// R�cup�ration des blocs de l'etablissement
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// R�cup�ration du bloc � modifier
$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsNotes();

// R�cup�ration de la salle � ajouter/editer
$salle = new CSalle();
$salle->load($salle_id);
$salle->loadRefsNotes();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("bloc",       $bloc);
$smarty->assign("salle",      $salle);

$smarty->display("vw_idx_blocs.tpl");

?>