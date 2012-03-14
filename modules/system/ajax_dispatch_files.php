<?php 
/**
 * Receive files EAI
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$this->setTimeLimit(360);

$actor_guid   = CValue::get("actor_guid");
$to_treatment = CValue::get("to_treatment", 1);

$sender = CMbObject::loadFromGuid($actor_guid);
$sender->loadRefGroup();
$sender->loadRefsExchangesSources();

$delete_file = $sender->_delete_file;

$source = reset($sender->_ref_exchanges_sources);

$files = array();
try {
  $files = $source->receive();
} catch (CMbException $e) {
  $e->stepAjax();
}

$fileextension           = $source->fileextension;
$fileextension_write_end = $source->fileextension_write_end;

$i = CAppUI::conf("eai max_files_to_process");
foreach ($files as $_filepath) {
  if ($i == 0) {
    CAppUI::stepAjax("Fin du traitement des fichiers");
    
    return;
  }
  
  $sender->_delete_file = $delete_file;
  
  $path_info = pathinfo($_filepath);
  if (!isset($path_info["extension"])) {
    continue;
  }
  
  $extension = $path_info["extension"];

  // Cas o� l'extension voulue par la source FS est diff�rente du fichier
  if ($fileextension && ($extension != $fileextension)) {
    continue;
  }

  $path = rtrim($path_info["dirname"], "\\/");
  $_filepath_no_ext = "$path/".$path_info["filename"];
  
  // Cas o� le suffix de l'acq OK est pr�sent mais que je n'ai pas de fichier 
  // d'acquittement dans le dossier
  if ($fileextension_write_end && count(preg_grep("@$_filepath_no_ext.$fileextension_write_end$@", $files)) == 0) {
    continue;
  }

  $message  = $source->getData($_filepath);  
  if (!$message) {
    continue;
  }
  
  $source->_receive_filename = $path_info["filename"];

  // Dispatch EAI 
  if ($acq = CEAIDispatcher::dispatch($message, $sender, null, $to_treatment)) {
    try {
      CEAIDispatcher::createFileACK($acq, $sender);
    } catch (Exception $e) {
      if ($sender->_delete_file !== false) {
        $source->delFile($_filepath);
        $i--;
      } 
      else {
        CAppUI::stepAjax("CEAIDispatcher-no_message_supported_for_this_actor", UI_MSG_WARNING, $sender->_data_format->_family_message->code);
      } 
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
  }

  if ($sender->_delete_file !== false) {
    $source->delFile($_filepath);
    $i--;
  }
  else {
    CAppUI::stepAjax("CEAIDispatcher-no_message_supported_for_this_actor", UI_MSG_WARNING, $sender->_data_format->_family_message->code);      
  } 
  
  CAppUI::stepAjax("Message retrait�");
}


?>