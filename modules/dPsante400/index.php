<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: $
* @author Thomas Despoix
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_identifiants", "Identifiants Sant�400", TAB_EDIT);
$module->registerTab("sante400"         , "Int�gration sant� 400", TAB_READ);

?>