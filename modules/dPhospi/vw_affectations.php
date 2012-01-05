<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

CCanDo::checkRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$pathos = new CDiscipline();

$g = CGroups::loadCurrent()->_id;

// A passer en variable de configuration

$heureLimit = CAppUI::conf("dPhospi hour_limit");
$date            = CValue::getOrSession("date", mbDate()); 
$mode            = CValue::getOrSession("mode", 0); 
$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filterFunction  = CValue::getOrSession("filterFunction");

if (!$services_ids) {
  $smarty = new CSmartyDP;
  $smarty->display("inc_no_services.tpl");
  CApp::rip();
}

$emptySejour = new CSejour;
$emptySejour->_type_admission = $_type_admission;

// R�cup�ration du service � ajouter/�diter
$totalLits = 0;

// R�cup�ration des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "externe, nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

global $phpChrono;

// Chargement des services
foreach ($services as &$service) {
  if (!in_array($service->_id, $services_ids)){
    continue;
  }
	
  loadServiceComplet($service, $date, $mode);
	$phpChrono->stop("Load Service Complet : '$service->_view'");
  $phpChrono->start();
  $totalLits += $service->_nb_lits_dispo;
}

// Nombre de patients � placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);

$where["annule"]          = "= '0'";
$where["sejour.entree"]   = "BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
if($_type_admission == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($_type_admission) {
  $where["sejour.type"] = " = '$_type_admission'";
} else {
  $where["sejour.type"] = "!= 'urg'";
}
if($_type_admission != "seances") {
  $where[] = "affectation.affectation_id IS NULL";
}

$where[] = "service_id IN (".join($services_ids, ',').") OR service_id IS NULL";
$leftjoin["affectation"]  = "sejour.sejour_id = affectation.sejour_id";

// Filtre sur les fonctions
if($filterFunction){
	$leftjoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
  $where["users_mediboard.function_id"] = " = '$filterFunction'";
}

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, $leftjoin);
$phpChrono->stop("Patient � placer dans la semaine");
$phpChrono->start();

// Liste des patients � placer
$groupSejourNonAffectes = array();

if ($can->edit) {
	$where = array();
	$where["sejour.annule"] = "= '0'";
  $where[] = "service_id IN (".join($services_ids, ',').") OR service_id IS NULL";

	$order = null;
  switch ($triAdm) {
    case "date_entree" :
      $order = "entree_prevue ASC";
      break;
    case "praticien" :  
      $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
      break;
    case "patient" :
      $order = "patients.nom, patients.prenom";
      break;
  }
  
  switch ($_type_admission) {
    case "ambucomp" :
      $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp'";
      break;
    case "0" :
    	break;
    default :
    	$where["sejour.type"] = "= '$_type_admission'"; 
  }
  
  // Admissions de la veille
  $dayBefore = mbDate("-1 days", $date);
	$where["sejour.entree"] = "BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
	$phpChrono->stop("Non affect�s: veille");
	$phpChrono->start();
  
  // Admissions du matin
  $where["sejour.entree"] = "BETWEEN '$date 02:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'";
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affect�s: matin");
  $phpChrono->start();
  
  // Admissions du soir
  $where["sejour.entree"] = "BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affect�s: soir");
  $phpChrono->start();
  
  // Admissions ant�rieures
  $twoDaysBefore = mbDate("-2 days", $date);
  $where["sejour.entree"] = "<= '$twoDaysBefore 23:59:59'";
  $where["sejour.sortie"] = ">= '$date 00:00:00'";

  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
  $phpChrono->stop("Non affect�s: avant");
  $phpChrono->start();
	
}

$functions_filter = array();
foreach($groupSejourNonAffectes as $_keyGroup => $_group) {
  foreach($_group as $_key => $_sejour) {
    $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
    if ($filterFunction && $filterFunction != $_sejour->_ref_praticien->function_id) {
      unset($groupSejourNonAffectes[$_keyGroup][$_key]);
    }
  }
}

$affectation = new CAffectation();
$affectation->entree = mbAddDateTime("08:00:00",$date);
$affectation->sortie = mbAddDateTime("23:00:00",$date);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("services_ids"          , $services_ids);
$smarty->assign("services"              , $services);
$smarty->assign("affectation"           , $affectation);
$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date);
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("emptySejour"           , $emptySejour);
$smarty->assign("filterFunction"        , $filterFunction);
$smarty->assign("triAdm"                , $triAdm);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->assign("functions_filter"      , $functions_filter);

$smarty->display("vw_affectations.tpl");

if (CAppUI::pref("INFOSYSTEM")) {
  mbTrace(CMbArray::pluck($phpChrono->report, "total"), "Rapport uniquement visible avec les informations syst�me");
}


?>