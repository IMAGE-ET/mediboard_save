<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */
CCanDo::checkAdmin();

CApp::setTimeLimit(0);
CApp::setMemoryLimit("1024M");
$ds = CSQLDataSource::get("std");

$words          = utf8_encode(CValue::get("words"));
$date_deb       = str_replace("-", "/", CValue::get("date_deb"));
$date_fin       = str_replace("-", "/", CValue::get("date_fin"));
$date_interval  = CValue::get("date_interval");
$specific_user  = CValue::get("user_id");
$start          = (int)CValue::get("start", 0);
$names_types    = CValue::get("names_types");

/**
 * Traitement des utilisateurs sp�cifiques ou globaux @Todo M�thode CSearch...
 */
if (!$specific_user) {
  $user           = new CMediusers();
  $users          = $user->loadListWithPerms(PERM_READ, CAppUI::$user);
  $users_id       = array();
  foreach ($users as $_user) {
    $users_id[] = $_user->_id;
  }
}
else {
  $users_id = explode('|', $specific_user);
  $user_req = str_replace('|', ' || ', $specific_user);
  $words = $words." author_id:(".$user_req.")";
  mbTrace($words);
}

$client_index  = new CSearch();
$client_index->createClient();
if ($date_deb || $date_fin) {
  $words = $client_index->constructWordsWithDate($words, $date_interval, $date_deb, $date_fin);
}

$array_results = array();
$authors = array();
$author_ids = array();
$patient_ids = array();
$patients = array();
$time          = 0;
$nbresult      = 0;
try {
  $results_query = $client_index->searchQueryString('AND', $words, $users_id, $start, 30, $names_types);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  foreach ($results as $result) {
    $var = $result->getHit();
    $author_ids[] = $var["_source"]["author_id"];
    $patient_ids[] = $var["_source"]["patient_id"];
    $array_results[] = $var;
  }

  foreach ($author_ids as $author) {
    $authors[$author] = CMbObject::loadFromGuid("CMediusers-$author");
    $authors[$author]->loadRefFunction();
  }

  foreach ($patient_ids as $_patient) {
    $patients[$_patient] = CMbObject::loadFromGuid("Cpatient-$_patient");
  }

} catch (Exception $e) {
  CAppUI::displayAjaxMsg("La requ�te est mal form�e", UI_MSG_ERROR);
  echo $e->getMessage();
}
//mbTrace($words);
$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("authors", $authors);
$smarty->assign("patients", $patients);
$smarty->assign("results", $array_results);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->display("inc_results_search.tpl");
