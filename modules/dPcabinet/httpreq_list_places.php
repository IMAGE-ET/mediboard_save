<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
// Initialisation des variables
$plageconsult_id = CValue::get("plageconsult_id");

// R�cup�ration des consultations de la plage s�l�ctionn�e
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date  = $plage->date;
} else {
  $date  = CValue::get("date", mbDate());
}

// Chargement des places disponibles
$listPlace = array();
$listBefore  = array();
$listAfter   = array();
if ($plageconsult_id) {
  if (!$plage->plageconsult_id) {
    $plage->load($plageconsult_id);
  }
  $plage->loadRefs(false);
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $listPlace[$i]["time"] = mbTime("+ $minutes minutes", $plage->debut);
    $listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient();
    // Chargement de la categorie
    $consultation->loadRefCategorie();
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
  
    if($keyPlace < 0) {
      $listBefore[$keyPlace] =& $consultation;
    }
    
    if($consultation->heure >= $plage->fin) {
      $listAfter[$keyPlace] =& $consultation;
    }
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($listPlace[($keyPlace + $i)])) {
        $listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlace"      , $listPlace);
$smarty->assign("listBefore"     , $listBefore);
$smarty->assign("listAfter"      , $listAfter);
$smarty->assign("online"         , true);

$smarty->display("inc_list_places.tpl");

?>