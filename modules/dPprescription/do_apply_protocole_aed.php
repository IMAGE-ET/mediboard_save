<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


global $AppUI, $can;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id    = mbGetValueFromPost("protocole_id");
$date_sel        = mbGetValueFromPost("debut", mbDate());
$praticien_id    = mbGetValueFromPost("praticien_id", $AppUI->user_id);
$operation_id    = mbGetValueFromPost("operation_id");

if(!$protocole_id){
	exit();
}

// Chargement de la prescription
$prescription = new CPrescription();
if ($prescription_id) {
  $prescription->load($prescription_id);
} else {
  $operation = new COperation();
  $operation->load($operation_id);
  $prescription->object_class = 'CSejour';
  $prescription->object_id = $operation->sejour_id;
  $prescription->type = 'sejour';
  if ($msg = $prescription->store()) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
  }
}
$prescription->applyProtocole($protocole_id, $praticien_id, $date_sel, $operation_id);

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id)</script>";
echo $AppUI->getMsg();

exit();  

?>