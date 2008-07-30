<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: $
 * @author S�bastien Fillonneau
 */

global $AppUI;
$ds = CSQLDataSource::get("std");

$old_pwd  = mbGetValueFromPost("old_pwd"  , null);
$new_pwd1 = mbGetValueFromPost("new_pwd1" , null);
$new_pwd2 = mbGetValueFromPost("new_pwd2" , null);

// V�rification du mot de passe actuel de l'utilisateur courant
$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$where["user_password"] = $ds->prepare("= %", md5($old_pwd));

$user->loadObject($where);

if($user->_id){
  // Mot de passe actuel correct
  if ($new_pwd1 == $new_pwd2){
    $user->_user_password = $new_pwd1;
    if ($msg = $user->store()) {
      return $AppUI->setMsg($msg);
    }

    $AppUI->setMsg("CUser-msg-password-updated", UI_MSG_OK);
    $AppUI->weak_password = false;
    
  }
  else{
    // Nouveaux mot de passe diff�rents
    $AppUI->setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
  }
}
else{
  // Mauvais mot de passe actuel
  $AppUI->setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
}
?>