<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead && !$dialog) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->display("view_install.tpl");

?>