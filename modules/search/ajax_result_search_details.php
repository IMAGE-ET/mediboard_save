<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
CCanDo::checkAdmin();

$object_ref_id  = CValue::get("object_ref_id");
$object_ref_class  = CValue::get("object_ref_class");
$type = CValue::get("type");
$words = CValue::get("words");

$client_index  = new CSearch();
$client_index->createClient();
$words = $words." object_ref_class:".$object_ref_class." "."object_ref_id:".$object_ref_id;
$array_results = array();
$array_highlights = array();
try {
  $results_query = $client_index->searchQueryString('AND', $words, 0, 30, array($type), false);
  $results       = $results_query->getResults();
  foreach ($results as $result) {
    $var = $result->getHit();
    $array_results[] = $var;
    $highlights = $result->getHighlights();
    if (count($highlights) != 0) {
      $array_highlights[] = utf8_decode(implode(" [...] ", $highlights['body']));
    }
    else {
      $array_highlights[] = "";
    }
  }
} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Probl�me � la r�cup�ration des donn�es", UI_MSG_ERROR);
}

$smarty = new CSmartyDP();
$smarty->assign("results", $array_results);
$smarty->assign("highlights", $array_highlights);
$smarty->display("inc_results_search_details.tpl");