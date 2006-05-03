<?php /* $Id: vw_idx_services.php,v 1.4 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.4 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "service"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// R�cup�ration du service � ajouter/editer
$serviceSel = new CService;
$serviceSel->load(mbGetValueFromGetOrSession("service_id"));

// R�cup�ration des services
$order = "nom";
$services = new CService;
$services = $services->loadList(null, $order);

// Cr�ation du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('serviceSel', $serviceSel);
$smarty->assign('services', $services);

$smarty->display('vw_idx_services.tpl');

?>