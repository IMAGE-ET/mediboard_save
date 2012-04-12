<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source sp�cifi�", UI_MSG_ERROR);
}

if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("Aucun type de test sp�cifi�", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

// Connexion
if ($type_action == "connexion") {
  try {
    $exchange_source->init();
  } catch (CMbException $e) {
    $e->stepAjax(UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("CSourceFileSystem-host-is-a-dir", UI_MSG_OK, $exchange_source->host);
}

// Envoi d'un fichier
else if ($type_action == "sendFile") {
  try {
    $exchange_source->setData("Test source file system in Mediboard", false, "testSendFile$exchange_source->fileextension");
    $exchange_source->send();
  } catch (CMbException $e) {
    $e->stepAjax(UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("Le fichier 'testSendFile$exchange_source->fileextension' a �t� copi� dans le dossier '$exchange_source->host'");
}  
// R�cup�ration des fichiers
else if ($type_action == "getFiles") {
  $count_files = CMbPath::countFiles($exchange_source->host);
  
  CAppUI::stepAjax("Le dossier '$exchange_source->host' contient : $count_files fichier(s)");
  
  $files = array();
  if ($count_files < 1000) {
    try {
      $files = $exchange_source->receive();
    } catch (CMbException $e) {
      $e->stepAjax(UI_MSG_ERROR);
    }
  }
  else {
    CAppUI::stepAjax("Le dossier '$exchange_source->host' contient trop de fichiers pour �tre list�", UI_MSG_WARNING);
  }
  
  // Cr�ation du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("exchange_source", $exchange_source);
  $smarty->assign("files", $files);
  
  $smarty->display("inc_fs_files.tpl");
}

?>