<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->needsRead();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("import_ami.tpl");

?>