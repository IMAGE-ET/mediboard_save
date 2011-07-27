<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

$op    = CValue::getOrSession("op");
$date  = CValue::getOrSession("date", mbDate());

$consultAnesth  = new CConsultAnesth;
$consult        = new CConsultation;
$userSel        = new CMediusers;

if($op) {
  $where = array();
  $where["operation_id"] = $ds->prepare("= %", $op);

  if($consultAnesth->loadObject($where)){
    $consultAnesth->loadRefConsultation();
    $consult = $consultAnesth->_ref_consultation;
    $consult->_ref_consult_anesth = $consultAnesth;
    
    $consult->loadRefPlageConsult();
    $consult->loadRefsDocItems();
    $consult->loadRefPatient();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    $consult_anesth =& $consult->_ref_consult_anesth;
    
    $consult_anesth->loadRefs();
    
    // On charge le praticien
    $userSel->load($prat_id);
    $userSel->loadRefs();
  }
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("op"             , $op);
$smarty->assign("date"           , $date);
$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult->_ref_consult_anesth);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("techniquesComp" , new CTechniqueComp());

$smarty->display("vw_anesthesie.tpl");
?>