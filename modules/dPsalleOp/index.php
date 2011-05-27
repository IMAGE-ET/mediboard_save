<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can;

// Chargement de l'utilisateur courant
$user = CMediusers::get();
$user->isPraticien();

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_operations"  , TAB_READ);

if (!$user->_is_praticien || ($user->_is_praticien && $can->edit)){
	$module->registerTab("vw_reveil"      , TAB_READ);
	$module->registerTab("vw_soins_reveil", TAB_READ);
	//$module->registerTab("vw_brancardage", TAB_READ);
	$module->registerTab("vw_urgences"    , TAB_READ);
	$module->registerTab("vw_suivi_salles", TAB_READ);
	//$module->registerTab("vw_anesthesie"     , TAB_READ);
	if(CAppUI::conf('dPsalleOp CActeCCAM signature')){
	  $module->registerTab("vw_signature_actes", TAB_READ);
	}
  $module->registerTab("vw_daily_check_traceability", TAB_READ);
  $module->registerTab("vw_daily_check_item_type"   , TAB_ADMIN);
}

?>