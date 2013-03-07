<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Alexis Granger
*/

global $can, $m, $g;

$can->needsAdmin();

// Last update
$today = CMbDT::dateTime();

// Chargement des praticiens de l'�tablissement
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();
$listPraticiens = array();

foreach($praticiens as $key=>$praticien) {
  $listPraticiens[$key]["prat"] = $praticien;
  $praticien->loadLastId400("labo code4");
  $listPraticiens[$key]["code4"]= $praticien->_ref_last_id400;
  $praticien->loadLastId400("labo code9");
  $listPraticiens[$key]["code9"]= $praticien->_ref_last_id400;
}




// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("today"     , $today);
$smarty->assign("listPraticiens", $listPraticiens);

$smarty->display("vw_edit_idLabo.tpl");
?>