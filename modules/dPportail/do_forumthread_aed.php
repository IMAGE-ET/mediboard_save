<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumThread", "forum_thread_id");
$do->createMsg = "Thread cr��";
$do->modifyMsg = "Thread modifi�";
$do->deleteMsg = "Thread supprim�";
$do->doIt();

?>
