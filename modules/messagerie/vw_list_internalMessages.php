<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage messagerie
* @version $Revision$
* @author Fabien
*/

CCanDo::checkRead();
$user = CMediusers::get();
$selected_folder = CValue::get('selected_folder', 'inbox');

// Liste des messages re�us
$listInboxUnread = CUserMessageDest::countUnreadFor($user);

// Liste des messages archiv�s
$listArchived = CUserMessageDest::countArchivedFor($user);

// Liste des messages envoy�s
$listSent = CUserMessageDest::countSentFor($user);

// Liste des brouillons
$countListDraft = CUserMessageDest::countDraftedFor($user);

$folders = array(
  'inbox'   => $listInboxUnread,
  'archive' => $listArchived,
  'sentbox' => $listSent,
  'draft'   => $countListDraft
);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("user"            , $user);
$smarty->assign('folders'         , $folders);
$smarty->assign('selected_folder' , $selected_folder);

$smarty->display("vw_list_usermessages.tpl");
