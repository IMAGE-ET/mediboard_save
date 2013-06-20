<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Portail
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$user = CUser::get();

// Chargement du thread demand�
$forum_thread_id = CValue::getOrSession('forum_thread_id');
$forum_thread = new CForumThread();
$forum_thread->load($forum_thread_id);
if ($forum_thread->_id) {
  $forum_thread->loadRefs();
}
else {
  $forum_thread->user_id = $user->_id;
  $forum_thread->date = CMbDT::dateTime();
}


// Chargement du theme demand�
$forum_theme_id = CValue::get('forum_theme_id');
if (!$forum_theme_id) {
  $forum_theme_id = $forum_thread->forum_theme_id;
}
$forum_theme = new CForumTheme();
$forum_theme->load($forum_theme_id);
if ($forum_theme->_id) {
  $forum_theme->loadRefs();
}

// R�cup�ration de la liste des themes
$order = 'title ASC';
$theme = new CForumTheme;
$listThemes = $theme->loadList(null, $order);
$listThreads = array();
foreach ($listThemes as $currTheme) {
  $currTheme->loadRefs();

  // R�cup�ration de la liste des threads du theme
  if ($currTheme->_id == $forum_theme_id) {
    $listThreads = $currTheme->_ref_forum_threads;
  }
}

// chargement des references des threads affich�s 
// (necessaire pour l'affichage du nombre de reponses de ceux-ci)
foreach ($listThreads as $currThread) {
  $currThread->loadRefsBack();
}

// Cr�ation du template
$smarty = new CSmartyDP();

// passage des listes de themes et de threads
$smarty->assign("listThreads", $listThreads);
$smarty->assign("listThemes", $listThemes);

// passage des donn�es du theme et du thread en cours
$smarty->assign("forum_thread", $forum_thread);
$smarty->assign("forum_theme", $forum_theme);

$smarty->display("vw_forumthread.tpl");
