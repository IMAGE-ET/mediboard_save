<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision:  $
* @author
*/

$object_guid = CValue::get("object_guid");

// Chargement du patient
$patient = CMbObject::loadFromGuid($object_guid);

// Chargement de son dossier m�dical
$patient->loadRefDossierMedical();
$dossier_medical =& $patient->_ref_dossier_medical;

// Chargement des allergies   
$dossier_medical->loadRefsAllergies();

$smarty = new CSmartyDP();
$smarty->assign("allergies", $dossier_medical->_ref_allergies);
$smarty->display("inc_vw_allergies.tpl");

?>