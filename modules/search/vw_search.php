<?php

/**
 * $Id$
 *
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// on r�cup�re les types en fonction de la config �tablissement.
$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}
// on teste la connexion
$test_search = new CSearch();
$test_search->testConnection($group);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("types", $types);
$smarty->display("vw_search.tpl");

