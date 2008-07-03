<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

/*
 * Permet d'avancer dans l'ordre des prescriptions :
 * Pr�-admission / S�jour / Sortie
 */

global $AppUI;

$prescription_id = mbGetValueFromPost("prescription_id");
$praticien_id = mbGetValueFromPost("praticien_id", $AppUI->user_id);
$type = mbGetValueFromPost("type");
$ajax = mbGetValueFromPost("ajax");

// Chargement de la prescription � dupliquer
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefObject();
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElement();

$lines = array();
$lines["medicament"] = $prescription->_ref_prescription_lines;
$lines["element"] = $prescription->_ref_prescription_lines_element;

$sejour = $prescription->_ref_object;

// Creation de la nouvelle prescription
$prescription->_id = "";
$prescription->type = $type;

$msg = $prescription->store();
$AppUI->displayMsg($msg, "msg-CPrescription-create");
if($msg){
	echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id)</script>";
	echo $AppUI->getMsg();
  exit();
}


// Parcours des lignes de medicaments
foreach($lines as $cat => $lines_by_type){
	foreach($lines_by_type as &$line){
		if($cat == "element"){
		  $line_chapter = $line->_ref_element_prescription->_ref_category_prescription->chapitre;
	    // Si element de type DMI, on ne le copie pas
		  if($line_chapter == "dmi"){
		    continue;
	    }
		}
		
		// Chargements des prises
	  $line->loadRefsPrises();

		// Creation d'une prescription de sejour
		if($type == "sejour"){
			// On ne duplique pas les lignes qui seront finis avant la debut du s�jour
			if($line->_date_arret_fin < $sejour->_entree){
				continue;
			}
			if($line->debut && $line->duree && ($line->debut < mbDate($sejour->_entree))){
				$diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_entree));
				if( $line->duree - $diff_duree > 0){
					$line->duree = $line->duree - $diff_duree;
					$line->debut = mbDate($sejour->_entree);
				}
			}
		}
		
		// Creation d'une prescription de sortie
		if($type == "sortie"){
			// On ne duplique pas les lignes qui seront finis avant la fin du sejour
			if($line->_date_arret_fin < $sejour->_sortie){
				continue;
			}
			if($line->debut && $line->duree && ($line->debut < mbDate($sejour->_sortie))){
	      $diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_sortie));
	      if($line->duree - $diff_duree > 0){
		      $line->duree = $line->duree - $diff_duree;
		      $line->debut = mbDate($sejour->_sortie);
	      }
			}
		}
	
		// On modifie la ligne en supprimant les validations
	  $line->_id = "";
	  $line->prescription_id = $prescription->_id;
	  $line->signee = 0;
	  $line->valide_infirmiere = 0;
	  $line->creator_id = $AppUI->user_id;
	  if($cat == "medicament"){
	    $line->valide_pharma = 0;
	  }
	  // Dans le cas de la sortie, on supprime toutes les infos de durees et on ne stocke que la fin de la ligne
	  if($type == "sortie"){
	  	if($line->debut && $line->unite_duree && $line->duree){
	  		$line->fin = $line->_fin;
	  		$line->debut = "";
	  		$line->time_debut = "";
	  		$line->unite_duree = "";
	  		$line->duree = "";
	  	}
	  }
	  $msg = $line->store();
	  $AppUI->displayMsg($msg, "msg-$line->_class_name-create");
		
	  // Parcours des prises et creation des nouvelles prises
		foreach($line->_ref_prises as $prise){
			$prise->_id = "";
			$prise->object_id = $line->_id;
			$msg = $prise->store();
		  $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");  	
		}
	}
}

echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id)</script>";
echo $AppUI->getMsg();
exit();

?>