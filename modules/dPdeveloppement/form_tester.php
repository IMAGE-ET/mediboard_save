<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Fabien M�nager
*/

global $can;
$can->needsRead();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display('form_tester.tpl');

?>