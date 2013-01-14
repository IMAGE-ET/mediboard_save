<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

CCanDo::checkRead();

$account = CValue::get("account");
$page = CValue::get("page", 0);
$limit_list = CAppUI::pref("nbMailList", 20);

$user = CMediusers::get();

$mail = new CUserMail();
$mail->account_id = $account;
$order = "date_inbox DESC";

$limit= "$page, $limit_list";

$nb_mails = $mail->countMatchingList();
$mails = $mail->loadMatchingList($order, $limit);

foreach ($mails as $_mail) {
  $_mail->loadComplete();
  $_mail->loadRefsFwd();
}


$smarty = new CSmartyDP();
$smarty->assign("mails", $mails);
$smarty->assign("page", $page);
$smarty->assign("nb_mails", $nb_mails);
$smarty->assign("account", $account);
$smarty->display("inc_list_mails.tpl");