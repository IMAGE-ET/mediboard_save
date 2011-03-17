<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$can->needsRead();

$praticien_sortie_id = CValue::get("praticien_sortie_id");
$only_dmi            = CValue::get("only_dmi");
$print               = CValue::get("print", 0);
$no_pdf              = CValue::get("no_pdf", 0);
$operation_id        = CValue::get("operation_id");
$linesDMI = array();

// Chargement de l'etablissement
$etablissement = CGroups::loadCurrent();

$prescription_id = CValue::getOrSession("prescription_id");
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsFwd();

// Chargement du poids du patient
$poids = "-";
if($prescription->object_id){
	$patient =& $prescription->_ref_patient;
	$patient->loadRefConstantesMedicales();
	$constantes_medicales = $patient->_ref_constantes_medicales;
	$poids = $constantes_medicales->poids;
}

// Chargement du praticien
$praticien = new CMediusers();
if($praticien_sortie_id){
  $praticien->load($praticien_sortie_id);	
}

// si le user courant est un praticien, on affiche ces lignes
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if($mediuser->isPraticien()){
	$praticien = $mediuser;
}

if($prescription->type == "externe"){
  $consultation =& $prescription->_ref_object;
  $consultation->loadRefPlageConsult();
  $praticien->load($consultation->_praticien_id);
}

if($praticien->_id){
  $praticien->loadRefsFwd();
}

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement des lignes de prescriptions
$prescription->loadRefsLinesMedComments("1", "debut ASC");
$prescription->loadRefsLinesElementsComments("1","","debut ASC");

// Chargement des prescription_line_mixes
$prescription->loadRefsPrescriptionLineMixes();
foreach($prescription->_ref_prescription_line_mixes as $curr_prescription_line_mix){
  $curr_prescription_line_mix->loadRefsLines();
}

if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
  $prescription->loadRefsLinesDMI($operation_id);
}

$medicament = 0;
$comment = 0;
$_ald = false;

// Parcours des medicaments, pas de gestion d'executant pour les medicaments
$lines["medicaments"]["med"]["ald"] = array();
$lines["medicaments"]["med"]["no_ald"] = array();
$lines["medicaments"]["comment"]["ald"] = array();
$lines["medicaments"]["comment"]["no_ald"] = array();
$lines["medicaments"]["dm"]["ald"] = array();
$lines["medicaments"]["dm"]["no_ald"] = array();
$linesElt = array();

// Initialisation du tableau
if(count($prescription->_ref_lines_elements_comments)){
  foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
    foreach($chap_element as $name_cat => $cat_element){
      foreach($cat_element as $type => $elements){
        foreach($elements as $element){
          if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$element->signee && $prescription->object_id){
            continue;
          }
          if($praticien->_id && $element->praticien_id != $praticien->_id){
          	continue;
          }
          if($element instanceof CPrescriptionLineElement){
            $element->loadCompleteView();
          }
          $executant = "aucun";
          if($element->_ref_executant){
            $executant = $element->_ref_executant->_guid;
          }
          
          $linesElt[$name_chap][$executant]["ald"] = array();
          $linesElt[$name_chap][$executant]["no_ald"] = array();  
        }
      }
    }
  }
}

foreach($prescription->_ref_lines_med_comments as $key => $lines_medicament_type){
	foreach($lines_medicament_type as $line_medicament){
	  $line_medicament->loadRefsFwd();
	  if(!$prescription->object_id){
	    if($line_medicament instanceof CPrescriptionLineMedicament){
		    $line_medicament->loadRefsSubstitutionLines();
		    foreach($line_medicament->_ref_substitution_lines as $_subst_by_chap){
		      foreach($_subst_by_chap as $_subst_line){
		        if($_subst_line instanceof CPrescriptionLineMedicament){
						  $_subst_line->loadRefsPrises();
					  } else {
					  	$_subst_line->loadRefsLines();
					  }
		        $_subst_line->loadRefsFwd();
		      }
		    }
	    }
	  }
		if($prescription->object_id && $praticien->_id && $line_medicament->praticien_id != $praticien->_id){
			continue;
		}	
		if($line_medicament->child_id){
			continue;
		}
		if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$line_medicament->signee && $prescription->object_id){
		  continue;
		}
	  if($line_medicament->ald){
	  	$_ald = true;
	    $lines["medicaments"][$key]["ald"][] = $line_medicament;
	  } else {
	  	$lines["medicaments"][$key]["no_ald"][] = $line_medicament;
	  }
		
		// Creation d'une ligne de soin pour la prescription des injections
		if(($prescription->type != "sejour") && $line_medicament instanceof CPrescriptionLineMedicament){
		  if($line_medicament->_is_injectable){
		  	if($line_medicament->injection_ide){
			  	$ald = $line_medicament->ald ? "ald" : "no_ald";
		      $linesElt["soin"]["aucun"][$ald]["inj"][] = $line_medicament;
					if($ald == "ald" && !isset($linesElt["soin"]["aucun"]["no_ald"])){
						 $linesElt["soin"]["aucun"]["no_ald"] = array();
					}
				}
	    }
		}
	}
}


// Tri des medicaments par ordre alphabetique pour l'impression des protocoles
if (!$prescription->object_id){
	if (!function_exists("compareMed")) {
		function compareMed($line1, $line2){
	    return strcmp($line1->_ucd_view, $line2->_ucd_view);
	  }
	}
	foreach($lines["medicaments"] as &$meds_by_key){
		foreach($meds_by_key as &$meds_by_ald){
			usort($meds_by_ald, "compareMed");
		}
	}
}


