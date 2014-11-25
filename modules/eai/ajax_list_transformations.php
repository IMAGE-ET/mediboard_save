<?php
/**
 * Link transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$event_name        = CValue::getOrSession("event_name");
$transformation_id = CValue::getOrSession("transformation_id");
$actor_guid        = CValue::getOrSession("actor_guid");

/** @var CInteropActor $actor */
$actor = CMbObject::loadFromGuid($actor_guid);

$event = new $event_name;

// Chargement des transformations peut-�tre existantes pour cet �v�nement
$transformation = new CEAITransformation();
$transformation->bindObject($event, $actor);

$transformations = $transformation->loadMatchingList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("actor"          , $actor);
$smarty->assign("event"          , $event);
$smarty->assign("transformations", $transformations);

$smarty->display("inc_list_transformations.tpl");