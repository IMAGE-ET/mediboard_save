<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author S�bastien Fillonneau
*/

CCanDo::checkAdmin();

$smarty = new CSmartyDP();
$smarty->display("vw_create_archive.tpl");
?>