<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Envoi � la source cr��e 'HPRIM21' (FTP)
$exchange_source = CExchangeSource::get("hprim21");
$extension = $exchange_source->fileextension;

$ftp = new CFTP();
$ftp->init($exchange_source);

try {
  $ftp->connect();
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
}

$list = array();
try {
  $list = $ftp->getListFiles(".");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
}

if (empty($list)) {
  CAppUI::stepAjax("Le r�pertoire ne contient aucun fichier", UI_MSG_ERROR);
}

foreach($list as $filepath) {
  if (substr($filepath, -(strlen($extension))) == $extension) {
    $filename = basename($filepath);
    $hprimFile = $ftp->getFile($filepath, "tmp/hprim21/$filename");
    
    // Cr�ation de l'�change
    $echg_hprim21 = new CEchangeHprim21();
    $echg_hprim21->group_id        = CGroups::loadCurrent()->_id;
    $echg_hprim21->date_production = mbDateTime();
    $echg_hprim21->store();
    
    $hprimReader = new CHPrim21Reader();
    $hprimReader->_echange_hprim21 = $echg_hprim21;
    $hprimReader->readFile($hprimFile);
    
    // Mapping de l'�change
    $echg_hprim21 = $hprimReader->bindEchange($hprimFile);
    
    if (!count($hprimReader->error_log)) {
      $echg_hprim21->message_valide = true;
      $ftp->delFile($filepath);
    } else {
      $echg_hprim21->message_valide = false;
      CAppUI::stepAjax("Erreur(s) pour le fichier '$filepath' : $hprimReader->error_log", UI_MSG_WARNING);
    }
    $msg = $echg_hprim21->store();
    $msg ? CAppUI::stepAjax("Erreur lors de la cr�ation de l'�change", UI_MSG_WARNING) : 
           CAppUI::stepAjax("L'�change '$echg_hprim21->_id' a �t� cr��.");
    unlink($hprimFile);
  } else {
    $ftp->delFile($filepath);
  }
}

?>