<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
//Initialisations des variables
$cabinet_id   = mbGetValueFromGetOrSession("cabinet_id", $mediuser->function_id);
$date         = mbGetValueFromGetOrSession("date", mbDate());
$today        = mbDate();
$hour         = mbTime(null);
$board        = mbGetValueFromGet("board", 1);
$boardItem    = mbGetValueFromGet("boardItem", 1);
$consult      = new CConsultation;


// R�cup�ration des fonctions

$cabinets = CMediusers::loadFonctions(PERM_EDIT, $g, "cabinet");

// R�cup�ration de la liste des praticiens
$praticiens = array();
if($cabinet_id) {
  $praticiens = $mediuser->loadPraticiens(PERM_READ,$cabinet_id);
}

if($consult->consultation_id) {
  $date = $consult->_ref_plageconsult->date;
  mbSetValueToSession("date", $date);
}


// R�cup�ration des plages de consultation du jour et chargement des r�f�rences
$listPlages = array();
foreach($praticiens as $keyPrat => $prat) {
  $listPlage = new CPlageconsult();
  $where = array();
  $where["chir_id"] = "= '$prat->_id'";
  $where["date"] = "= '$date'";
  $order = "debut";
  $listPlage = $listPlage->loadList($where, $order);
  if(!count($listPlage)) {
    unset($praticiens[$keyPrat]);
  } else {
    $listPlages[$prat->_id]["prat"] = $prat;
    $listPlages[$prat->_id]["plages"] = $listPlage;
  }
}

foreach($listPlages as &$element) {
  foreach ($element["plages"] as &$plage) {
    $plage->_ref_chir =& $element["prat"];
    $plage->loadRefsBack();
    foreach ($plage->_ref_consultations as $keyConsult => &$consultation) {
      /*
      if (($consultation->chrono == CConsultation::TERMINE)) {
        unset($plage->_ref_consultations[$keyConsult]);
        continue;
      }
      */
      $consultation->loadRefPatient();
      $consultation->loadRefCategorie();
      $consultation->getNumDocsAndFiles();
    }
  }
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("cabinet_id"    ,$cabinet_id);
$smarty->assign("consult"       ,$consult);
$smarty->assign("listPlages"    ,$listPlages);
$smarty->assign("date"          ,$date);
$smarty->assign("today"         ,$today);
$smarty->assign("hour"          ,$hour);
$smarty->assign("praticiens"    ,$praticiens);
$smarty->assign("cabinets"      ,$cabinets);
$smarty->assign("board"         ,$board);
$smarty->assign("boardItem"     ,$boardItem);
$smarty->assign("canCabinet"    ,CModule::getCanDo("dPcabinet"));

$smarty->display("vw_journee.tpl");


?>