<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien M�nager
*/

CCanDo::checkRead();


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display('poc_gestes_perop.tpl');

?>