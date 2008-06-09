<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


// Fonction permettant d'inserer $lines dans $prescription
function insertMedElts($lines, $prescription, $sejour){
	global $AppUI;
	
  if($prescription->type == "sejour"){ 
    $limit = $sejour->_entree;	
  }
  if($prescription->type == "sortie"){ 
    $limit = $sejour->_sortie;	
  }
  				
  // Chargement des lignes de medicament deja presents dans la prescription
  $medicaments = array();
  $line_medicament = new CPrescriptionLineMedicament();
  $line_medicament->prescription_id = $prescription->_id;
  $medicaments = $line_medicament->loadMatchingList();
  $produits = array();
  foreach($medicaments as &$_medicament){
    $produits[$_medicament->code_cip] = $_medicament;  	
  }	
  
  // Chargement des elements deja presents dans la prescription
  $elements = array();
  $line_element = new CPrescriptionLineElement();
  $line_element->prescription_id = $prescription->_id;
  $elements = $line_element->loadMatchingList();
  
  // Parcours des elements de la prescription courante
  foreach($lines as $cat => $lines_by_cat){
  	foreach($lines_by_cat as &$line){
  		// Si le medicament est deja present dans la prescription, on passe a la ligne suivante
  		if($cat == "medicament" && array_key_exists($line->code_cip,$produits)){
      	continue;
  		}
  		// Si l'element est deja present, on passe a la ligne suivante
  		if($cat == "element" && array_key_exists($line->_id, $elements)){
  			continue;
  		}	
		  // si la ligne est un element, on verifie les date
  		if($cat == "element"){
		    if($line->date_arret){
		      $line->_fin = $line->date_arret;	
		    }
			  if($line->_fin < mbDate($limit)){
		    	continue;
		    }
  		}
  		
  		// Chargement des prises 
			$line->loadRefsPrises();
			
  		// Adaptation des dates
			if($line->debut < mbDate($limit)){
				$diff_duree = mbDaysRelative($line->debut, mbDate($limit));
				if( $line->duree - $diff_duree > 0){
					$line->duree = $line->duree - $diff_duree;
					$line->debut = mbDate($limit);
				}
			}					
			$line->_id = "";
	    $line->prescription_id = $prescription->_id;
	    //$line->praticien_id = $AppUI->user_id;
	    $line->signee = 0;
	    if($cat == "medicament"){
	      $line->valide_pharma = 0;
	    }
	    $msg = $line->store();
	    viewMsg($msg, "msg-$line->_class_name-create");  
	  
		  // Parcours des prises et creation des nouvelles prises
		  foreach($line->_ref_prises as $prise){
			  $prise->_id = "";
			  $prise->object_id = $line->_id;
			  $msg = $prise->store();
		    viewMsg($msg, "msg-CPrisePosologie-create");  	
		  }
  	}
  }
}



global $AppUI;

// Dans le cas de la validation de la totalite des prescriptions
$prescription_id = mbGetValueFromPost("prescription_id");
$mode_pharma = mbGetValueFromPost("mode_pharma");
$chapitre = mbGetValueFromPost("chapitre", "medicament");


if($prescription_id){
	$prescription = new CPrescription();
	$prescription->load($prescription_id);
}

// Pour la validation d'une ligne precise
$prescription_line_id = mbGetValueFromPost("prescription_line_id");

$medicaments = array();
$elements = array();
$comments = array();
$lines = array();

// Validation d'une ligne
if($prescription_line_id){	
	// Ligne de m�dicaments
	if($chapitre == "medicament"){
		// Chargement de la ligne de prescription
	  $prescription_line = new CPrescriptionLineMedicament();
	  $prescription_line->load($prescription_line_id);
		$medicaments[$prescription_line->_id] = $prescription_line;
	  // Chargement de la prescription
	  $prescription = new CPrescription();
	  $prescription->load($prescription_line->prescription_id);
	}
}


// Si mode pharma en validation globale, chargement de tous les medicaments de la prescription et calcul des interactions
if($prescription->_id && $mode_pharma){
	$prescription->loadRefObject();
	$prescription->_ref_object->loadRefPatient();
	$prescription->loadRefsLines();
	$sejour =& $prescription->_ref_object;
	$patient =& $sejour->_ref_patient;
	
	// Chargement du dossier medical du patient
	$patient->loadRefDossierMedical();
  $patient->_ref_dossier_medical->updateFormFields();
  $patient->_ref_dossier_medical->loadRefsAntecedents();
  $patient->_ref_dossier_medical->loadRefsTraitements();
  $patient->_ref_dossier_medical->loadRefsAddictions();
  
  // Gestion des alertes
  $allergies    = new CBcbControleAllergie();
  $allergies->setPatient($patient);
  $interactions = new CBcbControleInteraction();
  $IPC          = new CBcbControleIPC();
  $profil       = new CBcbControleProfil();
  $profil->setPatient($patient);
  
  foreach ($prescription->_ref_prescription_lines as &$_line_med) {
  	if(!$_line_med->child_id){
	    // Chargement de la posologie
	    // Ajout des produits pour les alertes
	    $allergies->addProduit($_line_med->code_cip);
	    $interactions->addProduit($_line_med->code_cip);
	    $IPC->addProduit($_line_med->code_cip);
	    $profil->addProduit($_line_med->code_cip);
  	}
  }
  $alertesAllergies    = $allergies->getAllergies();
  $alertesInteractions = $interactions->getInteractions();
  $alertesIPC          = $IPC->getIPC();
  $alertesProfil       = $profil->getProfil();
  foreach ($prescription->_ref_prescription_lines as &$_line) {
  	if(!$_line->child_id){
	    $_line->checkAllergies($alertesAllergies);
	    $_line->checkInteractions($alertesInteractions);
	    $_line->checkIPC($alertesIPC);
	    $_line->checkProfil($alertesProfil);
  	}
  	if($_line->_nb_alertes == "0" && $_line->valide_pharma == "0" && $_line->_ref_produit->inLivret){
  		$medicaments[$_line->_id] = $_line;
  	}
  }
}



