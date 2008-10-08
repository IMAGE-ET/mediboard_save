<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$praticien_id = mbGetValueFromGet("praticien_id");
$function_id  = mbGetValueFromGet("function_id");
$group_id     = mbGetValueFromGet("group_id");
$protocoleSel_id = mbGetValueFromGet("protocoleSel_id");

$protocoles = array();
$protocole = new CPrescription();

if(!$function_id && !$praticien_id && $protocoleSel_id){
  $protocole->load($protocoleSel_id);
  $praticien_id = $protocole->praticien_id;	
  $function_id = $protocole->function_id;
}

$protocoles = CPrescription::loadAllProtocolesFor($praticien_id, $function_id, $group_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles", $protocoles);
$smarty->assign("protocoleSel_id", $protocoleSel_id);

$smarty->display("inc_vw_list_protocoles.tpl");

?>