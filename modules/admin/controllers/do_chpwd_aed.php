<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$ds = CSQLDataSource::get("std");

$user = CUser::get();

$old_pwd  = CValue::post("old_pwd"  , null);
$new_pwd1 = CValue::post("new_pwd1" , null);
$new_pwd2 = CValue::post("new_pwd2" , null);

// V�rification du mot de passe actuel de l'utilisateur courant
$where = array();
$where["user_id"]       = $ds->prepare("= %", $user->_id);
$where["user_password"] = $ds->prepare("= %", md5($old_pwd));

$user->loadObject($where);

if($user->_id){
  // Mot de passe actuel correct
  if ($new_pwd1 == $new_pwd2){
    $user->_user_password = $new_pwd1;
    if ($msg = $user->store()) {
      return CAppUI::setMsg($msg);
    }

    CAppUI::setMsg("CUser-msg-password-updated", UI_MSG_OK);
    CAppUI::$instance->weak_password = false;
    
  }
  else{
    // Nouveaux mot de passe diff�rents
    CAppUI::setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
  }
}
else{
  // Mauvais mot de passe actuel
  CAppUI::setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
}
?>