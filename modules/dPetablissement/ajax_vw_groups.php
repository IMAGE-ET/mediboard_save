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

// R�cup�ration du groupe selectionn�
$group = new CGroups;
$group->load(CValue::getOrSession("group_id"));
$group->loadFunctions();
$group->loadRefsNotes();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group" , $group);

$smarty->display("inc_vw_groups.tpl");
