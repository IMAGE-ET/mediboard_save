<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Thomas Despoix
 */

CCanDo::checkRead();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("cache_tester.tpl");

