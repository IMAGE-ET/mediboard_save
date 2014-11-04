<?php

/**
 * $Id$
 *
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
CCanDo::checkRead();
// R�cup�ration des valeurs n�cessaires
$words         = CValue::get("words");
$_min_date     = str_replace("-", "/", CValue::get("_min_date", "*"));
$_max_date     = str_replace("-", "/", CValue::get("_max_date", "*"));
$_date         = str_replace("-", "/", CValue::get("_date"));
$specific_user = CValue::get("user_id");
$start         = (int)CValue::get("start", 0);
$names_types   = CValue::get("names_types");
$aggregate     = CValue::get("aggregate");
$sejour_id     = CValue::get("sejour_id");

// Ajout du group_id pour imperm�abiliser les donn�es au niveau �tablissement.
$current_group = CGroups::loadCurrent()->_id;
$words .= "group_id:(". $current_group . ")";

/**
 * Traitement des utilisateurs sp�cifiques ou globaux
 */
if (!$specific_user) {
  $user = new CMediusers();
  if ($sejour_id) {
    $users = $user->loadPraticiens(PERM_READ);
  }
  else {
    $users = $user->loadPraticiens(PERM_EDIT);
  }
  $users_id = array();
  foreach ($users as $_user) {
    $users_id[] = $_user->_id;
  }
  $user_req = implode(' || ', $users_id);
  $words    = $words . " prat_id:(" . $user_req . ")";
}
else {
  $users_id = explode('|', $specific_user);
  $user_req = str_replace('|', ' || ', $specific_user);
  $words    = $words . " prat_id:(" . $user_req . ")";
}
// Traitement du s�jour sp�cifique dans le cadre du pmsi
if ($sejour_id) {
  $words = $words . " object_ref_class:(CSejour) object_ref_id:(" . $sejour_id . ")";
}

// Donn�es n�cessaires pour la recherche
$client_index = new CSearch();
$client_index->createClient();
$words             = $client_index->constructWordsWithDate($words, $_date, $_min_date, $_max_date);
$time              = 0;
$nbresult          = 0;
$array_results     = array();
$array_highlights  = array();
$array_aggregation = array();
$objects_refs      = array();
$authors           = array();
$author_ids        = array();
$patient_ids       = array();
$patients          = array();

// Recherche fulltext
try {
  $results_query = $client_index->searchQueryString('AND', $words, $start, 30, $names_types, $aggregate);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  // traitement des r�sultats
  foreach ($results as $result) {
    $var             = $result->getHit();
    $author_ids[]    = $var["_source"]["author_id"];
    $patient_ids[]   = $var["_source"]["patient_id"];
    $array_results[] = $var;

    // Traitement des highlights
    $highlights = $result->getHighlights();
    if ($highlights) {
      $array_highlights[] = utf8_decode(implode(" [...] ", $highlights['body']));
    }
  }
  // traitement des auteurs
  foreach ($author_ids as $author) {
    $authors[$author] = CMbObject::loadFromGuid("CMediusers-$author");
    $authors[$author]->loadRefFunction();
  }
  // traitement des patients
  foreach ($patient_ids as $_patient) {
    $patients[$_patient] = CMbObject::loadFromGuid("CPatient-$_patient");
  }


  //traitement des contextes r�f�rents si aggregation est coch�e
  if ($aggregate) {
    $array_aggregation = $results_query->getAggregations("ref_class");
    $agg_ref_class     = $array_aggregation['ref_class']['buckets'];
    foreach ($agg_ref_class as $_agg) {
      if ($_agg['key'] == "cconsult" || $_agg['key'] == "cconsultation") {
        $_agg['key'] = "CConsultation";
      }
      if ($_agg['key'] == "coper" || $_agg['key'] == "coperation") {
        $_agg['key'] = "COperation";
      }
      if ($_agg['key'] == "cconsultanesth") {
        $_agg['key'] = "CConsultAnesth";
      }
      $name_object = $_agg['key'];
      $agg_ref_id  = $_agg['sub_ref_id']['buckets'];

      foreach ($agg_ref_id as $__agg) {
        $id_object                          = $__agg['key'];
        $objects_refs[$id_object]["object"] = CMbObject::loadFromGuid("$name_object-$id_object");
        $agg_ref_type                       = $__agg['sub_ref_type']['buckets'];

        foreach ($agg_ref_type as $_key => $___agg) {
          $key                                              = $___agg['key'];
          $count                                            = $___agg['doc_count'];
          $objects_refs[$id_object]['type'][$_key]['key']   = $key;
          $objects_refs[$id_object]['type'][$_key]['count'] = $count;
        }
      }
    }
    foreach ($objects_refs as $_object_ref) {
      if ($_object_ref['object'] instanceof CMbObject) {
        if ($_object_ref['object'] instanceof CConsultAnesth) {
          $_object_ref['object']->loadRefConsultation()->loadRefPraticien();
          $_object_ref['object']->loadRefConsultation()->loadRelPatient();
          $_object_ref['object']->loadRefConsultation()->loadRefPlageConsult();
          $_object_ref['object']->loadRefSejour();
          if ($_object_ref['object']->_ref_sejour->_id) {
            $_object_ref['object']->_ref_sejour->loadNDA();
          }
        }
        else {
          if ($_object_ref['object'] instanceof CConsultation) {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadRefPlageConsult();
            $_object_ref['object']->loadRefSejour();
            if ($_object_ref['object']->_ref_sejour->_id) {
              $_object_ref['object']->_ref_sejour->loadNDA();
            }

          }
          if ($_object_ref['object'] instanceof CSejour) {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadNDA();
          }
          else {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadRefSejour();
            if ($_object_ref['object']->_ref_sejour->_id) {
              $_object_ref['object']->_ref_sejour->loadNDA();
            }
          }
        }
      }
    }
  }
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("La requ�te est mal form�e", UI_MSG_ERROR);
  mbLog($e->getMessage());
}
//mbTrace($words);
$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("authors", $authors);
$smarty->assign("patients", $patients);
$smarty->assign("results", $array_results);
$smarty->assign("highlights", $array_highlights);
$smarty->assign("objects_refs", $objects_refs);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("words", $words);
$smarty->display("inc_results_search.tpl");
