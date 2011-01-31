<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", mbDate());
$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefBilanSSR();
$sejour->loadRefPatient();
$sejour->loadRefPraticien();
$bilan_ssr =& $sejour->_ref_bilan_ssr;
$bilan_ssr->loadRefTechnicien();
$technicien =& $bilan_ssr->_ref_technicien;
$technicien->loadRefKine();

// Chargement des evenement SSR 
$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$sejour_id'";
$where["debut"] = "BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";
$evenements = $evenement_ssr->loadList($where);

$elements = array();
$intervenants = array();
foreach($evenements as $_evenement){
	$_evenement->loadRefPrescriptionLineElement();
	$_evenement->loadRefTherapeute();

  $element_prescription =& $_evenement->_ref_prescription_line_element->_ref_element_prescription;

  $elements[$element_prescription->_id] = $element_prescription;
	$intervenants[$element_prescription->_id][$_evenement->therapeute_id] = $_evenement->_ref_therapeute;
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("elements", $elements);
$smarty->assign("intervenants", $intervenants);
$smarty->assign("sejour", $sejour);
$smarty->display("print_planning_sejour.tpl");

?>