<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$do_import = CValue::get("do_import");
$start     = CValue::getOrSession("start", 0);
$count     = CValue::get("count", 5);

$user = new CUser();

// Requ�tes
$ljoin["id_sante400"] = "`id_sante400`.`object_id` = `users`.`user_id`";
    
$where = array();
$where["id_sante400.object_class"] = "= 'CUser'";
$where["id_sante400.tag"]          = "= '".CAppUI::conf("admin LDAP ldap_tag")."'";
$where["id_sante400.id400"]        = "IS NOT NULL";
$where["users.template"]           = "= '0'";

if (!$do_import) {
  $count_users_ldap = $user->countList($where, null, null, null, $ljoin);
  $where = array();
  $where["users.template"] = "= '0'";
  $count_users_all  = $user->countList($where);
  CAppUI::stepAjax(($count_users_all - $count_users_ldap)." comptes qui ne sont pas associ�s");
} else {
  $ldaprdn  = CAppUI::conf("admin LDAP ldap_user");
  $ldappass = CAppUI::conf("admin LDAP ldap_password");

  // R�cup�ration de la liste des comptes qui ne sont pas associ�s
  $users_ldap = $user->loadList($where, null, null, null, $ljoin);
  $where = array();
  $where["users.template"] = "= '0'";
  $users_all = $user->loadList($where, null);

  $users = array_diff_key($users_all, $users_ldap);
  $users = array_slice($users, $start, $count);
  
  $count = $count_no_associate = $count_associate =  0;
  foreach($users as $_user) { 
    try {
      $source_ldap = CLDAP::bind($_user, $ldaprdn, $ldappass);
      $_user = CLDAP::searchAndMap($_user, $source_ldap, $source_ldap->_ldapconn, $_user->user_username, null);
    } catch(Exception $e) {}
    
    if ($_user->_count_ldap != 0) {
      $count_associate++;
    }
    if ($_user->_count_ldap == 0) {
      $count_no_associate++;
    }
    
    $count++;
  }
  if ($count == 0) {
    echo "<script type='text/javascript'>stop=true;</script>";
  }
  
  $next = $start + $count_no_associate;
  
  CAppUI::stepAjax("$count_associate comptes associ�s");
  CAppUI::stepAjax("$count_no_associate comptes non associ�s", UI_MSG_WARNING);
  
  CValue::setSession("start", $next);
  CAppUI::stepAjax("On continuera au n� $next / ".count($users_all));
}

?>