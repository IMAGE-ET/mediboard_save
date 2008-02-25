<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can;
$can->needsEdit();

$numdos = mbGetValueFromGet("numdos");
$sejour = CSpObjectHandler::getMbObjectFor("CSejour", $numdos);
$sejour->loadRefPatient();

if ($numdos && !$sejour->_id) {
  trigger_error("S�jour avec le num�ro de dossier '$numdos' introuvable dans l'�tablissement", E_USER_WARNING);
}

if (isset($_GET["actes"])) {
	foreach ($_GET["actes"] as $idinterv => $actes) {
	  $operation = CSpObjectHandler::getMbObjectFor("COperation", $idinterv);
	
	  if (!$operation->_id) {
		  trigger_error("Operation avec l'identifiant '$idinterv' introuvable dans l'�tablissement", E_USER_WARNING);
		  continue;
		}
	
		if ($operation->sejour_id != $sejour->_id) {
		  trigger_error("Operation avec l'identifiant '$idinterv' non associ� au s�jour num�ro '$numdos'", E_USER_WARNING);
		  continue;
		}
		
		$operation->loadRefPlageOp();
		
		$sejour->_ref_operations[$operation->_id] = $operation;
		
		// Ajout des actes � importer
	  foreach ($actes as $tokenCCAM) {
	    $acte = CSpDetCCAM::mapFromToken($operation, $tokenCCAM);
	    $operation->_ref_actes_ccam[] = $acte;
	  }
	}
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->display("import_actes.tpl");
?>