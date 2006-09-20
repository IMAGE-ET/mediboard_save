<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_modeles"     , "liste des mod�les"      , TAB_READ);
$module->registerTab("addedit_modeles", "Edition des mod�les"    , TAB_READ);
$module->registerTab("vw_idx_aides"   , "Aides � la saisie"      , TAB_READ);
$module->registerTab("vw_idx_listes"  , "Listes de choix"        , TAB_READ);
$module->registerTab("vw_idx_packs"   , "Packs d'hospitalisation", TAB_READ);

?>