// Validation de tous les medicaments
if($prescription_id && $chapitre=="medicament" && !$mode_pharma){
	// Chargement de toutes les lignes du user_courant non valid�es
	$prescriptionLineMedicament = new CPrescriptionLineMedicament();
	$prescriptionLineMedicament->prescription_id = $prescription_id;
  $prescriptionLineMedicament->praticien_id = $AppUI->user_id;
	$prescriptionLineMedicament->signee = "0";
	$medicaments = $prescriptionLineMedicament->loadMatchingList();

  $prescriptionLineComment = new CPrescriptionLineComment();
  
  $where = array();
  $where["prescription_id"] = " = '$prescription_id'";
  $where["praticien_id"] = " = '$AppUI->user_id'";
  $where["category_prescription_id"] = "IS NULL";
  $where["signee"] = " = '0'";
  $where["child_id"] = "IS NULL";
  $comments = $prescriptionLineComment->loadList($where);
	
	// Chargement de la prescription
	$prescription = new CPrescription();
  $prescription->load($prescription_id);
}


if($prescription_id && $chapitre!="medicament" && !$mode_pharma){
	// Elements
	$ljoinElement["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	$ljoinElement["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	
	// Comments
	$ljoinComment["category_prescription"] = "category_prescription.category_prescription_id = prescription_line_comment.category_prescription_id";
	
	$where = array();
	$where["prescription_id"] = " = '$prescription_id'";
	$where["praticien_id"] = " = '$AppUI->user_id'";
	$where["signee"] = " = '0'";
	$where["child_id"] = "IS NULL";
	$where["category_prescription.chapitre"] = " = '$chapitre'";
	
	$prescription_line_element = new CPrescriptionLineElement();
	$elements = $prescription_line_element->loadList($where, null, null, null, $ljoinElement);
	
	$prescription_line_comment = new CPrescriptionLineComment();
	$comments = $prescription_line_comment->loadList($where, null, null, null, $ljoinComment);
}

// Parcours des medicaments et passage de valide � 1
foreach($medicaments as $key => $lineMedicament){
	if($mode_pharma){
		$lineMedicament->valide_pharma = 1;
	} else {
	  $lineMedicament->signee = 1;
	}
	$msg = $lineMedicament->store();
	viewMsg($msg, "msg-CPrescriptionLineMedicament-modify");	
}

// Parcours des medicaments et passage de valide � 1
if(!$mode_pharma){
	foreach($elements as $key => $lineElement){
		$lineElement->signee = 1;
		$msg = $lineElement->store();
		viewMsg($msg, "msg-CPrescriptionLineElement-modify");	
	}
}

// Parcours des medicaments et passage de valide � 1
if(!$mode_pharma){
	foreach($comments as $key => $lineComment){
		$lineComment->signee = 1;
		$msg = $lineComment->store();
		viewMsg($msg, "msg-CPrescriptionLineComment-modify");	
	}
}


// Ajout des lignes a la prescription suivante si la ligne n'est pas d�ja incluse
if(!$mode_pharma){
	// Stockage dans un tableaux des medicaments et elements de la prescription courante
	$lines["medicament"] = $medicaments;
	$lines["element"] = $elements;
	  
  // Chargement de la prescription de sejour
  $prescription_sejour = new CPrescription();
  $prescription_sejour->object_id = $prescription->object_id;
  $prescription_sejour->object_class = $prescription->object_class;
  $prescription_sejour->type = "sejour";
  $prescription_sejour->loadMatchingObject();
  
  $sejour =& $prescription_sejour->_ref_object;
  
  // Chargement de la prescription de sortie
  $prescription_sortie = new CPrescription();
  $prescription_sortie->object_id = $prescription->object_id;
  $prescription_sortie->object_class = $prescription->object_class;
  $prescription_sortie->praticien_id = $AppUI->user_id;
  $prescription_sortie->type = "sortie";
  $prescription_sortie->loadMatchingObject();
  
  // Si la prescription est de type pre_admission, on insere les medicaments et les elements dans les prescriptions de sejour et de sortie
	if($prescription->type == "pre_admission"){
	  if($prescription_sejour->_id){
		  // Insertion des medicaments et elements de pre_admission dans le sejour
		  insertMedElts($lines, $prescription_sejour, $sejour);
		}
	}

	// Si la prescription est de type pre_admission ou sejour
	if($prescription->type == "pre_admission" || $prescription->type == "sejour"){
		if($prescription_sejour->_id){
			insertMedElts($lines, $prescription_sejour, $sejour);
		}
	  if($prescription_sortie->_id){
      // Insertion des medicaments et elements de pre_admission dans la sortie
			insertMedElts($lines, $prescription_sortie, $sejour);
		}
	}
}

echo "<script type='text/javascript'>Prescription.reload($prescription->_id,'', '$chapitre','','$mode_pharma');</script>";
echo $AppUI->getMsg();
exit();

?>