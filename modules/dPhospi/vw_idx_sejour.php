<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
require_once($AppUI->getModuleFile("dPhospi", "inc_vw_affectations"));

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate()); 
$mode = mbGetValueFromGetOrSession("mode", 0);
$service_id = mbGetValueFromGetOrSession("service_id");
$sejour_id = mbGetValueFromGetOrSession("sejour_id",0);
// R�cup�ration du service � ajouter/�diter
$totalLits = 0;



// R�cuperation du sejour s�lectionn�
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefs();


// R�cup�ration de la liste des services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$services = $services->loadListWithPerms(PERM_READ,$where);


// Chargement du service selectionne
$service = new CService; 
$service->load($service_id);
loadServiceComplet($service, $date, $mode);

// Cr�ation du template
$smarty = new CSmartyDP();

//$smarty->assign("sejour", $sejour);
$smarty->assign("object"                , $sejour);
$smarty->assign("mode"                  , $mode);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("date"                  , $date );
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("services"              , $services);
$smarty->assign("service"               , $service);
$smarty->display("vw_idx_sejour.tpl");


?>