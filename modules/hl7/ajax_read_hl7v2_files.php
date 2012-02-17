<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireLibraryFile("geshi/geshi");

CCanDo::checkRead();

// Envoi � la source cr��e 'HL7 v.2'
$exchange_source = CExchangeSource::get("hl7v2");
$extension = $exchange_source->fileextension;

$ftp = new CFTP();
$ftp->init($exchange_source);
$ftp->connect();

if (!$list = $ftp->getListFiles($ftp->fileprefix)) {
  CAppUI::stepAjax("Le r�pertoire ne contient aucun fichier", UI_MSG_ERROR);
}

$messages = array();

foreach($list as $filepath) {
  if (substr($filepath, -(strlen($extension))) == $extension) {
    $filename = tempnam("", "hl7");
    $ftp->getFile($filepath, $filename);
    $hl7v2_reader = new CHL7v2Reader();
    
    $message = $hl7v2_reader->readFile($filename);
    
    if (!$message) {
      $message = new CHL7v2Message;
    }
    
    $message->filename = basename($filepath);
    
    $message->_errors_msg   = !$message->isOK(CHL7v2Error::E_ERROR);
    $message->_warnings_msg = !$message->isOK(CHL7v2Error::E_WARNING);

    $geshi = new Geshi($message->toXML()->saveXML(), "xml");
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style("max-height: 100%; white-space:pre-wrap;");
    $geshi->enable_classes();
    $message->_xml = $geshi->parse_code();
    
    $messages[] = $message;
    
    unlink($filename);
  } else {
   // $ftp->delFile($filepath);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("messages", $messages);
$smarty->display("inc_read_hl7v2_files.tpl");
