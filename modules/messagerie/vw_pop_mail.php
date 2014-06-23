<?php 

/**
 * open a mail by its UID, directly from server
 *
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$mail_id = CValue::get("id");
//usermail
$mail = new CUserMail();
$mail->load($mail_id);
$mail->loadAttachments();

//client POP
$clientPOP = new CSourcePOP();
$clientPOP->load($mail->account_id);
$pop = new CPop($clientPOP);
if (!$pop->open()) {
  return;
}

//overview
$overview = $pop->header($mail->uid);
$msgno = $overview->msgno;

$infos = $pop->infos($msgno);

//structure
$structure = $pop->structure($mail->uid);

//content
$content = $pop->getFullBody($mail->uid);

//attachments
$attachments = array();
$_attachments = $pop->getListAttachments($mail->uid);
foreach ($_attachments as $_attach) {
  $attachments[] = $_attach->getPlainFields();
}

$pop->close();

$smarty = new CSmartyDP();
$smarty->assign("mail", $mail);
$smarty->assign("overview", $overview);
$smarty->assign("structure", $structure);
$smarty->assign("mail_id", $mail_id);
$smarty->assign("content", $content);
$smarty->assign("attachments", $attachments);
$smarty->assign("infos", $infos);
$smarty->display("vw_pop_mail.tpl");