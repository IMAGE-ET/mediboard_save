<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_files"       , "Visualiser les fichiers", TAB_READ);
$module->registerTab("configure"      , "G�rer les cat�gories"   , TAB_ADMIN);
$module->registerTab("files_integrity", "V�rification fichiers"  , TAB_ADMIN);

?>