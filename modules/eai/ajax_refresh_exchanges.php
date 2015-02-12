<?php 
/**
 * Refresh exchanges
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_class    = CValue::getOrSession("exchange_class");
$exchange_type     = CValue::getOrSession("exchange_type");
$exchange_group_id = CValue::getOrSession("exchange_group_id");

$keywords_msg      = CValue::getOrSession("keywords_msg");
$keywords_ack      = CValue::getOrSession("keywords_ack");

$_date_min = CValue::getOrSession('_date_min', CMbDT::dateTime("-7 day"));
$_date_max = CValue::getOrSession('_date_max', CMbDT::dateTime("+1 day"));
$page      = CValue::getOrSession('value', 0);

// Types filtres qu'on peut prendre en compte
$filtre_types = array(
  'ok'    => array('emetteur', 'destinataire'),
  'error' => array('no_date_echange','message_invalide', 'acquittement_invalide', 'master_idex_missing')
);

$types = array();
foreach ($filtre_types as $status_type => $_type) {
  foreach ($_type as $type) {
    $types[$status_type][$type] = !isset($t) || in_array($type, $t);
  }
}

$exchange = new $exchange_class;
$exchange->_date_min = $_date_min;
$exchange->_date_max = $_date_max;
$exchange->type      = $exchange_type;
$exchange->group_id  = $exchange_group_id;

$messages = $exchange->getFamily();
$evenements = array();
foreach ($messages as $_message => $_evt_class) { 
  $evt  = new $_evt_class;
  $evts = $evt->getEvenements(); 
  $keys       = array_map_recursive(array("CMbString" , "removeDiacritics"), array_keys($evts));
  $values     = array_values($evts);
  $evenements[$_message] = ($keys && $values) ? array_combine($keys, $values) : array();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("exchange"  , $exchange);
$smarty->assign("types"     , $types);
$smarty->assign("page"      , $page);
$smarty->assign("messages"  , $messages);
$smarty->assign("evenements", $evenements);
$smarty->assign("keywords_msg"  , $keywords_msg);
$smarty->assign("keywords_ack"  , $keywords_ack);
$smarty->display("inc_filters_exchanges.tpl");

