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
$_selected_cis   = CValue::get("_selected_cis");

// Calcul de date_max et date_min
$date_min = CValue::get('_date_min');
$date_max = CValue::get('_date_max');

if (!$date_min) {
  $date_min = CValue::session('_date_delivrance_min');
}
if (!$date_max) {
  $date_max = CValue::session('_date_delivrance_max');
}

CValue::setSession('_date_delivrance_min', $date_min);
CValue::setSession('_date_delivrance_max', $date_max);

$date_min_orig = $date_min;
$date_max_orig = $date_max;

// Recherche des prescriptions dont les dates de sejours correspondent
$where = array();
$ljoin = array();
$ljoin['sejour'] = 'prescription.object_id = sejour.sejour_id';
$ljoin['affectation'] = 'sejour.sejour_id = affectation.sejour_id';
$ljoin['lit'] = 'affectation.lit_id = lit.lit_id';
$ljoin['chambre'] = 'lit.chambre_id = chambre.chambre_id';
$ljoin['service'] = 'chambre.service_id = service.service_id';
$where['prescription.type'] = " = 'sejour'";
$where[] = "(sejour.entree_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR 
            (sejour.sortie_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR
            (sejour.entree_prevue <= '$date_min 00:00:00' AND sejour.sortie_prevue >= '$date_max 23:59:59')"; 
$where['service.service_id'] = " = '$service_id'";

$dispensations = array();
$delivrances = array();
$prescriptions = array();
$produits_cis = array();
$produits_cip = array();

$stocks = array();
$quantites_reference = array();
$quantites = array();
$done = array();
$besoin_patients = array();
$stocks_service = array();
$stocks_pharmacie = array();
$besoin_patient = array();
$done_nominatif = array();
$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
$_lines = array();
$correction_dispensation = array();

// Creation du tableau de dates
$dates = array();
if($date_max_orig != $date_min_orig){
	$date = $date_min_orig;
	while($date <= $date_max_orig){
	  $dates[] = $date;
	  $date = mbDate("+ 1 DAY", $date);
	}
} else {
  $dates[] = $date_min_orig; 
}

if($prescriptions) {
  foreach($prescriptions as $_prescription) {
    $date_min = $date_min_orig;
	  $date_max = $date_max_orig;
	  
	  // Stockage du sejour de la prescription
	  $sejour =& $_prescription->_ref_object;
	  if(!$sejour->_ref_patient){
     $sejour->loadRefPatient();
    }
    $patient =& $sejour->_ref_patient;

    // On borne les dates aux dates du sejour si besoin
	  $date_min = max($sejour->_entree, $date_min);
	  $date_max = min($sejour->_sortie, $date_max);

	  if ($date_min > $date_max) {
	    continue;
	  }

    $_prescription->loadRefsLinesMed("1","1");
	  $_prescription->loadRefsPrescriptionLineMixes();

	  // Calcul du plan de soin
    if(strlen($_selected_cis) == 8){
      // CIS
      $_prescription->calculPlanSoin($dates, 0, 0, 1, null, true, $_selected_cis);
    } else {
      // CIP
      $_prescription->calculPlanSoin($dates, 0, 0, 1, $_selected_cis, true);
    }

    $lines = array();
    $lines["medicament"] = $_prescription->_ref_prescription_lines;
		
    // Parcours des prises prevues pour les medicaments
     foreach($lines as $lines_by_type){
       foreach($lines_by_type as $_line_med){

          $_lines[$_line_med->code_cis] = $_line_med;
          if($_selected_cis && ($_line_med->code_cis != $_selected_cis) && ($_line_med->code_cip != $_selected_cis)){
            continue;
          }

          $_line_med->loadRefProduitPrescription();
          $code = $_line_med->code_cis ? $_line_med->code_cis : $_line_med->code_cip;

	 				if(!isset($besoin_patient[$code][$patient->_id])){
		        $besoins =& $besoin_patient[$code][$patient->_id];
		        $besoins = array("patient" => "", "quantite_administration" => 0, "quantite_dispensation" => 0);
		      }
		      if($_line_med->_quantity_by_date){
	          if(!isset($produits_cis[$code])){
	             $produits_cis[$code] = $_line_med;
	          }  
	          foreach($_line_med->_quantity_by_date as $type => $quantity_by_date){
					  	foreach($quantity_by_date as $date => $quantity_by_hour){
					  		if(@$quantity_by_hour['quantites']) { //FIXME: parfois cette valeur est vide
						  	  foreach($quantity_by_hour['quantites'] as $hour => $quantity){
						  	    if(!isset($dispensations[$code])){
						  	      $dispensations[$code]["quantite_administration"] = 0;
						  	      $dispensations[$code]["quantite_dispensation"] = 0;
						  	    }
						  	    $dispensations[$code]["quantite_administration"] += $quantity["total"];
										if($_line_med->_ref_produit_prescription->_id && ($_line_med->_ref_produit_prescription->unite_prise != $_line_med->_ref_produit_prescription->unite_dispensation)){
                      $quantity["total_disp"] = $quantity["total_disp"] / ($_line_med->_ref_produit_prescription->quantite * $_line_med->_ref_produit_prescription->nb_presentation);
                    }
							      $dispensations[$code]["quantite_dispensation"] += $quantity["total_disp"];
							      $besoin_patient[$code][$patient->_id]["patient"] = $patient; 
		  				      $besoin_patient[$code][$patient->_id]["quantite_administration"] += $quantity["total"];
							      $besoin_patient[$code][$patient->_id]["quantite_dispensation"] += $quantity["total_disp"];
						  	  }
					  		}
					  	}
		        }
	        }	
	       
		      // Gestion des prises planifiees
	        if($_line_med->_administrations){
			      foreach($_line_med->_administrations as $unite_prise => &$administrations_by_unite){
					    foreach($administrations_by_unite as $_date => &$administrations_by_date){
							  foreach($administrations_by_date as $_hour => &$administrations_by_hour){
								  if(is_numeric($_hour)){
			          		$quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
				            if($quantite_planifiee){
									    // Calcul de la quantite 
									    @$dispensations[$code]["quantite_administration"] += $quantite_planifiee;
											if($_line_med->_ref_produit_prescription->_id && ($_line_med->_ref_produit_prescription->unite_prise != $_line_med->_ref_produit_prescription->unite_dispensation)){
                        $quantite_dispensation = $quantite_planifiee / ($_line_med->_ref_produit_prescription->quantite * $_line_med->_ref_produit_prescription->nb_presentation);
                      } else {
										    $quantite_dispensation = $quantite_planifiee * $_line_med->_ratio_administration_dispensation; 
										  }
									    @$dispensations[$code]["quantite_dispensation"] += $quantite_dispensation;
								      $besoin_patient[$code][$patient->_id]["patient"] = $patient; 
								      $besoin_patient[$code][$patient->_id]["quantite_administration"] += $quantite_planifiee;
					            $besoin_patient[$code][$patient->_id]["quantite_dispensation"] += $quantite_dispensation;
				            }
			            }
			          }
			        }
			      }
	        }
	      }
     }   
	   if($_prescription->_ref_prescription_line_mixes_for_plan){
		  foreach($_prescription->_ref_prescription_line_mixes_for_plan as $_prescription_line_mix){
		    if(is_array($_prescription_line_mix->_prises_prevues)){
			    foreach($_prescription_line_mix->_prises_prevues as $_date => $_prises_by_hour){
			      foreach($_prises_by_hour as $_hour => $_prise){
			        foreach($_prescription_line_mix->_ref_lines as $_perf_line){   	
								$count_prises_by_hour = count($_prescription_line_mix->_prises_prevues[$_date][$_hour]["real_hour"]);
           
					     	$_perf_line->loadRefProduitPrescription();
								$code = $_perf_line->code_cis ? $_perf_line->code_cis : $_perf_line->code_cip;
								
			          $_lines[$code] = $_perf_line;
			                  
			          $datetime = "$_date $_hour:00:00";			          
			          if($datetime < "$date_min_orig 00:00:00" || $datetime > "$date_max_orig 23:59:59"){
			            continue;
			          }
			        	if(!isset($besoin_patient[$code][$patient->_id])){
					        $besoins =& $besoin_patient[$code][$patient->_id];
			            $besoins = array("patient" => "", "quantite_administration" => 0, "quantite_dispensation" => 0);
					      }
			          if(!isset($produits_cis[$code])){
			           $produits_cis[$code] = $_perf_line;
			          }      
			          if(!isset($dispensations[$code])){
			            $dispensations[$code]["quantite_administration"] = 0;
			            $dispensations[$code]["quantite_dispensation"] = 0;
			          }
			          $dispensations[$code]["quantite_administration"] += ($_perf_line->_quantite_administration * $count_prises_by_hour);
								
						    if($_perf_line->_ref_produit_prescription->_id && ($_perf_line->_ref_produit_prescription->unite_prise != $_perf_line->_ref_produit_prescription->unite_dispensation)){
                  $quantite_dispensation = $_perf_line->_quantite_dispensation / ($_perf_line->_ref_produit_prescription->quantite * $_perf_line->_ref_produit_prescription->nb_presentation);
                } else {
                	$quantite_dispensation = $_perf_line->_quantite_dispensation;
                }
										
			          $dispensations[$code]["quantite_dispensation"] += ($quantite_dispensation * $count_prises_by_hour);
					      $besoin_patient[$code][$patient->_id]["patient"] = $patient; 
	 				      $besoin_patient[$code][$patient->_id]["quantite_administration"] += ($_perf_line->_quantite_administration * $count_prises_by_hour);
					      $besoin_patient[$code][$patient->_id]["quantite_dispensation"] += ($quantite_dispensation * $count_prises_by_hour);
	
			        }
			      }
			    }
		    }
		  }
	  }
  }
}  

foreach($besoin_patient as &$quantites_by_patient){
  foreach($quantites_by_patient as $patient_id => &$quantites){
	  if(strstr($quantites["quantite_administration"],'.')){
      //$quantites["quantite_administration"] = ceil($quantites["quantite_administration"]);
	  } 
	  if(strstr($quantites["quantite_dispensation"],'.')){
	    $quantites["quantite_dispensation"] = ceil($quantites["quantite_dispensation"]);
	  }
	}
}

$category_id = CAppUI::conf('bcb CBcbProduitLivretTherapeutique product_category_id');

// Calcul du nombre de boites (unites de presentation)
foreach($dispensations as $code => $unites){
  if ($dispensations[$code]["quantite_administration"] == 0) {
    unset($dispensations[$code]); continue;
  }
  
  if(strstr($dispensations[$code]["quantite_dispensation"],'.')){
    $dispensations[$code]["quantite_dispensation"] = ceil($dispensations[$code]["quantite_dispensation"]);
  }
		
	// Si le code est bien un code CIS
  if(strlen($code) == '8'){
    $_produits_from_cis[$code] = CBcbProduit::getProduitsFromCISInLivret($code); 
  } else {
    $produit = new CBcbProduit();
    $produit->load($code);
    $_produits_from_cis[$code] = array(array("CODE_CIP" => $code, "LIBELLE_PRODUIT" => $produit->libelle));
  }
	
  foreach($_produits_from_cis[$code] as $_produit){
    $_code_cip = $_produit["CODE_CIP"];
    $produits_cip[$_code_cip] = $_produit;
    
	  $product = new CProduct();
	  $product->code = $_code_cip;
	  $product->category_id = $category_id;
	  
	  if ($product->loadMatchingObject()) {
	    // Chargement du stock
      $product->loadRefStock();
	    $stock = $product->_ref_stock_group;
	    
	    // Chargement des ref de la dispensation � effectuer
	    $delivery = new CProductDelivery();
      $delivery->stock_id = $stock->_id;
      $delivery->stock_class = $stock->_class_name;
	    $delivery->service_id = $service_id;
	    $delivery->loadRefsFwd();
	    $delivery->_ref_stock->loadRefsFwd();
	    $delivery->_ref_stock->_ref_product->updateFormFields();
	    $delivrances[$code][$_code_cip] = $delivery;
	    
	    // Calcul du stock de la pharmacie
	  	$stocks_pharmacie[$code][$_code_cip] = $delivery->_ref_stock->quantity;
		  if(!isset($stocks_pharmacie[$code]["total"])){
		    $stocks_pharmacie[$code]["total"] = 0;
		  }
	    $stocks_pharmacie[$code]["total"] += $stocks_pharmacie[$code][$_code_cip];
	  }
  }
}


// On arrondit la quantite de "boites"
foreach($dispensations as $code_cis => $_quantites){
  foreach($_quantites as $_quantite){
    if(strstr($_quantite,'.')){
      //$_quantite = ceil($_quantite);
    }
  }

  $_produits = $_produits_from_cis[$code_cis];
  foreach($_produits as $_produit){
    $code_cip = $_produit["CODE_CIP"];
    
	  // Chargement des dispensation d�j� effectu�e
	  $where = array();
	  $where['product_delivery.date_dispensation'] = "BETWEEN '$date_min_orig 00:00:00' AND '$date_max_orig 23:59:59'"; // entre les deux dates
	  $where['product.code'] = "= '$code_cip'"; // avec le bon code CIP et seulement les produits du livret th�rapeutique
	  $where['product.category_id'] = "= '$category_id'";
	  $where['product_delivery.patient_id'] = "IS NULL";
	  $where['product_delivery.quantity'] = " > 0";
	  $where['product_delivery.stock_class'] = "= 'CProductStockGroup'";
    
	  // Pour faire le lien entre le produit et la delivrance, on utilise le stock etablissement
	  $ljoin = array();
	  $ljoin['product_stock_group'] = 'product_delivery.stock_id = product_stock_group.stock_id';
	  $ljoin['product'] = 'product_stock_group.product_id = product.product_id';
	  
	  $deliv = new CProductDelivery();
	  $list_done = $deliv->loadList($where, null, null, null, $ljoin);

	  $done[$code_cis][$code_cip] = array();
	  $done_nominatif[$code_cis][$code_cip] = array();
	  
	  if (count($list_done)) {
	    if(!isset($done[$code_cis]["total"])){
	      $done[$code_cis]["total"] = 0;
	    }
	    foreach ($list_done as $d) {
	    	$d->loadRefsBack();
	      $done[$code_cis][$code_cip][] = $d;
	      $done[$code_cis]["total"] += $d->quantity;
	    }
	  }
	
	  // Chargement des dispensation d�j� effectu�e en mode nominatif
	  $where['product_delivery.patient_id'] = "IS NOT NULL";
	  $list_done_nominatif = $deliv->loadList($where, null, null, null, $ljoin);
	  
	  if (count($list_done_nominatif)) {
	    if(!isset($done_nominatif[$code_cis]["total"])){
	      $done_nominatif[$code_cis]["total"] = 0;
	    }
	    foreach ($list_done_nominatif as $d_nomin) {
	    	$d_nomin->loadRefsBack();
				$d_nomin->loadRefPatient();
	      $done_nominatif[$code_cis][$code_cip][] = $d_nomin;
	      $done_nominatif[$code_cis]["total"] += $d_nomin->quantity;
	    }
	  }
	  $stocks_service[$code_cis][$code_cip] = CProductStockService::getFromCode($code_cip, $service_id);
	  if(!isset($stocks_service[$code_cis]["total"])){
	    $stocks_service[$code_cis]["total"] = 0;
	  }
	  $stocks_service[$code_cis]["total"] += $stocks_service[$code_cis][$code_cip]->quantity;
	}
}

// Ajustement de la valeur propos� dans la dispensation en fonction de celles deja effectu�es
foreach($done as $_done_cis => $_done) {
  if(isset($delivrances[$_done_cis])){
	  foreach($delivrances[$_done_cis] as $_delivery_cip => $_delivery){
	    $_quantites = $dispensations[$_done_cis];	    
	    $_delivery->quantity = max($_quantites["quantite_dispensation"] - (isset($_done["total"]) ? $_done["total"] : 0), 0);
	    if(!CAppUI::conf("dPstock CProductStockGroup infinite_quantity")){
	      $_delivery->quantity = min($_delivery->quantity, $stocks_pharmacie[$_done_cis][$_delivery_cip]);
	    }
	  }
  }
}



// Patch unite de dispensation
foreach($delivrances as $code_cis => $_delivrance){
  $produits = CBcbProduit::getProduitsFromCIS($code_cis);
  foreach($produits as $_produit_bcb){
    if(!array_key_exists($_produit_bcb["CODE_CIP"], $delivrances[$code_cis])){
      continue;
    }
    $produit = new CBcbProduit();
    $produit->load($_produit_bcb["CODE_CIP"]);
    $produit->loadConditionnement();
    $produit->loadLibellePresentation();
    $produit->_unite_administration = $produit->libelle_unite_presentation;
		$produit->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;
    
    if($produit->_unite_dispensation == $produit->libelle_unite_presentation){
      $ratio = 1;
    } else {
      $ratio = 1 / $produit->nb_unite_presentation;
    }
   
    // Calcul de la quantite 
	  $administration = $dispensations[$code_cis]["quantite_administration"];
	  $quantite_dispensation = $ratio*$administration;
    if(strstr($quantite_dispensation,'.')){
      $quantite_dispensation = ceil($quantite_dispensation);
    }
    
	  $correction_dispensation[$code_cis][$produit->code_cip]["produit"] = $produit->libelle;
	  $correction_dispensation[$code_cis][$produit->code_cip]["dispensation"] = $quantite_dispensation;
	  $correction_dispensation[$code_cis]["nb"]["$quantite_dispensation"] = $quantite_dispensation;
  }
}

foreach($correction_dispensation as $code_cis => $_correction){
  if(count($correction_dispensation[$code_cis]["nb"]) ==  1){
    unset($correction_dispensation[$code_cis]);
    continue;
  }  
  foreach($delivrances[$code_cis] as $code_cip => $_delivery){
    $_delivery->quantity = $correction_dispensation[$code_cis][$code_cip]["dispensation"];
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("correction_dispensation", $correction_dispensation);
$smarty->assign("besoin_patient", $besoin_patient);
$smarty->assign('delivrances', $delivrances);
$smarty->assign('produits_cip'  , $produits_cip);
$smarty->assign('produits_cis'  , $produits_cis);
$smarty->assign('done'  , $done);
$smarty->assign('done_nominatif', $done_nominatif);
$smarty->assign('stocks_service'  , $stocks_service);
$smarty->assign('stocks_pharmacie'  , $stocks_pharmacie);
$smarty->assign('service_id', $service_id);
$smarty->assign("date_min", $date_min_orig);
$smarty->assign("date_max", $date_max_orig);
$smarty->assign("now", mbDate());
$smarty->assign('mode_nominatif', "0");

if($_selected_cis){
  $smarty->assign("quantites", $dispensations[$_selected_cis]);
  $smarty->assign("code_cis", $_selected_cis);
  $smarty->assign("nodebug", true);
  $smarty->display('inc_dispensation_line.tpl');
} else {
  $smarty->assign('dispensations', $dispensations);
  $smarty->display('inc_dispensations_list.tpl');
}
?>