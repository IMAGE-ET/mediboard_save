<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("log", file_get_contents(LOG_PATH));
$smarty->display('view_logs.tpl');
