<?php

/**
 * $Id: $
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 18541 $
 * @link     http://www.mediboard.org
 */

$old_pwd  = CValue::post("old_pwd" );
$new_pwd1 = CValue::post("new_pwd1");
$new_pwd2 = CValue::post("new_pwd2");
$callback = CValue::post("callback");

// V�rification du mot de passe actuel de l'utilisateur courant
$user = CUser::checkPassword(CUser::get()->user_username, $old_pwd, true);

// Mot de passe actuel correct
if (!$user->_id) {
  CAppUI::stepAjax("CUser-user_password-nomatch", UI_MSG_ERROR);
}

if (!$user->canChangePassword()) {
  CAppUI::stepAjax("CUser-password_change_forbidden", UI_MSG_ERROR);
}

$allow_change_password = CAppUI::conf("admin LDAP allow_change_password");
$ldap_linked = $user->isLDAPLinked();

// Si utilisateur associ� au LDAP et modif de mot de passe non autoris�e: ERROR
if (!$allow_change_password && $ldap_linked) {
  CAppUI::stepAjax("CUser_associate-ldap-no-password-change", UI_MSG_ERROR);
}

// Mots de passe diff�rents
if ($new_pwd1 != $new_pwd2) {
  CAppUI::stepAjax("CUser-user_password-nomatch", UI_MSG_ERROR);
}

// Enregistrement
$user->_user_password = $new_pwd1;
$user->_is_changing   = true;

// If user was obliged to change and successfully changed, remove flag
if ($user->force_change_password) {
  $user->force_change_password = false;
}

if ($msg = $user->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

// Si utilisateur associ� au LDAP et modif mdp autoris�e
if ($ldap_linked && $allow_change_password) {
  try {
    CLDAP::changePassword($user, $old_pwd, $new_pwd1);
    CAppUI::stepAjax("CLDAP-change_password_succeeded", UI_MSG_OK);
  }
  catch(CMbException $e) {
    // R�tablissement de l'ancien mot de passe
    $user->_user_password = $old_pwd;
    if ($msg = $user->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    
    $e->stepAjax();
    CAppUI::stepAjax("CLDAP-change_password_failed", UI_MSG_ERROR);
  }
}

CAppUI::stepAjax("CUser-msg-password-updated", UI_MSG_OK);
CAppUI::$instance->weak_password = false;
CAppUI::callbackAjax($callback);

CApp::rip();
