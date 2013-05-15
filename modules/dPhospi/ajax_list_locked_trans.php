<?php 

/**
 * Visualiser les transmissions d'une cible ferm�e
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$transmission_id = CValue::get("transmission_id");
$from_compact = CValue::get("from_compact", 0);

$transmission = new CTransmissionMedicale();
$transmission->load($transmission_id);

$trans = new CTransmissionMedicale();
$trans->sejour_id = $transmission->sejour_id;

if ($transmission->libelle_ATC) {
  $trans->libelle_ATC = $transmission->libelle_ATC;
}
else if ($transmission->object_id && $transmission->object_class) {
  $trans->object_class = $transmission->object_class;
  $trans->object_id = $transmission->object_id;
}

$trans = $trans->loadMatchingList("date DESC, transmission_medicale_id ASC");
CMbObject::massLoadFwdRef($trans, "sejour_id");
CMbObject::massLoadFwdRef($trans, "user_id");

$transmissions = array();

foreach ($trans as $_trans) {
  $_trans->canDo();
  $_trans->loadRefSejour();
  $_trans->loadRefUser()->loadRefFunction();
  $_trans->loadTargetObject();

  if ($_trans->_ref_object instanceof CAdministration) {
    $_trans->_ref_object->loadRefsFwd();
  }

  $sort_key_pattern = "$_trans->date $_trans->_class $_trans->user_id $_trans->object_id $_trans->object_class $_trans->libelle_ATC";

  $sort_key = "$_trans->date $sort_key_pattern";

  $date_before = CMbDT::dateTime("-1 SECOND", $_trans->date);
  $sort_key_before = "$date_before $sort_key_pattern";

  $date_after  = CMbDT::dateTime("+1 SECOND", $_trans->date);
  $sort_key_after = "$date_after $sort_key_pattern";

  // Aggr�gation � -1 sec
  if (array_key_exists($sort_key_before, $transmissions)) {
    array_unshift($transmissions[$sort_key_before], $_trans);
  }
  // � +1 sec
  else if (array_key_exists($sort_key_after, $transmissions)) {
    array_unshift($transmissions[$sort_key_after], $_trans);
  }
  // au temps exact, ou unique
  else {
    if (!array_key_exists($sort_key, $transmissions)) {
      $transmissions[$sort_key] = array();
    }
    array_push($transmissions[$sort_key], $_trans);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("transmission" , $transmission);
$smarty->assign("transmissions", $transmissions);
$smarty->assign("from_compact" , $from_compact);

$smarty->display("inc_list_locked_trans.tpl");