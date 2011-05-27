<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$username = trim(CValue::post('username'));
$password = trim(CValue::post('password'));

if (!$username) {
  CAppUI::setMsg("Auth-failed-nousername", UI_MSG_ERROR);
}

// If admin: no need to  give a password
else if (($user->user_type == 1) && !CAppUI::conf("admin LDAP ldap_connection")) {
  $_REQUEST['loginas'] = $username;
  CAppUI::login();
}

else if (!$password) {
  CAppUI::setMsg("Auth-failed-nopassword", UI_MSG_ERROR);
}

else {
  $_REQUEST['loginas'] = $username;
  
  if (CAppUI::conf("admin LDAP ldap_connection")) {
    $_REQUEST['passwordas'] = $password;
    
    CAppUI::login(true);
  } else {
    $new_user = new CUser;
    $new_user->user_username = trim($username);
    $new_user->loadMatchingObject();
    
    if (md5($password) != $new_user->user_password) {
      CAppUI::setMsg("Auth-failed-combination", UI_MSG_ERROR);
    }
    else CAppUI::login(true);
  }
}

if ($msg = CAppUI::getMsg()) {
  echo $msg;
  return;
}
else CAppUI::callbackAjax('UserSwitch.reload');
