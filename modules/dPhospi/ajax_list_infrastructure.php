<?php /* $Id: ajax_list_infrastructure.php 13247 2011-09-23 08:43:46Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 13247 $
* @author 
*/

CCanDo::checkAdmin();

$type_name    = CValue::get("type_name");

$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$prestation_id = CValue::getOrSession("prestation_id");
$secteur_id    = CValue::getOrSession("secteur_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

if ($type_name == "services") {
  // Chargement du service � ajouter/editer
  $service = new CService();
  $service->group_id = $group->_id;
  $service->load($service_id);
  $service->loadRefsNotes();
}
if ($type_name == "chambres") {
  // R�cup�ration de la chambre � ajouter/editer
  $chambre = new CChambre();
  $chambre->load($chambre_id);
  $chambre->loadRefsNotes();
  $chambre->loadRefService();
  foreach ($chambre->loadRefsLits() as $_lit) {
    $_lit->loadRefsNotes();
  }
  
  if (!$chambre->_id) {
    CValue::setSession("lit_id", 0);
  }

    // Chargement du lit � ajouter/editer
    $lit = new CLit();
    $lit->load($lit_id);
    $lit->loadRefChambre();
}

// R�cup�ration des chambres/services
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";
$service = new CService();
$services = $service->loadListWithPerms(PERM_READ,$where, $order);

foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
    $_chambre->loadRefs();
  }
}

if ($type_name == "UF") {
  // Chargement de l'uf � ajouter/�diter
  $uf = new CUniteFonctionnelle();
  $uf->group_id = $group->_id;
  $uf->load($uf_id);
  $uf->loadRefsNotes();
  
  // R�cup�ration des ufs
  $order = "group_id, code";
  $ufs = array("hebergement" => $uf->loadGroupList(array("type" => "= 'hebergement'"), $order),
               "medicale"    => $uf->loadGroupList(array("type" => "= 'medicale'"), $order),
               "soins"       => $uf->loadGroupList(array("type" => "= 'soins'"), $order));
}
if ($type_name == "prestations") {
  // Chargement de la prestation � ajouter/�diter
  $prestation = new CPrestation();
  $prestation->group_id = $group->_id;
  $prestation->load($prestation_id);
  $prestation->loadRefsNotes();
  
  // R�cup�ration des prestations
  $order = "group_id, nom";
  $prestations = $prestation->loadList(null, $order);
  foreach ($prestations as $_prestation) {
    $_prestation->loadRefGroup();
  }
}
if ($type_name == "secteurs") {
   // Chargement du secteur � ajouter / �diter
  $secteur = new CSecteur;
  $secteur->group_id = $group->_id;
  $secteur->load($secteur_id);
  $secteur->loadRefsNotes();
  $secteur->loadRefsServices();
  // R�cup�ration des secteurs
  $secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);
}

$praticiens = CAppUI::$user->loadPraticiens();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

if($type_name == "services"){
  $smarty->assign("services"      , $services);
  $smarty->assign("service"       , $service);
  $smarty->display("inc_vw_idx_services.tpl");
}
if($type_name == "chambres"){
  $smarty->assign("services"       , $services);
  $smarty->assign("chambre"       , $chambre);
  $smarty->assign("lit"           , $lit);
  $smarty->display("inc_vw_idx_chambres.tpl");
}
if($type_name == "UF"){
  $smarty->assign("ufs"           , $ufs);
  $smarty->assign("uf"            , $uf);
  $smarty->display("inc_vw_idx_ufs.tpl");
}
if($type_name == "prestations"){
  $smarty->assign("prestation"    , $prestation);
  $smarty->assign("prestations"   , $prestations);
  $smarty->display("inc_vw_idx_prestations.tpl");
}
if($type_name == "secteurs"){
  $smarty->assign("secteurs"      , $secteurs);
  $smarty->assign("secteur"       , $secteur);
  $smarty->display("inc_vw_idx_secteurs.tpl");
}
?>