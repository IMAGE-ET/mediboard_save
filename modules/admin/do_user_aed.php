<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CUser", "user_id");

$do->createMsg = "Utilisateur cr��";
$do->modifyMsg = "Utilisateur modifi�";
$do->deleteMsg = "Utilisateur supprim�";

$do->doBind();
    
if (intval(dPgetParam($_POST, 'del'))) {
  $do->doDelete();
} else {
  // Verification de la non existence d'un utilisateur avec le m�me login
  $otherUser = new CUser;
  $where = array();
  $where["user_username"] = "= '".$do->_obj->user_username."'";
  $where["user_id"] = "!= '".$do->_obj->user_id."'";
  $otherUser->loadObject($where);
  if($otherUser->user_id) {
    $AppUI->setMsg("Login d�j� existant dans la base", UI_MSG_ERROR);
  } else {
    $do->doStore();
  }
}
$do->doRedirect();

?>