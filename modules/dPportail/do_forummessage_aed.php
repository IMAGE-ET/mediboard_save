<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumMessage", "forum_message_id");
$do->createMsg = "Message cr��";
$do->modifyMsg = "Message modifi�";
$do->deleteMsg = "Message supprim�";
$do->doIt();

?>
