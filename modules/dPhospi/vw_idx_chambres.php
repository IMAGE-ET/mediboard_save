<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// R�cup�ration de la chambre � ajouter/editer
$chambreSel = new CChambre;
$chambreSel->load(mbGetValueFromGetOrSession("chambre_id"));
$chambreSel->loadRefs();

// R�cup�ration du lit � ajouter/editer
$litSel = new CLit;
$litSel->load(mbGetValueFromGetOrSession("lit_id"));
$litSel->loadRefs();

// R�cup�ration des chambres/services
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);
foreach ($services as $service_id => $service) {
  $services[$service_id]->loadRefs();
  $chambres =& $services[$service_id]->_ref_chambres;
  foreach ($chambres as $chambre_id => $chambre) {
	  $chambres[$chambre_id]->loadRefs();
	}
}

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("chambreSel", $chambreSel);
$smarty->assign("litSel"    , $litSel);
$smarty->assign("services"  , $services);

$smarty->display("vw_idx_chambres.tpl");

?>