// Parcours des prescription_line_mixes
foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
  $_prescription_line_mix->loadRefPraticien();
  $_prescription_line_mix->loadRefsSubstitutionLines();
  foreach($_prescription_line_mix->_ref_substitution_lines as $_subst_by_chap){
    foreach($_subst_by_chap as &$_subst_perf){
      $_subst_perf->loadRefPraticien();
			if($_subst_perf instanceof CPrescriptionLineMix){
        $_subst_perf->loadRefsLines();
		  } else {
		  	$_subst_perf->loadRefsPrises();
		  }
    }
  }	   
  if($prescription->object_id && $praticien->_id && $_prescription_line_mix->praticien_id != $praticien->_id){
		continue;
	}	
	if($_prescription_line_mix->next_line_id){
		continue;
	}
  if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$_prescription_line_mix->signature_prat && $prescription->object_id){
	  continue;
  }
  $lines["medicaments"]["med"]["no_ald"][] = $_prescription_line_mix;
}



// Parcours des elements
if(count($prescription->_ref_lines_elements_comments)){
	foreach($prescription->_ref_lines_elements_comments as $name_chap => $chap_element){
		foreach($chap_element as $name_cat => $cat_element){
			foreach($cat_element as $type => $elements){
				foreach($elements as $element){
					if($prescription->object_id && $praticien->_id && $element->praticien_id != $praticien->_id){
				    continue;
			    }	
					if($element->child_id){
						continue;
					}
					if(!CAppUI::conf("dPprescription CPrescription show_unsigned_lines") && !$element->signee && $prescription->object_id){
					  continue;
				  }
				  $element->loadRefsFwd();
				  $executant = "aucun";
			    if($element->_ref_executant){
			      $executant = $element->_ref_executant->_guid;
			    }
					
					if($element->ald){
			    	$_ald = true;
			    	$linesElt[$name_chap][$executant]["ald"][$name_cat][] = $element;
			    } else {
			      $linesElt[$name_chap][$executant]["no_ald"][$name_cat][] = $element;
			    }
				
					// Affichage des DM dans la page des medicaments
					if($prescription->type != 'sejour' && ($element instanceof CPrescriptionLineElement) && $element->cip_dm){
						$element->loadRefDM();
				    $libelle_ald = $element->ald ? "ald" : "no_ald";
						$lines["medicaments"]["dm"][$libelle_ald][] = $element;
        	}	
				}
			}
	  }
	}
}

// Tri des elements par ordre alphabetique pour l'impression des protocoles
if (!$prescription->object_id){
	if (!function_exists("compareElt")) {
		function compareElt($line1, $line2){
		  return strcmp($line1->_view, $line2->_view);
		}
	}
	foreach($linesElt as &$lines_by_chap){
		foreach($lines_by_chap as &$lines_by_exec){
			foreach($lines_by_exec as &$lines_elt_by_ald){
	      foreach($lines_elt_by_ald as &$lines_elt_by_cat){
					usort($lines_elt_by_cat, "compareElt");
				}
			}
		}
	}
}
			  	
if(count($prescription->_ref_lines_dmi)){
  foreach($prescription->_ref_lines_dmi as $_line_dmi){
    $_line_dmi->loadRefsFwd();
    $linesDMI[$_line_dmi->_id] = $_line_dmi;
  }
}

// Tableau de traductions
$traduction = array("E" => "l'entr�e", "I" => "I", "S" => "la sortie", "N" => "M");

// Affectation du praticien selectionn�
$prescription->_ref_selected_prat = $praticien;

// Chargement du header
$header_height = 120;
$template_header = new CTemplateManager();
if(!$_ald){
	$prescription->fillTemplate($template_header);
	$header = CPrescription::getPrescriptionTemplate("header", $praticien);
	if($header->_id){
	  $header->loadContent();
	  $template_header->renderDocument($header->_source);
		$header_height = $header->height * 12;
	}
}

// Chargement du footer
$footer_height = 120;
$template_footer = new CTemplateManager();
if(!$_ald){
	$prescription->fillTemplate($template_footer);
	$footer = CPrescription::getPrescriptionTemplate("footer", $praticien);
	if($footer->_id){
	  $footer->loadContent();
	  $template_footer->renderDocument($footer->_source);
	  $footer_height = $footer->height * 12;
	}
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("_ald", $_ald);
$smarty->assign("only_dmi", $only_dmi);
$smarty->assign("header", $header_height);
$smarty->assign("footer", $footer_height);
$smarty->assign("traduction"     , $traduction);
$smarty->assign("print"          , $print);
$smarty->assign("praticien"      , $praticien);
$smarty->assign("function"       , $praticien->_id ? $praticien->_ref_function : new CFunctions());
$smarty->assign("date"           , mbDate());
$smarty->assign("etablissement"  , $etablissement);
$smarty->assign("prescription"   , $prescription);
$smarty->assign("lines"          , $lines);
$smarty->assign("linesElt"       , $linesElt);
$smarty->assign("linesDMI"       , $linesDMI);
$smarty->assign("categories"     , $categories);
$smarty->assign("poids"          , $poids);
$smarty->assign("dci"            , CValue::get("dci"));
$smarty->assign("generated_header", @$header->_id ? $template_header->document : "");
$smarty->assign("generated_footer", @$footer->_id ? $template_footer->document : "");

if (!$prescription->object_id && !$no_pdf) {
	$smarty->assign("no_header", isset($no_header));
  $content = $smarty->fetch("print_prescription.tpl");
	$htmltopdf = new CHtmlToPDF;
	$file = new CFile;
	$file->file_name = "{$prescription->libelle}.pdf";
	$htmltopdf->generatePDF($content, 1, "A4", "portrait", $file);
} else
  $smarty->display("print_prescription.tpl");


?>