<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");
$sel_chapitre = CValue::get("sel_chapitre");
$debut = CValue::get("debut", mbDate());
$print = CValue::get("print", "0");
$list_bons = CValue::get("list_bons");

// Recuperation des bons � imprimer
$_list_bons = array();
if(count($list_bons)){
	foreach($list_bons as $_key_bon){
		$explode_bon = explode("-", $_key_bon);
		$line_bon = $explode_bon[0];
		$hour_bon = $explode_bon[1];
		$_list_bons[$line_bon][] = $hour_bon;
	}
}

$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLinesElementByCat();

$prescription->calculPlanSoin(array($debut));

$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadRefConstantesMedicales();
	  
$sejour =& $prescription->_ref_object;
$sejour->loadRefCurrAffectation($debut);
$sejour->loadRefsOperations();
$sejour->_ref_last_operation->loadRefPlageOp();
$sejour->_ref_last_operation->loadExtCodesCCAM();
$bons = array();
$all_bons = array();
$lines = array();

// Liste des chapitres concern�s par l'impression des bons
$chapitres = array("anapath", "biologie", "imagerie");

// Stockage des bons
if(count($prescription->_ref_lines_elt_for_plan)){
	foreach($prescription->_ref_lines_elt_for_plan as $_name_chap => $lines_by_cat){
	  if(!in_array($_name_chap, $chapitres) || ($sel_chapitre && $sel_chapitre != $_name_chap)){
	    continue;
	  }
	  foreach($lines_by_cat as $_name_cat => $_lines){
	    foreach($_lines as $lines_by_unite){
	      foreach($lines_by_unite as $_line){
	        if(is_array($_line->_quantity_by_date)){
		        foreach($_line->_quantity_by_date as $unite => $_quantity_by_date){
		          foreach($_quantity_by_date as $_date => $quantites){
		            foreach($quantites as $_quantites){
			            if(is_array($_quantites)){  
			              foreach($_quantites as $_hour => $_quantite){
			                $print_bon = false;
											// Si le bon n'a pas ete selectionn�
											if(array_key_exists($_line->_id, $_list_bons) && in_array($_hour, $_list_bons[$_line->_id])){
			                  $print_bon = true;
											}

											if(isset($_line->_administrations[$unite][$_date][$_hour]["quantite_planifiee"])){
			                	$quantite = $_line->_administrations[$unite][$_date][$_hour]["quantite_planifiee"];
												if($print_bon){
			                    @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $quantite;
												}
												@$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $quantite;
			                }
											
											if(isset($_quantite["total"]) && $_quantite["total"]){
												if($print_bon){		                  
			                    @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $_quantite["total"];
												}
												@$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $_quantite["total"];
			                }

			                if(!array_key_exists($_line->_id, $lines)){
			                  $lines[$_line->_id] = $_line;
			                }
			              
										}
			            }
		            }
		          }
		        }
	        }
	        if(is_array($_line->_administrations)){
			      foreach($_line->_administrations as $unite_prise => &$administrations_by_unite){
			        foreach($administrations_by_unite as $_date => &$administrations_by_date){
			          foreach($administrations_by_date as $_hour => &$administrations_by_hour){
	                if(is_numeric($_hour)){
                	  $print_bon = false;
                    // Si le bon ne fait pas parti de ceux selectionnes
                    if(array_key_exists($_line->_id, $_list_bons) && in_array($_hour, $_list_bons[$_line->_id])){
                     $print_bon = true;
                    }
										
				          	$quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
					          if($quantite_planifiee){
		                	if($print_bon){
			                  @$bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $quantite_planifiee;
											}
											@$all_bons[$_name_chap][$_hour][$_name_cat][$_line->_id] += $quantite_planifiee;
					          }
										
	                	if(!array_key_exists($_line->_id, $lines)){
			                $lines[$_line->_id] = $_line;
			              }
	                }
			          }
			        }
			      }
		      }
	      }
	    }
	  }
	}
}

// Tri par heures
foreach($bons as $_chap => &$_bons){
  ksort($_bons);
}

// Tri par heures
foreach($all_bons as $_chap => &$_all_bons){
  ksort($_all_bons);
}

// Creation d'un tableau des affectations pour la date courante
$affectations = array();
$prescription->_ref_object->loadRefsAffectations();
foreach($prescription->_ref_object->_ref_affectations as $_affectation){
	if(mbDate($_affectation->entree) == $debut || mbDate($_affectation->sortie) == $debut){
	  $affectations[$_affectation->entree] = $_affectation;
	}
}
ksort($affectations);

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

$filter_line = new CPrescriptionLineElement();
$filter_line->debut = $debut;

$smarty = new CSmartyDP;
$smarty->assign("affectations", $affectations);
$smarty->assign("all_bons", $all_bons);
$smarty->assign("bons", $bons);
$smarty->assign("lines", $lines);
$smarty->assign("debut", $debut);
$smarty->assign("prescription", $prescription);
$smarty->assign("nodebug", true);
$smarty->assign("sel_chapitre", $sel_chapitre);
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line", $filter_line);
$smarty->assign("categories", $categories);
$smarty->assign("debut", $debut);
$smarty->assign("print", $print);
$smarty->assign("list_bons", $list_bons);
$smarty->display("print_bon.tpl");

?>