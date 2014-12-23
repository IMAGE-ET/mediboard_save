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

try{
  // r�cup�ration du client
  $client_index   = new CSearch();
  $client_index->createClient();
  $infos_index = $client_index->loadCartoInfos();

} catch (Exception $e) {
  $infos_index = array();
  CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connect�", UI_MSG_WARNING);

}

try{
  // r�cup�ration des donn�es pour les journaux utilisateur
  $client_log   = new CSearchLog();
  $client_log->createClient();
  $infos_log = $client_log->loadCartoInfos();

} catch (Exception $e) {
  $infos_log = array();
  CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connect�", UI_MSG_WARNING);

}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("infos_index", $infos_index);
$smarty->assign("infos_log", $infos_log);

$smarty->display("vw_cartographie_mapping.tpl");