<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$ds = CSQLDataSource::get("std");

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$filter->_date_max = mbGetValueFromGetOrSession("_date_max", mbDate());

$chir_id = mbGetValueFromGetOrSession("chir", null);
$chirSel = new CMediusers;
$chirSel->load($chir_id);
$listPrat = $chirSel->loadPraticiens(PERM_EDIT);

$listConsults = array();

$consult = new CConsultation();

$where = array();
$ljoin["plageconsult"]                      = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where["consultation.du_tiers"]             = "> 0";
$where["consultation.tiers_date_reglement"] = "IS NULL";
$where["plageconsult.date"]                 = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
$where["plageconsult.chir_id"]              = $ds->prepareIn(array_keys($listPrat), $chir_id);
$order = "plageconsult.date";

$listConsults = $consult->loadList($where, $order, null, null, $ljoin);

$total = array("nb" => 0, "value" => 0);

foreach($listConsults as $key => &$consult) {
  $consult->loadRefsFwd();
  $consult->loadRefsReglements();
  $consult->loadIdsFSE();
  $consult->_new_tiers_reglement = new CReglement();
  $consult->_new_tiers_reglement->mode = "virement";
  $consult->_new_tiers_reglement->montant = $consult->_du_tiers_restant;
  $hasNoemie = (!$consult->_current_fse || $consult->_current_fse->S_FSE_ETAT != 9);
  if(!$hasNoemie) {
    $_POST["consultation_id"] = $consult->_id;
    $_POST["montant"]         = $consult->_du_tiers_restant;
    $do = new CDoObjectAddEdit("CReglement", "reglement_id");
	  $do->redirect = null;
    $do->doIt();
  }
}

// Redirection finale
$do->redirect = "m=$m&a=print_noemie&dialog=1";
$do->doRedirect();

?>