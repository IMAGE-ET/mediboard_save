<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision: $
* @author Fabien
*/
 
global $AppUI, $can;
$can->needsRead();

$mbmail = new CMbMail();

// Liste des messages re�us
$where = array();
$where["to"]            = "= '$AppUI->user_id'";
$where["date_archived"] = "IS NULL";
$order = "date_sent DESC";
$listInbox = $mbmail->loadList($where, $order);
foreach($listInbox as &$mail) {
  $mail->loadRefsFwd();
}

// Liste des messages archiv�s
$where = array();
$where["to"]            = "= '$AppUI->user_id'";
$where["date_archived"] = "IS NOT NULL";
$order = "date_sent DESC";
$listArchived = $mbmail->loadList($where, $order);
foreach($listArchived as &$mail) {
  $mail->loadRefsFwd();
}

// Liste des messages envoy�s
$where = array();
$where["from"]            = "= '$AppUI->user_id'";
$order = "date_sent DESC";
$listSent = $mbmail->loadList($where, $order);
foreach($listSent as &$mail) {
  $mail->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listInbox"   , $listInbox);
$smarty->assign("listArchived", $listArchived);
$smarty->assign("listSent"    , $listSent);

$smarty->display("vw_list_mbmails.tpl");

?>