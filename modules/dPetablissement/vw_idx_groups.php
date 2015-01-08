<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// r�cup�ration des Entit�s Juridiques
$legal_entity = new CLegalEntity();
$legal_entities = $legal_entity->loadList();

// R�cup�ration du groupe selectionn�
$group_id = CValue::getOrSession("group_id", CGroups::loadCurrent()->_id);

// R�cup�ration des fonctions
$groups = CGroups::loadGroups(PERM_READ);
foreach ($groups as $_group) {
  $_group->loadFunctions();
  $_group->loadRefLegalEntity();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group_id"        , $group_id);
$smarty->assign("groups"          , $groups);
$smarty->assign("legal_entities"  , $legal_entities);

$smarty->display("vw_idx_groups.tpl");
