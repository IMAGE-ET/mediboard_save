<?php 

/**
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$date     = CValue::get("date");
$user_id  = CValue::get("user_id");
$type     = CValue::get("type");
$words    = CValue::get("words");
$start    = (int)CValue::get("start", 0);

$client_index  = new CSearchLog();
$client_index->createClient();
$date = CMbDT::format($date, "%Y/%m/%d");
$words = "date:[".$date." TO "."$date] user_id:(".$user_id.")";
$array_results = array();
$array_highlights = array();
$authors           = array();
$author_ids        = array();
$time              = 0;
$nbresult          = 0;

try {
  $results_query = $client_index->searchQueryLogDetails('AND', $words, 0, 30, array($type), false);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  foreach ($results as $result) {
    $var = $result->getHit();
    $array_results[] = $var;
    $author_ids[]    = $var["_source"]["user_id"];

    $highlights = $result->getHighlights();
    if (count($highlights) != 0) {
      $array_highlights[] = utf8_decode(implode(" [...] ", $highlights['body']));
    }
    else {
      $array_highlights[] = "";
    }
  }
  // traitement des auteurs
  foreach ($author_ids as $author) {
    $authors[$author] = CMbObject::loadFromGuid("CMediusers-$author");
    $authors[$author]->loadRefFunction();
  }
} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Probl�me � la r�cup�ration des donn�es", UI_MSG_ERROR);
}

$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("authors", $authors);
$smarty->assign("results", $array_results);
$smarty->assign("highlights", $array_highlights);
$smarty->assign("objects_refs", null);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("date_log_details", $date);

$smarty->display("inc_results_log_search.tpl");