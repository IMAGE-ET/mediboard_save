<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author S�bastien Fillonneau
*/

$object_classname = mbGetValueFromGet("object_classname");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("object_classname" , $object_classname);

$smarty->display("object_not_found.tpl");

?>