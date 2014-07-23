<?php 
/**
 * Details interop receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkRead();

$actor_guid  = CValue::getOrSession("actor_guid");
$actor_class = CValue::getOrSession("actor_class");

// Chargement de l'acteur d'interop�rabilit�
if ($actor_class) {
  $actor = new $actor_class;
  $actor->updateFormFields();
}
else {
  if ($actor_guid) {
    /** @var CInteropActor $actor */
    $actor = CMbObject::loadFromGuid($actor_guid);
    if ($actor->_id) {
      $actor->loadRefGroup();
      $actor->loadRefUser();
      $actor->loadRefObjectConfigs();

      if ($actor instanceof CInteropSender) {
        $actor->countBackRefs("routes");
      }
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("actor" , $actor);
$smarty->display("inc_actor.tpl");