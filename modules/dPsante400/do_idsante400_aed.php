<?php /* $Id: do_plageressource_aed.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPsante400
* @version $Revision: 23 $
* @author Thomas Despoix
**/

global $AppUI;

$do = new CDoObjectAddEdit("CIdSante400", "id_sante400_id");
$do->createMsg = $AppUI->_("msg-CIdSante400-create"); //ID Sant� 400 cr��
$do->modifyMsg = $AppUI->_("msg-CIdSante400-modify"); //"ID Sant� 400 modifi�";
$do->deleteMsg = $AppUI->_("msg-CIdSante400-delete"); //"ID Sant� 400 supprim�";
$do->redirect = null;
$do->doIt();


?>