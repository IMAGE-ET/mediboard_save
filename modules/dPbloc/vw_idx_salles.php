<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkAdmin();

$salle_id = CValue::getOrSession("salle_id");

// R�cup�ration des salles
$blocs_list = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// R�cup�ration de la salle � ajouter/editer
$salle = new CSalle;
$salle->load($salle_id);
$salle->loadRefsNotes();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("blocs_list", $blocs_list);
$smarty->assign("salle",      $salle);

$smarty->display("vw_idx_salles.tpl");

?>