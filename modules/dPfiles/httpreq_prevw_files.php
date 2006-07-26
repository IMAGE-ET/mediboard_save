<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPfiles", "files"));

$file_id = mbGetValueFromGetOrSession("file_id", null);

$file = new CFile;
$file->load($file_id);

// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("file_id", $file_id);
$smarty->assign("file"   , $file   );

$smarty->display("inc_prevw_files.tpl");

?>
