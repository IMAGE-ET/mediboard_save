<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Romain Ollivier
*/

global $can, $log_path;

$can->needsRead();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display('view_logs.tpl');

include($log_path);

?>