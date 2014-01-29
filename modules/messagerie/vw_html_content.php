<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

// Open a contentHTML from an email_id
CCanDo::checkRead();
$mail_id = CValue::get("mail_id");

$mail = new CUserMail();
$mail->load($mail_id);

if ($mail->_id) {
  $mail->loadRefsFwd();
  $mail->checkInlineAttachments();  //inline attachment
}

if (strpos($mail->_text_html->content, '<') === false) {
  echo nl2br($mail->_text_html->content);
}
else {
  echo $mail->_text_html->content;
}
