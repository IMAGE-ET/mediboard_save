<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$operation_id  = CValue::getOrSession("operation_id", null);

$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefsAnesthPerops();
$operation->loadRefsFwd();
$operation->loadRefsActesCCAM();
foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
  $acte =& $operation->_ref_actes_ccam[$keyActe];
  $acte->loadRefsFwd();
  $acte->guessAssociation();
}  
$sejour =& $operation->_ref_sejour;
$sejour->loadRefsFwd();
$sejour->loadRefPrescriptionSejour();

$prescription_id = $sejour->_ref_prescription_sejour->_id;

$administration = new CAdministration();
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMedicament'";
$ljoin["prescription_line_element"] = "prescription_line_element.prescription_line_element_id = administration.object_id AND administration.object_class = 'CPrescriptionLineElement'";
$ljoin["prescription_line_mix_item"] = "prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMixItem'";
$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";
                                                                                       
$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
                          (prescription_line_mix.prescription_id = prescription.prescription_id)";

$where["prescription.prescription_id"] = " = '$prescription_id'";

$where[] = "prescription_line_medicament.perop = '1' OR 
            prescription_line_element.perop = '1' OR
            prescription_line_mix.perop = '1'";
    
$administrations = $administration->loadList($where, null, null, null, $ljoin);

// Chargement des constantes saisies durant l'intervention
$whereConst = array();
$whereConst["datetime"] = "BETWEEN '$operation->_datetime_reel' AND '$operation->_datetime_reel_fin'";

$sejour->loadListConstantesMedicales($whereConst);  
  
// Tri des gestes et administrations perop par ordre chronologique
$perops = array();
foreach($administrations as $_administration){
  $_administration->loadRefsFwd();
  $perops[$_administration->dateTime][$_administration->_guid] = $_administration;
}
foreach($operation->_ref_anesth_perops as $_perop){
  $perops[$_perop->datetime][$_perop->_guid] = $_perop;
}

$constantes = array("pouls", "ta", "frequence_respiratoire", "score_sedation", "spo2", "diurese");
foreach ($sejour->_list_constantes_medicales as $_constante_medicale) {
  foreach ($constantes as $_constante) {
    $perops[$_constante_medicale->datetime][$_constante_medicale->_guid][$_constante] = $_constante_medicale->$_constante;
  }
}

ksort($perops);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient"  , $operation->_ref_sejour->_ref_patient);
$smarty->assign("operation", $operation);
$smarty->assign("perops"   , $perops);

$smarty->display("print_feuille_bloc.tpl");

?>