<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author S�bastien Fillonneau
*/

CCanDo::checkRead();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// R�cup�ration des services
$order = "group_id, nom";
$where = array();
$where["group_id"] = CSQLDataSource::prepareIn(array_keys($etablissements));
$services = new CService;
$services = $services->loadList($where, $order);


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("services"       , $services);

$smarty->display("httpreq_get_services_offline.tpl");
?>