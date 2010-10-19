<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$user = new CMediusers();
$user->load($AppUI->user_id);

if(!$user->isPraticien()) {
  $can->needsRead();
}

$sejour_id = CValue::get("sejour_id", 0);
$user_id = CValue::get("user_id");
$cible = CValue::get("cible");


$observation  = new CObservationMedicale();
$transmission = new CTransmissionMedicale();

$observation->loadAides($AppUI->user_id);
$transmission->loadAides($AppUI->user_id);

// Chargement du sejour
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadSuiviMedical();
$sejour->loadRefPraticien();

$sejour->loadRefPrescriptionSejour();
$prescription =& $sejour->_ref_prescription_sejour;

$cibles = array();
$users = array();
foreach($sejour->_ref_suivi_medical as $_trans_or_obs) {
  $users[$_trans_or_obs->user_id] = $_trans_or_obs->_ref_user;
  $type = ($_trans_or_obs instanceof CObservationMedicale) ? "obs" : "trans";
  if($user_id && $_trans_or_obs->user_id != $user_id){
    unset($sejour->_ref_suivi_medical[$_trans_or_obs->date.$_trans_or_obs->_id.$type]);
  }
	
  $_trans_or_obs->loadRefUser();
  if($_trans_or_obs instanceof CTransmissionMedicale) {
    $trans = $_trans_or_obs;
    $trans->calculCibles($cibles);
		if ($cible && $_trans_or_obs->_cible != $cible){
			unset($sejour->_ref_suivi_medical[$_trans_or_obs->date.$_trans_or_obs->_id.$type]);
		}
  }
  $_trans_or_obs->canEdit();
}

//TODO: Revoir l'ajout des constantes dans le suivi de soins 
//Ajout des constantes
if(!$cible && CAppUI::conf("soins constantes_show")){
  $sejour->loadRefConstantes($user_id);
}

//mettre les transmissions dans un tableau dont l'index est le datetime 
$list_trans_const = array();
foreach($sejour->_ref_suivi_medical as $_trans_const) {
	if($_trans_const instanceof CConstantesMedicales) {
		$list_trans_const["$_trans_const->datetime $_trans_const->_guid"] = $_trans_const;
	}
	else {
		$list_trans_const["$_trans_const->date $_trans_const->_guid"] = $_trans_const;
	}
}

if($sejour->type == "urg" && CAppUI::conf("dPprescription CPrescription prescription_suivi_soins")){
	// Chargement des lignes de prescriptions d'elements
	$prescription->loadRefsLinesElement();
	
	$prescription->loadRefsLinesAllComments();
	
	foreach($prescription->_ref_prescription_lines_all_comments as $_comment){
		$_comment->canEdit();
		$_comment->countBackRefs("transmissions");
		$list_trans_const["$_comment->debut $_comment->time_debut $_comment->_guid"] = $_comment;
	}
  
	// Ajout des lignes de prescription dans la liste du suivi de soins
	foreach($prescription->_ref_prescription_lines_element as $_line_element){
		if(!$user_id || $_line_element->praticien_id == $user_id){
		  $_line_element->canEdit();
			$_line_element->countBackRefs("transmissions");
		  $list_trans_const["$_line_element->debut $_line_element->time_debut $_line_element->_guid"] = $_line_element;
		}
	}
}

krsort($list_trans_const);

$count_trans = count($list_trans_const);
$sejour->_ref_suivi_medical = $list_trans_const;
// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("params"              , CConstantesMedicales::$list_constantes);
$smarty->assign("page_step"           , 10);
$smarty->assign("readOnly"            , CValue::get("readOnly",false));
$smarty->assign("count_trans"         , $count_trans);
$smarty->assign("observation"         , $observation);
$smarty->assign("transmission"        , $transmission);
$smarty->assign("user"                , $user);
$smarty->assign("isPraticien"         , $user->isPraticien());
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("cibles"              , $cibles);
$smarty->assign("cible"               , $cible);
$smarty->assign("users"               , $users);
$smarty->assign("user_id"             , $user_id);
$smarty->assign("date"                , mbDate());
$smarty->assign("hour"                , mbTransformTime(null, mbTime(), "%H"));
$smarty->display("inc_vw_dossier_suivi.tpl");

?>