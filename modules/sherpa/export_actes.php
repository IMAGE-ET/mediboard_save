<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());

// Chargement des s�jours concern�s
$where = array();
$where["type"] = "NOT IN ('exte', 'urg')";
$where["sortie_reelle"] = "LIKE '$date%'";
$order = "entree_reelle, sortie_reelle";
$sejour = new CSejour();
$sejours = $sejour->loadList($where, $order);

global $detCCAM; $detCCAM = array();

// Associations entre actes CCAM Mediboard et les d�tails CCAM Sherpa
function exportDetCCAM(CActeCCAM &$acte_ccam, $idinterv) {
  global $detCCAM;
  
  $spDetCCAM = new CSpDetCCAM();
  $spDetCCAM->makeId();
  $spDetCCAM->idinterv = $idinterv;
  $spDetCCAM->mapFrom($acte_ccam);
  $spDetCCAM->getCurrentDataSource();
  $detCCAM[$acte_ccam->_id] = $spDetCCAM->store();
}

global $entCCAM; $entCCAM = array();

// Associations entre codable Mediboard et les ent�tes CCAM Sherpa
function exportEntCCAM(CCodable &$codable) {
  global $entCCAM;
  
  if (!count($codable->_ref_actes)) {
    return;
  }
  
  $spEntCCAM = new CSpEntCCAM();
  $spEntCCAM->makeId($codable);
  $spEntCCAM->mapFrom($codable);
  $spEntCCAM->getCurrentDataSource();
  $entCCAM[$codable->_id] = $spEntCCAM->store();
  
  foreach ($codable->_ref_actes_ccam as &$acte_ccam) {
    exportDetCCAM($acte_ccam, $spEntCCAM->_id);
  }
  
}

$delDetCCAM = array();
$delEntCCAM = array();

foreach ($sejours as &$sejour) {
  // Suppression des actes
  $sejour->loadNumDossier();
  
  // Suppression des anciens ent�tes
  $spDetCCAM = new CSpDetCCAM();
  $delDetCCAM[$sejour->_id] = $spDetCCAM->deleteForDossier($sejour->_num_dossier);
  
  // Suppression des anciens d�tails
  $spEntCCAM = new CSpEntCCAM();
  $delEntCCAM[$sejour->_id] = $spEntCCAM->deleteForDossier($sejour->_num_dossier);
  
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  
  // Actes du s�jour
  $sejour->loadRefsActes();
  exportEntCCAM($sejour);
  
  // Op�rations
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as &$operation) {
    $operation->_ref_sejour =& $sejour;
    $operation->loadRefChir();
    $operation->loadRefsActes();
    exportEntCCAM($operation);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("acte_ccam", new CActeCCAM());
$smarty->assign("sejours", $sejours);
$smarty->assign("delDetCCAM", $delDetCCAM);
$smarty->assign("delEntCCAM", $delEntCCAM);
$smarty->assign("detCCAM", $detCCAM);
$smarty->assign("entCCAM", $entCCAM);

$smarty->display("export_actes.tpl");
?>