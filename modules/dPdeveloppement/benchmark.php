<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// D�verouiller la session pour rendre possible les requ�tes concurrentes.
session_write_close();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("module", "dPdeveloppement");
$smarty->assign("action", "view_logs");
$smarty->display("benchmark.tpl");