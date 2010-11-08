<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$mode = CValue::get("mode", "count");
$date    = CValue::getOrSession("date", mbDate());
$kine_id = CValue::getOrSession("kine_id");
$hide_noevents = CValue::getOrSession("hide_noevents");

$mediuser = new CMediusers();
$mediuser->load($kine_id);

$planning = new CPlanningWeek($date);

// Sejour SSR
$sejour = new CSejour;

$ds = CSQLDataSource::get("std");

// Sejours pour lesquels le kine est r�f�rent
if ($mode == "count" || $mode == "referenced") {
	$join = array();
	$join["patients"]  = "patients.patient_id = sejour.patient_id";
	$order = "nom, prenom";
	$join["bilan_ssr"]  = "bilan_ssr.sejour_id = sejour.sejour_id";
	$join["technicien"] = "technicien.technicien_id = bilan_ssr.technicien_id";
	$where = array();
	$where["sejour.type"  ] = "= 'ssr'";
	$where["sejour.entree"] = "<= '$planning->date_max'";
	$where["sejour.sortie"] = ">= '$planning->date_min'";
	$where["sejour.annule"] = "= '0'";
	$where["technicien.kine_id"] = "= '$kine_id'";
	
	if ($mode == "count") {
	  $counts["referenced"] = $sejour->countList($where, $order, null, null, $join);
	} 
	else {
	  $sejours = $sejour->loadList($where, $order, null, null, $join);
	}
}

// Sejours pour lesquels le kine est rempla�ant
if ($mode == "count" || $mode == "replaced") {
  $order = "nom, prenom";
	$join = array();
	$join["patients"   ]  = "patients.patient_id = sejour.patient_id";
  $join["replacement"]  = "replacement.sejour_id = sejour.sejour_id";
  $join["plageconge"]   = "plageconge.plage_id = replacement.conge_id";
	$where = array();
	$where["sejour.type"  ] = "= 'ssr'";
	$where["sejour.entree"] = "<= '$planning->date_max'";
	$where["sejour.sortie"] = ">= '$planning->date_min'";
	$where["sejour.annule"] = "= '0'";
	$where["replacement.replacement_id"] = "IS NOT NULL";
	$where["replacement.replacer_id"] = " = '$kine_id'";
  $where["plageconge.date_debut"] = "<= '$planning->date_max'";
  $where["plageconge.date_fin"  ] = ">= '$planning->date_min'";
	
	if ($mode == "count") {
	  $counts["replaced"] = $sejour->countList($where, $order, null, null, $join);
	} 
	else {
	  $sejours = $sejour->loadList($where, $order, null, null, $join);
	}
}

// Sejours pour lesquels le r��ducateur a des �v�nements
if ($mode == "count" || $mode == "planned") {
  $order = "nom, prenom";
	$join = array();
	$join["patients"     ]  = "patients.patient_id = sejour.patient_id";
	$join["evenement_ssr"]  = "evenement_ssr.sejour_id = sejour.sejour_id";
	$where = array();
	$where["sejour.type"  ] = "= 'ssr'";
	$where["sejour.entree"] = "<= '$planning->date_max'";
	$where["sejour.sortie"] = ">= '$planning->date_min'";
	$where["sejour.annule"] = "= '0'";
	$where["evenement_ssr.therapeute_id"] = "= '$kine_id'";
	$group = "sejour.sejour_id";
	
	if ($mode == "count") {
		// Do not use countList which won't work due to group by statement
	  $counts["planned"] = count($sejour->loadIds($where, $order, null, $group, $join));
	} 
	else {
	  $sejours = $sejour->loadList($where, $order, null, $group, $join);
	}
}

// Sejours pour lesquels le r��ducateur est ex�cutant pour des lignes prescrites mais n'a pas encore d'evenement planifi�s
if ($mode == "count" || $mode == "plannable") {
	// S�jours �lligibles
	$where = array();
	$where["sejour.type"  ] = "= 'ssr'";
	$where["sejour.entree"] = "<= '$planning->date_max'";
	$where["sejour.sortie"] = ">= '$planning->date_min'";
	$where["sejour.annule"] = "= '0'";
	$sejour_ids = $sejour->loadIds($where);
	
	// Identifiants de cat�gorie de prescriptions disponibles
	$mediuser->loadRefFunction();
	$function =& $mediuser->_ref_function;
	$executants = $function->loadBackRefs("executants_prescription");
	$category_ids = CMbArray::pluck($executants, "category_prescription_id");
	
	// Recherche des lignes de prescriptions executables
	$line = new CPrescriptionLineElement;
	$join = array();
	$where = array();
	$join["element_prescription"] = "element_prescription.element_prescription_id = prescription_line_element.element_prescription_id";
	$where["element_prescription.category_prescription_id"] = $ds->prepareIn($category_ids);
	$join["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id";
	$where["prescription.type"] = "= 'sejour'";
	$where["prescription.object_class"] = "= 'CSejour'";
	$where["prescription.object_id"] = $ds->prepareIn($sejour_ids);
	$line_ids = $line->loadIds($where, null, null, null, $join);
	
	// Prescriptions ex�cutables
	$query = new CRequest;
	$query->addSelect("DISTINCT prescription_id");
	$query->addTable("prescription_line_element");
	$query->addWhereClause("prescription_line_element_id", $ds->prepareIn($line_ids));
	$prescription_ids = $ds->loadColumn($query->getRequest());
	
	// S�jours planifiables
	$query = new CRequest;
	$query->addSelect("DISTINCT object_id");
	$query->addTable("prescription");
	$query->addWhereClause("prescription_id", $ds->prepareIn($prescription_ids));
	$sejour_ids = $ds->loadColumn($query->getRequest());
	
	$where = array();
	$where["sejour_id"] = $ds->prepareIn($sejour_ids);
	$join = array();
	$join["patients"]  = "patients.patient_id = sejour.patient_id";
	$order = "nom, prenom";
	
	if ($mode == "count") {
	  $counts["plannable"] = $sejour->countList($where, $order, null, null, $join);
	} 
	else {
	  $sejours = $sejour->loadList($where, $order, null, null, $join);
	}
}

// Mode count
if ($mode == "count") {
	$smarty = new CSmartyDP();
	$smarty->assign("counts", $counts);
	$smarty->assign("hide_noevents", $hide_noevents);
	$smarty->display("inc_board_sejours.tpl");
	return;
}

// Chargement des d�tails affich�s de chaque s�jour
CMbObject::massLoadFwdRef($sejours, "patient_id");
foreach ($sejours as $_sejour) {
  $_sejour->countEvenementsSSRWeek($kine_id, $planning->date_min, $planning->date_max);
	if ($hide_noevents && !$_sejour->_count_evenements_ssr_week) {
		unset($sejours[$_sejour->_id]);
		continue;
	}
  $_sejour->loadRefPatient();
	
  // Modification des prescription
  $_sejour->loadRefPrescriptionSejour();
  $_sejour->_ref_prescription_sejour->loadRefsLinesElementByCat();
  $_sejour->_ref_prescription_sejour->countRecentModif();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("mode", $mode);
$smarty->display("inc_board_list_sejours.tpl");
?>