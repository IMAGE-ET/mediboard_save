<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$del = CValue::post("del");
$moment_unitaire_id = CValue::post("moment_unitaire_id");
$mode_checkbox = false;

$nb_fois = CValue::post("nb_fois");
if($nb_fois){
	$_POST["unite_fois"] = "jour";
}

//Traitement specifique pour la gestion des checkBox
$list_checkbox = array("matin" => "le matin", 
                       "midi" => "le midi",
											 "soir" => "le soir", 
											 "apres_midi" => "l'apr�s-midi", 
											 "coucher" => "au coucher");
											 
foreach($list_checkbox as $key_moment => $_checkbox_libelle){
  if(isset($_POST[$key_moment])){
	  $moment_unitaire = new CMomentUnitaire();
	  $moment_unitaire->libelle = addslashes($_checkbox_libelle);
	  $moment_unitaire->loadMatchingObject();
	  
	  $prise_poso = new CPrisePosologie();
	  $prise_poso->object_id = CValue::post("object_id");
	  $prise_poso->object_class = CValue::post("object_class");
	  $prise_poso->unite_prise = CValue::post("unite_prise");
	  $prise_poso->quantite = CValue::post("quantite");
	  $prise_poso->moment_unitaire_id = $moment_unitaire->_id;
	  $msg = $prise_poso->store();
	  $mode_checkbox = true;
  }
}

if(isset($_POST["_urgent"]) || isset($_POST["_now"])){
  $prise_poso = new CPrisePosologie();
	$prise_poso->object_id = CValue::post("object_id");
	$prise_poso->object_class = CValue::post("object_class");
	$prise_poso->quantite = CValue::post("quantite");
	$prise_poso->unite_prise = CValue::post("unite_prise");
	if(isset($_POST["_urgent"])){
	  $prise_poso->urgence_datetime = mbDateTime();
  } else {
    $prise_poso->datetime = mbDateTime();
  }
	$msg = $prise_poso->store();
  $mode_checkbox = true;
}

if($mode_checkbox){
  CApp::rip();
}

if($del || !$moment_unitaire_id){
	$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
	$do->doIt();
}

$_moment_explode = explode("-",$moment_unitaire_id);

// Si moment unitaire, on reprend un traitement normal
if($_moment_explode[0] == "unitaire"){
  $_POST["moment_unitaire_id"] = $_moment_explode[1];
	$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
	$do->doIt();
} else {
	// On recupere toutes les valeurs pass�es
  $object_id = CValue::post("object_id");
  $object_class = CValue::post("object_class");
  $quantite = CValue::post("quantite");
  $unite_prise = CValue::post("unite_prise");
  $nb_tous_les = CValue::post("nb_tous_les");
  $unite_tous_les = CValue::post("unite_tous_les");
  $decalage_prise = CValue::post("decalage_prise");
  
  // Si moment complexe, chargement des moments unitaires correspondants
  $moment = new CBcbMoment();
  $moment->load($_moment_explode[1]);
  $moment->loadRefsAssociations();
  foreach($moment->_ref_associations as &$_association){  	
		$prise_posologie = new CPrisePosologie();
		$prise_posologie->object_id = $object_id;
		$prise_posologie->object_class = $object_class;
		$prise_posologie->moment_unitaire_id = $_association->moment_unitaire_id;
		// Si association ne OR, quantite � 0
		if($_association->OR){
			$prise_posologie->quantite = 0;
		} else {
		  $prise_posologie->quantite = $quantite;
		}
		$prise_posologie->unite_prise = $unite_prise;
		$prise_posologie->nb_tous_les = $nb_tous_les;
		$prise_posologie->unite_tous_les = $unite_tous_les;
		$prise_posologie->decalage_prise = $decalage_prise;
		
		if($msg = $prise_posologie->store()){
			return $msg;
		}
  }
  CApp::rip();
}
?>