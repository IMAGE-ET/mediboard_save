<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Thomas Despoix
*/

global $can;

$can->needsRead();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("launch_tests.tpl");

?>