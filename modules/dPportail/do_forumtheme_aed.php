<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPportail
 *  @version $Revision: $
 *  @author Fabien
 */

global $AppUI;

$do = new CDoObjectAddEdit("CForumTheme", "forum_theme_id");
$do->createMsg = "Th�me cr��";
$do->modifyMsg = "Th�me modifi�";
$do->deleteMsg = "Th�me supprim�";
$do->doIt();

?>
