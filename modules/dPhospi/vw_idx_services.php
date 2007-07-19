<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// R�cup�ration du service � ajouter/editer
$serviceSel = new CService;
$serviceSel->load(mbGetValueFromGetOrSession("service_id"));

// R�cup�ration des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = $ds->prepareIn(array_keys($etablissements));
$services = new CService;
$services = $services->loadList($where, $order);
foreach($services as $keyService=>$valService){
  $services[$keyService]->loadRefsFwd();
} 

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("serviceSel"     , $serviceSel    );
$smarty->assign("services"       , $services      );
$smarty->assign("etablissements" , $etablissements);

$smarty->display("vw_idx_services.tpl");

?>