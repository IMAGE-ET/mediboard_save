<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author Romain Ollivier
 */

global $AppUI, $can, $m;

if (!$can->read && !$dialog) {
  $can->redirect();
}
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("view_install.tpl");

?>