<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkEdit();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("files_integrity.tpl");

?>