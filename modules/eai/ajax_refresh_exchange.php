<?php 
/**
 * Refresh exchange
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

// Chargement de l'�change demand�
$object = new CMbObject();
$exchange = $object->loadFromGuid($exchange_guid);
$exchange->loadRefs(); 
$exchange->loadRefsInteropActor();
$exchange->getObservations();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("object", $exchange);
$smarty->display("inc_exchange.tpl");

?>