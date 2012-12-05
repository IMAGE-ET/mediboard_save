<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$service_id = CValue::getOrSession('service_id');
$all_stocks = CValue::getOrSession('all_stocks') == 'true';

$date_min = CValue::getOrSession('_date_min');
$date_max = CValue::getOrSession('_date_max');
CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

$destockages = array();

// Chargement de toutes les administrations dans la periode donnee
$administration = new CAdministration();
$where = array();
$where["dateTime"] = "BETWEEN '$date_min' AND '$date_max'"; 
$where["object_class"] = " = 'CPrescriptionLineMedicament'";
$administrations = $administration->loadList($where);

// Calcul des quantit�s administr�es
foreach($administrations as $_administration){
  $_administration->loadTargetObject();	
	$line =& $_administration->_ref_object;
  $code_cip = $line->code_cip;
	if(!isset($destockages[$code_cip]["quantite"])) {
		$destockages[$code_cip]["quantite"] = 0;
	}
  $destockages[$code_cip]["quantite"] += $_administration->quantite;
  // Tableaux de cache de medicaments
  if(!isset($medicaments[$code_cip])){
    $medicaments[$code_cip] =& $line->_ref_produit;
  }
}

// Calcul du nombre de boites correspondant aux administrations
foreach($destockages as $code_cip => $_destockage){
  $medicament = $medicaments[$code_cip];
  //$presentation = $destockages[$code_cip]["quantite"]/$medicament->nb_unite_presentation/$medicament->nb_presentation;
  $presentation = $destockages[$code_cip]["quantite"];
  
  if (!isset($destockages[$code_cip]["nb_produit"])){
    $destockages[$code_cip]["nb_produit"] = 0;
  }
  $destockages[$code_cip]["nb_produit"] = $presentation; 
  
  if(strstr($destockages[$code_cip]["nb_produit"], '.')){
    $destockages[$code_cip]["nb_produit"] = ceil($destockages[$code_cip]["nb_produit"]);
  }
  
  $destockages[$code_cip]["stock"] = CProductStockService::getFromCode($code_cip, $service_id);
  if ($destockages[$code_cip]["stock"]) {
    $destockages[$code_cip]["stock"]->quantity -= $destockages[$code_cip]["nb_produit"];
    
    $stock = $destockages[$code_cip]["stock"];
    $log = new CUserLog();
    $where = array();
    $order = "date DESC";
    $where["object_id"] = " = '$stock->_id'";
    $where["object_class"] = " = '$stock->_class'";
    $where["date"] = " BETWEEN '$date_min' AND '$date_max'";
    $where["fields"] = " LIKE '%quantity%'";
    
    $destockages[$code_cip]["stock"]->_ref_logs = $log->loadList($where, $order);
    foreach ($destockages[$code_cip]["stock"]->_ref_logs as $log) {
      $log->loadRefsFwd();
    }
  } else {
    $product = new CProduct();
    $product->code = $code_cip;
    $product->category_id = CAppUI::conf('bcb CBcbProduitLivretTherapeutique product_category_id');
    if ($product->loadMatchingObject()) {
      $stock = new CProductStockService();
      $stock->object_id = $service_id;
      $stock->object_class = "CService"; // XXX
      $stock->product_id = $product->_id;
      $stock->store();
      $destockages[$code_cip]['stock'] = $stock;
    } 
    else {
      $destockages[$code_cip]['stock'] = null;
    }
  }
}

if ($all_stocks) {
  $stock = new CProductStockService();
  $stock->object_id = $service_id;
  $stock->object_class = "CService"; // XXX
  $list_stocks = $stock->loadMatchingList();

  foreach ($list_stocks as $sto) {
    $sto->loadRefsFwd();
    $already = false;
    foreach ($destockages as $code => $desto) {
      if ($sto->_ref_product->code == $code) {
        $already = true;
      }
    }
    if (!$already) {
      $destockages[$sto->_ref_product->code]['stock'] = $sto;
      $destockages[$sto->_ref_product->code]['nb_produit'] = $stock->quantity;
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('destockages', $destockages);
$smarty->display('inc_stock_inventory.tpl');

?>