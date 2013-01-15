<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 17164 $
 */

$factureconsult_id  = CValue::get("facture_id");
$patient_id         = CValue::get("patient_id");

//Patient s�lectionn�
$patient = new CPatient();
$patient->load($patient_id);
$patient->loadRefsCorrespondantsPatient();

$facture = new CFactureConsult();
$facture->load($factureconsult_id);  
$facture->loadRefs();
$facture->_ref_patient->loadRefsCorrespondantsPatient();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient"       , $patient);
$smarty->assign("facture"       , $facture);

$smarty->display("inc_vw_assurances.tpl");
?>