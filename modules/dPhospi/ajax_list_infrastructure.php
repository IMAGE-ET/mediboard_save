<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$type_name    = CValue::get("type_name");

$service_id    = CValue::getOrSession("service_id");
$chambre_id    = CValue::getOrSession("chambre_id");
$lit_id        = CValue::getOrSession("lit_id");
$uf_id         = CValue::getOrSession("uf_id");
$secteur_id    = CValue::getOrSession("secteur_id");

$group = CGroups::loadCurrent();

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

$praticiens = CAppUI::$user->loadPraticiens();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("etablissements", $etablissements);

// R�cup�ration des chambres/services
$where = array();
$where["group_id"] = "= '$group->_id'";

if ($type_name == "services") {
  // Chargement des services
  $service = new CService();
  /** @var CService[] $services */
  $services = $service->loadListWithPerms(PERM_READ, $where, "nom");

  foreach ($services as $_service) {
    // Chargement des chambres et lits
    foreach ($_service->loadRefsChambres() as $_chambre) {
      $_chambre->loadRefsLits(true);
    }
  }

  $smarty->assign("services"    , $services);
  $smarty->display("inc_vw_idx_services.tpl");
}

if ($type_name == "UF") {
  // Chargement de l'uf � ajouter/�diter
  $uf = new CUniteFonctionnelle();
  $uf->group_id = $group->_id;
  $uf->load($uf_id);
  $uf->loadRefUm();
  $uf->loadRefsNotes();

  // R�cup�ration des ufs
  $order = "group_id, code";
  $ufs = array(
    "hebergement" => $uf->loadGroupList(array("type" => "= 'hebergement'"), $order),
    "medicale"    => $uf->loadGroupList(array("type" => "= 'medicale'"), $order),
    "soins"       => $uf->loadGroupList(array("type" => "= 'soins'"), $order),
  );

  // R�cup�ration des Unit�s M�dicales (pmsi)
  $ums = array ();
  $ums_infos = array ();
  $um  = new CUniteMedicale();
  if (CSQLDataSource::get("sae") && CModule::getActive("atih")) {
    $um_infos  = new CUniteMedicaleInfos();
    $ums = $um->loadListUm();
    $group = CGroups::loadCurrent();
    $where["group_id"] = " = '$group->_id'";
    $where["mode_hospi"] = " IS NOT NULL";
    $where["nb_lits"] = " IS NOT NULL";
    $ums_infos = $um_infos->loadList($where);
  }

  $smarty->assign("ufs", $ufs);
  $smarty->assign("uf", $uf);
  $smarty->assign("ums", $ums);
  $smarty->assign("ums_infos", $ums_infos);
  $smarty->display("inc_vw_idx_ufs.tpl");
}

if ($type_name == "secteurs") {
  // Chargement du secteur � ajouter / �diter
  $secteur = new CSecteur;
  $secteur->group_id = $group->_id;
  $secteur->load($secteur_id);
  $secteur->loadRefsNotes();
  $secteur->loadRefsServices();

  // R�cup�ration des prestations
  $order = "group_id, nom";

  // R�cup�ration des secteurs
  $secteurs = $secteur->loadListWithPerms(PERM_READ, $where, $order);

  foreach ($secteurs as $_secteur) {
    /** @var CSecteur $_secteur */
    $_secteur->loadRefsServices();
  }

  $smarty->assign("secteurs", $secteurs);
  $smarty->assign("secteur", $secteur);
  $smarty->display("inc_vw_idx_secteurs.tpl");
}