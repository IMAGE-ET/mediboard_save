<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m, $g, $dPconfig;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
$id400 = new CIdSante400;
$id400->loadLatestFor($patient);
$patient400 = $id400->id400;

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("patient"   , $patient);
$smarty->assign("patient400", $patient400);
$smarty->assign("url"       , $dPconfig["dPImeds"]["url"]);

$smarty->display("inc_patient_results.tpl");