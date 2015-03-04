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

// R�cup�ration des fonctions
$groups = CGroups::loadGroups(PERM_READ);
CStoredObject::massLoadFwdRef($groups, "legal_entity_id");

foreach ($groups as $_group) {
  $_group->loadFunctions();
  $_group->loadRefLegalEntity();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("groups"          , $groups);
$smarty->assign("legal_entities"  , $legal_entities);

$smarty->display("vw_idx_groups.tpl");
