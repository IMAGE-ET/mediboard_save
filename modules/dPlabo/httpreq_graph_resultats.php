<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $can;

$can->needsRead();

// Chargement de l'item choisi
$prescriptionItem = new CPrescriptionLaboExamen;
$prescriptionItem->load(mbGetValueFromGetOrSession("prescription_labo_examen_id"));
if($prescriptionItem->loadRefs()) {
  $prescriptionItem->_ref_prescription_labo->loadRefsFwd();
}

$siblingItems = $prescriptionItem->loadSiblings();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("prescriptionItem", $prescriptionItem);
$smarty->assign("siblingItems", $siblingItems);
$smarty->assign("time", time());



$smarty->display("inc_graph_resultats.tpl");
?>
