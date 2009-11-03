<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision$
* @author Fabien
*/
 
global $AppUI, $can;
$can->needsRead();

/** Chargement du message demand� **/
// s'il est indiqu� dans le GET ou la session on charge l'objet
$forum_message = new CForumMessage();
$forum_message->load(CValue::getOrSession('forum_message_id'));
if($forum_message->_id) {
    $forum_message->loadRefs();
} else { // sinon on en cr�e un nouveau
    $forum_message->user_id = $AppUI->user_id;
    $forum_message->date = mbDateTime();
}


/** Chargement du thread demand�  **/
// on r�cup�re le thread auquel appartient le message
if ($forum_message->forum_thread_id) {
    $forum_thread = $forum_message->_ref_forum_thread;
} else {
	$forum_thread = new CForumThread();
	$forum_thread->load(CValue::getOrSession('forum_thread_id'));
	if($forum_thread->_id) {
	    $forum_thread->loadRefs();
	}
}

/** Chargement du theme demand� en fonction du thread **/
$forum_theme = $forum_thread->_ref_forum_theme;
if($forum_theme) {
	$forum_theme->loadRefs();
}


/** R�cup�ration de la liste des messages du thread **/
$listMessages = $forum_thread->_ref_forum_messages;
foreach($listMessages as &$currMessage) {
    $currMessage->loadRefs();
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('listMessages', $listMessages);
$smarty->assign('forum_theme', $forum_theme);
$smarty->assign('forum_thread', $forum_thread);
$smarty->assign('forum_message', $forum_message);

$smarty->display('vw_forummessage.tpl');

?>