<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediuser
* @version $Revision$
* @author Fabien M�nager
*/

$hexa_values = array('33', '66', '99', 'CC', 'EE', 'FF');
$range = range(0, count($hexa_values)-1);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("hex",   $hexa_values);
$smarty->assign("range", $range);
$smarty->assign("color", mbGetValueFromGet("color"));

$smarty->display("color_selector.tpl");

?>