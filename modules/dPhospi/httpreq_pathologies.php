<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;


$date = mbGetValueFromGetOrSession("date", mbDate()); 
$pathos = new CDiscipline();

// Recuperation de l'id du sejour
$sejour_id = mbGetValueFromGet("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPraticien();
$sejour->_ref_praticien->loadRefFunction();
$sejour->loadRefPatient();
    
$sejour->loadRefsOperations();
foreach($sejour->_ref_operations as &$operation) {
  $operation->loadRefsCodesCCAM();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("pathos",$pathos);
$smarty->assign("date" , $date);
$smarty->assign("curr_sejour" , $sejour);
$smarty->display("inc_pathologies.tpl");

?>