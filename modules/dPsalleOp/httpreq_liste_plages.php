<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author S�bastien Fillonneau
 */


CCanDo::checkRead();

$salle_id     = CValue::getOrSession("salle");
$bloc_id      = CValue::getOrSession("bloc_id");
$date         = CValue::getOrSession("date", mbDate());
$operation_id = CValue::getOrSession("operation_id");
$hide_finished = CValue::getOrSession("hide_finished", 0);

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);


// Selection des plages op�ratoires de la journ�e
$salle = new CSalle;
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date);
}

if ($hide_finished == 1 && $salle->_ref_plages) {
  foreach($salle->_ref_plages as &$plage) {
    foreach($plage->_ref_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_ref_operations[$key]);
    }
    foreach($plage->_unordered_operations as $key => $op){
      if ($op->sortie_salle) unset($plage->_unordered_operations[$key]);
    }
  }
  
  foreach($salle->_ref_deplacees as $key => $op){
    if ($op->sortie_salle) unset($salle->_ref_deplacees[$key]);
  }
  
  foreach($salle->_ref_urgences as $key => $op){
    if ($op->sortie_salle) unset($salle->_ref_urgences[$key]);
  }
}

// Calcul du nombre d'actes cod� dans les interventions
if($salle->_ref_plages){
	foreach($salle->_ref_plages as $_plageop){
		$_plageop->loadRefsNotes();
		foreach($_plageop->_ref_operations as $_operation){
			$_operation->countActes();
		}
		foreach($_plageop->_unordered_operations as $_operation){
	    $_operation->countActes();
	  }
	}
}
if($salle->_ref_deplacees){
	foreach($salle->_ref_deplacees as $_operation){
	  $_operation->countActes();
	}  
}
if($salle->_ref_urgences){
	foreach($salle->_ref_urgences as $_operation){
	  $_operation->countActes();
	}
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("salle"         , $salle       );
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien_id"  , null         );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_plages.tpl");
?>