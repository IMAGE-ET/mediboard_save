<?php
/**
 * Edit discipline
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$discipline_id = CValue::getOrSession("discipline_id");

// R�cup�ration des groups
$groups = CGroups::loadGroups(PERM_EDIT);

// R�cup�ration de la fonction selectionn�e
$discipline = new CDiscipline();
$discipline->load($discipline_id);
$discipline->loadGroupRefsBack();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("discipline", $discipline);
$smarty->assign("groups"  , $groups);

$smarty->display("inc_edit_discipline.tpl");