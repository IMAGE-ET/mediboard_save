<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkAdmin();

$secteur_id    = CValue::getOrSession("secteur_id");
$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$prestation_id = CValue::getOrSession("prestation_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement du secteur � ajouter / �diter
$secteur = new CSecteur;
$secteur->group_id = $group->_id;
$secteur->load($secteur_id);
$secteur->loadRefsNotes();
$secteur->loadRefsServices();

// Chargement du service � ajouter / �diter
$service = new CService();
$service->group_id = $group->_id;
$service->load($service_id);
$service->loadRefsNotes();

// R�cup�ration de la chambre � ajouter / �diter
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

// Chargement du lit � ajouter / �diter
$lit = new CLit();
$lit->load($lit_id);
$lit->loadRefChambre();

// R�cup�ration des chambres/services/secteurs
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ,$where, $order);
foreach ($services as $_service) {
  foreach ($_service->loadRefsChambres() as $_chambre) {
    $_chambre->loadRefs();
  }
}

$secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);

// Chargement de l'uf � ajouter/�diter
$uf = new CUniteFonctionnelle();
$uf->group_id = $group->_id;
$uf->load($uf_id);
$uf->loadRefsNotes();

// R�cup�ration des ufs
$order = "group_id, code";
$ufs = array("hebergement" => $uf->loadList(array("type" => "= 'hebergement'"), $order),
             "medicale"    => $uf->loadList(array("type" => "= 'medicale'"), $order),
             "soins"       => $uf->loadList(array("type" => "= 'soins'"), $order));

// Chargement de la prestation � ajouter/�diter
$prestation = new CPrestation();
$prestation->group_id = $group->_id;
$prestation->load($prestation_id);
$prestation->loadRefsNotes();

// R�cup�ration des prestations
$presta = new CPrestation;
$presta->group_id = $group->_id;
$prestations = $presta->loadMatchingList("nom");

$praticiens = CAppUI::$user->loadPraticiens();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("services"      , $services);
$smarty->assign("service"       , $service);
$smarty->assign("secteurs"      , $secteurs);
$smarty->assign("secteur"       , $secteur);
$smarty->assign("chambre"       , $chambre);
$smarty->assign("lit"           , $lit);
$smarty->assign("ufs"           , $ufs);
$smarty->assign("uf"            , $uf);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("prestation"    , $prestation);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_idx_infrastructure.tpl");
?>