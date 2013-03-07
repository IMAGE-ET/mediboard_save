<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());

// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadGroupList();

$totalOp = 0;

$counts = array (
  "operations" => array (
	  "total" => 0,
    "facturees" => 0,
	),
  "urgences" => array (
    "total" => 0,
    "facturees" => 0,
	),
);

$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order = "debut";
$plages = $plages->loadList($where, $order);
$_operation = new COperation();
foreach($plages as &$_plage) {
  $_plage->loadRefsOperations(0);
  foreach($_plage->_ref_operations as $_operation) {
    $_operation->loadRefChir(1);
    $_operation->_ref_chir->loadRefFunction();
    $_operation->loadRefSejour();
    $_operation->_ref_sejour->loadNDA();
    $_operation->_ref_sejour->countDocItems();
    $_operation->_ref_sejour->loadRefPatient();
    $_operation->_ref_sejour->_ref_patient->loadIPP();
    $_operation->loadExtCodesCCAM();
    $_operation->countExchanges();
    $_operation->countDocItems();

		$counts["operations"]["total"]++; 
		if (count($_operation->_nb_exchanges)) {
      $counts["operations"]["facturees"]++; 
		}
  }
  $totalOp += count($_plage->_ref_operations);
}

$operation = new COperation;
$where = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where["operations.date"]       = "= '$date'";
$where["operations.plageop_id"] = "IS NULL";
$where["operations.annulee"]    = "= '0'";
$where["sejour.group_id"]       = "= '".CGroups::loadCurrent()->_id."'";
$order = "operations.chir_id";
$urgences = $operation->loadList($where, $order, null, null, $ljoin);
$totalOp += count($urgences);
foreach($urgences as $_urgence) {
  $_urgence->loadRefChir(1);
  $_urgence->_ref_chir->loadRefFunction();
  $_urgence->loadRefSejour();
  $_urgence->_ref_sejour->loadNDA();
  $_urgence->_ref_sejour->loadRefPatient();
  $_urgence->_ref_sejour->_ref_patient->loadIPP();
  $_urgence->loadExtCodesCCAM();
  $_urgence->countExchanges();
	
  $counts["urgences"]["total"]++; 
  if (count($_urgence->_nb_exchanges)) {
    $counts["urgences"]["facturees"]++; 
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("operation", $operation);
$smarty->assign("plages"   , $plages);
$smarty->assign("urgences" , $urgences);
$smarty->assign("counts"   , $counts);
$smarty->assign("totalOp"  , $totalOp);

$smarty->display("vw_list_interv.tpl");

?>
