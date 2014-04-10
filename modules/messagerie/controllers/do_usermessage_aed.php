<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author Thomas despoix
 */

$user = CMediusers::get();
$date =  CMbDT::dateTime();
$dests = CValue::post("dest", array());
$del = CValue::post("del", 0);
$send_it = CValue::post("_send");
$archive_mine = CValue::post("_archive");
$read_only = CValue::post("_readonly");

$usermessage = new CUserMessage();

// edit mode (draft)
$usermessage->load($_POST["usermessage_id"]);
if ($del && $usermessage->_id) {
  if ($msg = $usermessage->delete()) {
    CAppUI::stepAjax($msg, UI_MSG_ERROR);
  }
  return;
}
$usermessage->bind($_POST);
if ($msg = $usermessage->store()) {
  mbLog($msg);
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

$destinataires = $usermessage->loadRefDests();
foreach ($destinataires as $_dest) {

  // mine reception
  if ($_dest->to_user_id == $user->_id) {
    $_dest->archived = $archive_mine;
    if (!$_dest->datetime_read) {
      $_dest->datetime_read = $date;
    }
    if ($msg = $_dest->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }

  // in edit mode, we don't find a dest, (delete it !)
  if (!$read_only && !in_array($_dest->to_user_id, $dests)) {
    if ($msg = $_dest->delete()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    continue;
  }
}


foreach ($dests as $_dest) {
  $destinataire = new CUserMessageDest();
  $destinataire->user_message_id = $usermessage->_id;
  $destinataire->from_user_id = $usermessage->creator_id;
  $destinataire->to_user_id = $_dest;
  $destinataire->loadMatchingObject();
  if ($send_it) {
    $destinataire->datetime_sent = $date;
  }
  if ($msg = $destinataire->store()) {
    CAppUI::stepAjax($msg, UI_MSG_ERROR);
  }
}
