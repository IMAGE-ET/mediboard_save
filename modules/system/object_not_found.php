<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author S�bastien Fillonneau
*/

list ($object_class, $object_id) = explode("-", mbGetValueFromGet("object_guid"));

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id);

$smarty->display("object_not_found.tpl");

?>