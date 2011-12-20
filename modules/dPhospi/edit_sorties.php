<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$vue  = CValue::getOrSession("vue", 0);
$date = CValue::getOrSession("date", mbDate());
$sorties = array("comp" => array("place" => 0, "non_place" => 0),
                 "ambu" => array("place" => 0, "non_place" => 0),
                 "ssr"  => array("place" => 0, "non_place" => 0),
                 "psy"  => array("place" => 0, "non_place" => 0));

$group = CGroups::loadCurrent();

// R�cup�ration de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

$service  = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where);

$date  = CValue::getOrSession("date" , mbDate());

$limit1  = $date." 00:00:00";
$limit2  = $date." 23:59:59";

// Patients plac�s
$affectation                 = new CAffectation();
$ljoin                       = array();
$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]            = "service.service_id = chambre.service_id";
$where                       = array();
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services));
$where["sejour.type"]        = "NOT IN ('exte', 'seances')";
if ($vue) {
  $where["confirme"] = " = '0'";
}

// Patients non plac�s
$sejour                                = new CSejour();
$ljoinNP                               = array();
$ljoinNP["affectation"]                = "sejour.sejour_id = affectation.sejour_id";
$whereNP                               = array();
$whereNP["sejour.group_id"]            = "= '$group->_id'";
$whereNP["sejour.type"]                = "NOT IN ('exte', 'seances')";
$whereNP["affectation.affectation_id"] = "IS NULL";

// Comptage des patients pr�sents
$wherePresents   = $where;
$wherePresents[] = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
$presents        = $affectation->countList($wherePresents, null, $ljoin);

$wherePresentsNP   = $whereNP;
$wherePresentsNP[] = "'$date' BETWEEN DATE(sejour.entree) AND DATE(sejour.sortie)";
$presentsNP        = $sejour->countList($wherePresentsNP, null, $ljoinNP);

$where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";

// Comptage des d�placements
if ($vue) {
  unset($where["confirme"]);
  $where["effectue"] = "= '0'";
}
$where["sejour.sortie"]      = "!= affectation.sortie";
$deplacements = $affectation->countList($where, null, $ljoin);

// Comptage des sorties
$where["sejour.sortie"]      = "= affectation.sortie";
$whereNP["sejour.sortie"]    = "BETWEEN '$limit1' AND '$limit2'";
foreach($sorties as $type => &$_sortie) {
  $where["sejour.type"] = $whereNP["sejour.type"] = " = '$type'";
  $_sortie["place"]     = $affectation->countList($where, null, $ljoin);
  $_sortie["non_place"] = $sejour->countList($whereNP, null, $ljoinNP);
}

$smarty = new CSmartyDP;
$smarty->assign("presents"    , $presents);
$smarty->assign("presentsNP"  , $presentsNP);
$smarty->assign("sorties"     , $sorties);
$smarty->assign("deplacements", $deplacements);
$smarty->assign("vue"         , $vue);
$smarty->assign("date"        , $date);

$smarty->display("edit_sorties.tpl");

?>