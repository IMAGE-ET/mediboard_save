<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescription extends CMbObject {
	
  // DB Table key
  var $prescription_id = null;
  
  // DB Fields
  var $praticien_id    = null;
  var $function_id     = null;
  var $object_class    = null;
  var $object_id       = null;
  var $libelle         = null;
  var $type            = null;
  
  // Object References
  var $_ref_object     = null;
  var $_ref_patient    = null;
  var $_ref_current_praticien = null;
  
  // BackRefs
  var $_ref_prescription_lines                = null;
  var $_ref_prescription_lines_element        = null;
  var $_ref_prescription_lines_element_by_cat = null;
  var $_ref_prescription_lines_comment        = null;
  
  // Others Fields
  var $_type_sejour = null;
  var $_counts_by_chapitre = null;
  var $_counts_no_valide = null;
  var $_dates_dispo = null;
  var $_current_praticien_id = null;  // Praticien utilis� pour l'affichage des protocoles / favoris dans la prescription
  var $_praticiens = null;            // Tableau de praticiens prescripteur
  var $_can_add_line = null;
  
  var $_dateTime_min = null;
  var $_dateTime_max = null;
  
  
  // Dossier/Feuille de soin
  var $_prises = null;
  var $_list_prises = null;
  var $_lines = null;
  var $_administrations = null;
  var $_transmissions = null;
  var $_prises_med = null;
  var $_list_prises_med = null;
 
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription';
    $spec->key   = 'prescription_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line_medicament"] = "CPrescriptionLineMedicament prescription_id";
    $backRefs["prescription_line_element"]    = "CPrescriptionLineElement prescription_id";
    $backRefs["prescription_line_comment"]    = "CPrescriptionLineComment prescription_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["praticien_id"]  = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";  
    $specs["object_id"]     = "ref class|CCodable meta|object_class";
    $specs["object_class"]  = "notNull enum list|CSejour|CConsultation";
    $specs["libelle"]       = "str";
    $specs["type"]          = "notNull enum list|traitement|pre_admission|sejour|sortie|externe";
    $specs["_type_sejour"]  = "notNull enum list|pre_admission|sejour|sortie";
    $specs["_dateTime_min"] = "dateTime";
    $specs["_dateTime_max"] = "dateTime";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    if(!$this->object_id){
      $this->_view = "Protocole: ".$this->libelle;
    } else {
	    $this->_view = "Prescription du Dr ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
	    if($this->libelle){
	    	$this->_view .= "($this->libelle)";
	    }
    }
    
    $this->loadRefCurrentPraticien();
  }
  
  /*
   * Permet de savoir si l'utilisateur courant a le droit de cr�er des lignes dans la prescription
   */
  function getAdvancedPerms($is_praticien, $mode_pharma){
		// Si le user courant est un praticien
		if ($is_praticien || !$this->object_id || $mode_pharma){
			$this->_can_add_line = 1;
		} 
		// Sinon (infirmiere)
		else {
			$time = mbTime();
			$borne_start = CAppUI::conf("dPprescription CPrescription infirmiere_borne_start").":00:00";
			$borne_stop = CAppUI::conf("dPprescription CPrescription infirmiere_borne_stop").":00:00";	
			$freeDays = mbBankHolidays();
			$day = mbTransformTime(null, mbDate(), "%w");

		  if(isset($freeDays[mbDate()]) || 
		     ($time >= $borne_start) || 
		     ($time <= $borne_stop) || 
		     ($day == 0) || 
		     ($day == 6)){
		  	$this->_can_add_line = 1;
		  }
		}	
  }
  
  function check() {  	
    if ($msg = parent::check()) {
  	  return $msg;
  	}
 
  	// Test permettant d'eviter que plusieurs prescriptions identiques soient cr��es 
  	if($this->object_id !== null && $this->object_class !== null && $this->praticien_id !== null && $this->type !== null){
      $prescription = new CPrescription();
      $prescription->object_id = $this->object_id;
      $prescription->object_class = $this->object_class;
      if($prescription->type != "externe"){
        $prescription->praticien_id = $this->praticien_id;
      }
      $prescription->type = $this->type;
  	  $prescription->loadMatchingObject();
  	  
  	  if($prescription->_id){
  	  	return "Prescription d�j� existante";
  	  }	
  	}	
  }
  
  
  function applyDateProtocole(&$_line, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, $date_operation, 
                              $hour_operation, $operation, $sejour, $mode_preview){
    global $AppUI;
		$_line->loadRefsPrises();
		if(!$mode_preview){
		  $_line->_id = "";
		}
    $_line->unite_duree = "jour";
    $_line->debut = "";
		
    // Calcul de la date d'entree
	  switch($_line->jour_decalage){
	  	case 'E': 
	  	  $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->_entree;
	  	  break;
	  	case 'I': 
	  	  $date_debut = "";
		  	$time_debut = "";
	  	  if($date_operation){
	  	    $date_debut = mbDate($date_operation);
	  	    $time_debut = mbTime($date_operation); 
	  	  } else {
		  	  if($operation->_id){
		  	    $date_debut = $operation->_ref_plageop->date; 
	  	      $time_debut = $hour_operation;
		  	  }
	  	  }
	  	  break;
	  	case 'S': $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->_sortie; break;
	  	case 'N': $date_debut = mbDate(); break;
	  }
	  
	  
    // Calcul de la date de sortie
	  switch($_line->jour_decalage_fin){
	  	case 'I': 
	  	  $date_fin = "";
	  	  $time_fin = "";
	  	  if($date_operation){
	  	    $date_fin = mbDate($date_operation);
	  	    $time_fin = mbTime($date_operation); 
	  	  } else {
		  	  if($operation->_id){
		  	    $date_fin = $operation->_ref_plageop->date;
		  	    $time_fin = $hour_operation;
		  	  }
	  	  }
	  	  break;
	  	case 'S': $date_fin = ($fin_sejour) ? $fin_sejour : $sejour->_sortie; break;
	  }
	  
	  $unite_decalage_debut = $_line->unite_decalage == "heure" ? "HOURS" : "DAYS";
	  $unite_decalage_fin   = $_line->unite_decalage_fin == "heure" ? "HOURS" : "DAYS";

	  if(!$_line->jour_decalage){
	    $date_debut = $date_sel;  
	  }
	  
	  // Decalage de la fin
	  if($_line->decalage_line_fin){
	    $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
	  	if($unite_decalage_fin == "DAYS"){
	      $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_fin);	
	  	} else {
	  	  $_line->time_fin = mbTime("$signe_fin $_line->decalage_line_fin HOURS", $time_fin);   
	  	}
	  }

	  // Decalage du debut
    if($_line->_class_name == "CPrescriptionLineMedicament"){
	    if($_line->decalage_line){
	      $signe = ($_line->decalage_line >= 0) ? "+" : "";
			  if($unite_decalage_debut == "DAYS"){ 
		      $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut);	
			  } else {
			    $_line->debut = $date_debut;
			    $_line->time_debut = mbTime("$signe $_line->decalage_line HOURS", $time_debut);	  
			  }
	    } else {
		  $_line->debut = mbDate($date_debut);
	    }
    }

	  else if($_line->_class_name == "CPrescriptionLineElement"){
		  $chapitre = $_line->_ref_element_prescription->_ref_category_prescription->chapitre;
	    if($chapitre != "dmi"){
		    $signe = ($_line->decalage_line >= 0) ? "+" : "";
		    if($_line->decalage_line){
		      if($unite_decalage_debut == "DAYS"){
		        $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut);
		      } else {
		        $_line->debut = $date_debut;
		        $_line->time_debut = mbTime("$signe $_line->decalage_line HOURS", $time_debut);
		      }
		    } else {
		      $_line->debut = mbDate($date_debut);
		    }
		    	
	    }
	  }

	  // Calcul de la duree
	  if($_line->jour_decalage_fin){
	  	$_line->duree = mbDaysRelative($_line->debut, $date_fin);
	  	if($_line->jour_decalage_fin != "S"){
	  	  $_line->duree++;
	  	}
	  }	  
	  
	  $_line->prescription_id = $this->_id;
	  $_line->praticien_id = $praticien_id;
	  $_line->creator_id = $AppUI->user_id;
	  
	  if(!$mode_preview){
		  if($_line->jour_decalage == "I" || $_line->jour_decalage_fin == "I"){
		    if($operation_id){
		      $_line->operation_id = $operation_id;
		    } else {
		      $_line->debut = "";
		      $_line->duree = "";
		      $_line->time_debut = "";
		      $_line->time_fin = "";
		    }
		  }

	    $msg = $_line->store();
	    $AppUI->displayMsg($msg, "{$_line->_class_name}-msg-create");  
	  }
	 	
		// Parcours des prises
    if(!$mode_preview){
			foreach($_line->_ref_prises as $prise){
			  $prise->_id = "";
				$prise->object_id = $_line->_id;
				$prise->object_class = $_line->_class_name;
			  if(!$mode_preview){
				  $msg = $prise->store();
			    $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");  
			  }	
			}	  
    }
  }
  
  // Permet d'appliquer un protocole � une prescription
  function applyProtocole($protocole_id, $praticien_id, $date_sel, $operation_id, $debut_sejour="", $fin_sejour="", $date_operation="") {
    global $AppUI;
    
    // Mode utilis� pour visualiser le protocole au moment de sa cr�ation
    $mode_preview = ($debut_sejour && $fin_sejour);
    
    // Chargement du protocole
    $protocole = new CPrescription();
    $protocole->load($protocole_id);
    
    // Creation d'un protocole de pr�-admission s'il n'y en a pas
    if (!$mode_preview && $this->object_class === 'CSejour') {
      $this->loadRefObject();
	    $this->_ref_object->loadRefsPrescriptions();
	    if (!$this->_ref_object->_ref_prescriptions["pre_admission"]->_id) {
	      $prescription = new CPrescription;
	      $prescription->object_class = "CSejour";
	      $prescription->object_id = $this->object_id;
	      $prescription->type = "pre_admission";
	      $prescription->store();
	      $this->_ref_object->_ref_prescriptions["pre_admission"] = $prescription;
	    }
    }
	
	  // Chargement des lignes de medicaments, d'elements et de commentaires
	  $protocole->loadRefsLinesMed();
	  $protocole->loadRefsLinesElementByCat();
	  $protocole->loadRefsLinesAllComments();

	  $operation = new COperation();
	  $hour_operation = "";
  	$sejour = new CSejour();
	    
	  if($operation_id){
  		// Chargement de l'operation
  		$operation->load($operation_id);
  		$operation->loadRefPlageOp();
			if($operation->_id){
  		  $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
  		}
		  if($this->_ref_object->_class_name == "CSejour"){
		  	$sejour =& $this->_ref_object;
	    }
	  }
	
  // Parcours des lignes de medicaments
	foreach($protocole->_ref_prescription_lines as &$_line_med){	    
	  if(!$mode_preview){
	    // Chargement des lignes de substitutions de la ligne de protocole
	    $_line_med->loadRefsSubstitutionLines();
		  $_substitutions = $_line_med->_ref_substitution_lines;
	  }
	  
	  // Creation et modification de la ligne en fonction des dates
    $this->applyDateProtocole($_line_med, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
	                            $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
	                            
	  if(!$mode_preview){
		  // Creation d'une nouvelle ligne de substitution qui pointe vers la ligne qui vient d'etre cr�e
		  foreach($_substitutions as &$_line_subst){
		    $_line_subst->substitute_for = $_line_med->_id;
		    $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
	                                $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
	    }
	  }  
	}
	
	// Parcours des lignes d'elements
	foreach($protocole->_ref_prescription_lines_element_by_cat as &$elements_by_chap){
	  foreach($elements_by_chap as &$elements_by_cat){
	    foreach($elements_by_cat as &$_lines){
	      foreach($_lines as $_line_elt){
	        $this->applyDateProtocole($_line_elt, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
	                                  $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
	      } 
	    }
	  }
	}
	
	// Parcours des lignes de commentaires
	foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
		$line_comment->_id = "";
		$line_comment->prescription_id = $this->_id;
		$line_comment->praticien_id = $praticien_id;
		$line_comment->creator_id = $AppUI->user_id;
		if(!$mode_preview){
		  $msg = $line_comment->store();
		  $AppUI->displayMsg($msg, "CPrescriptionLineComment-msg-create");
		}
	}
	
	  if($mode_preview){
	    return $protocole;
	  }
  }
  
  /*
   * Calcul du praticien_id responsable de la prescription
   */
  function calculPraticienId(){
  	global $AppUI;
  	
  	if ($this->object_id !== null && $this->object_class !== null && $this->type !== null && $this->object_id){
  		// Chargement de l'object
  		$object = new $this->object_class;
  	  $object->load($this->object_id);
  	  $object->loadRefsFwd();
  		
  		if($this->type != "sejour"){
  		  $this->praticien_id = $AppUI->_ref_user->isPraticien() ? $AppUI->_ref_user->_id : $object->_praticien_id;
  		}
  		else {
  			$this->praticien_id = $object->_praticien_id;
  		}
  	}
  }
  
  
  function store(){  	
  	if(!$this->_id){
  		$this->calculPraticienId(); 
  	}
    return parent::store();
  }
  
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien() {
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien = $this->_ref_praticien->getCached($this->praticien_id);
  }
  
  /*
   * Chargement du praticien utilis� pour l'affichage des protocoles/favoris
   */
  function loadRefCurrentPraticien() {
    if ($this->_ref_current_praticien) {
      return;
    }

    global $AppUI;
  	if ($AppUI->_ref_user->isPraticien()) {
  	  $this->_ref_current_praticien = $AppUI->_ref_user;
  	}
    else {
    	$this->_ref_object->loadRefPraticien();
    	$this->_ref_current_praticien = $this->_ref_object->_ref_praticien;
    }

    $this->_current_praticien_id = $this->_ref_current_praticien->_id;
  }
  
  /*
   * Chargement de l'objet de la prescription
   */ 
  function loadRefObject(){
  	$this->_ref_object = new $this->object_class;
    $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
  
  /*
   * Chargement du patient
   */
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->patient_id);	
  }
  
  /*
   * Forward Refs
   */
  function loadRefsFwd() {
    $this->loadRefPraticien();
    $this->loadRefObject();
    $this->loadRefPatient();
  }
  
  /*
   * Compte le nombre de lignes non valid�es dans la prescription
   */ 
  function countNoValideLines(){
    $this->_counts_no_valide = 0;
    if($this->_id){
      $line = new CPrescriptionLineMedicament();
      $where = array();
      $where["signee"] = " = '0'";
      $where["prescription_id"] = " = '$this->_id'";
      $where["child_id"] = "IS NULL";
      $where["substitution_line_id"] = "IS NULL";
      $this->_counts_no_valide = $line->countList($where);
    }
  }
  
	/*
	 * Chargement du nombre des medicaments et d'elements
	 */
  function countLinesMedsElements($praticien_sortie_id = null){
  	$this->_counts_by_chapitre = array();
  	
  	$line_comment_med = new CPrescriptionLineComment();
  	$ljoin_comment["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	
  	// Count sur les medicaments
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    $where["category_prescription.chapitre"] = "IS NULL";
    
  	$line_med = new CPrescriptionLineMedicament();
  	$whereMed["prescription_id"] = " = '$this->_id'";
  	$whereMed["child_id"] = "IS NULL";
  	$whereMed["substitution_line_id"] = "IS NULL";
  	$whereMed["substitution_active"] = " = '1'";
  	if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  		$whereMed["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	$this->_counts_by_chapitre["med"] = $line_med->countList($whereMed);
  	$this->_counts_by_chapitre["med"] += $line_comment_med->countList($where, null, null, null, $ljoin_comment);
  	
  	
  	// Count sur les elements
  	$ljoin_element["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	  $ljoin_element["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	  
  	$line_element = new CPrescriptionLineElement();
  	$line_comment = new CPrescriptionLineComment();
		
  	$category = new CCategoryPrescription;
    $chapitres = explode("|", $category->_specs["chapitre"]->list);
      	
  	// Initialisation du tableau
    foreach ($chapitres as $chapitre){
    	$this->_counts_by_chapitre[$chapitre] = 0;
    }
  	
    // Parcours des elements
 	  $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	
    foreach ($chapitres as $chapitre) {
  	  $where["category_prescription.chapitre"] = " = '$chapitre'";
   	  $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
			$nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
			$this->_counts_by_chapitre[$chapitre] = $nb_element + $nb_comment;
  	}
  }
  
  
  /*
   * Chargement de l'historique
   */
  function loadRefsLinesHistorique(){
  	$this->loadRefObject();
  	$historique = array();
  	$this->_ref_object->loadRefsPrescriptions();
  	if($this->type == "sejour" || $this->type == "sortie"){
  		$prescription_pre_adm =& $this->_ref_object->_ref_prescriptions["pre_admission"];
  		$prescription_pre_adm->loadRefsLinesMedComments("0");
  		$prescription_pre_adm->loadRefsLinesElementsComments("0");
  		$historique["pre_admission"] = $prescription_pre_adm;
  	}
  	if($this->type == "sortie"){
  		$prescription_sejour =& $this->_ref_object->_ref_prescriptions["sejour"];
  		$prescription_sejour->loadRefsLinesMedComments("0");
  		$prescription_sejour->loadRefsLinesElementsComments("0");
  		$historique["sejour"] = $prescription_sejour;
  	}
  	return $historique;
  }
  
  
  /*
   * Chargement des lignes de prescription de m�dicament
   */
  function loadRefsLinesMed($with_child = 0, $with_subst = 0) {
    $line = new CPrescriptionLineMedicament();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    
    if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
    if($with_subst != "1"){
      $where["substitution_line_id"] = "IS NULL";
    }
    // Permet de ne pas afficher les lignes de substitutions
    $where["substitution_active"] = " = '1'";
    
    $order = "prescription_line_medicament_id DESC";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
    
    foreach($this->_ref_prescription_lines as &$_line){
    	$_line->_ref_produit->loadRefPosologies();
    }
  }
  
  /*
   * Chargement des lignes de medicaments (medicaments + commentaires)
   */
  function loadRefsLinesMedComments($withRefs = "1"){
    // Chargement des lignes de medicaments
  	$this->loadRefsLinesMed();
  	// Chargement des lignes de commentaire du medicament
  	$this->loadRefsLinesComment("medicament");
  	
  	// Initialisation du tableau de fusion
  	$this->_ref_lines_med_comments["med"] = array();
  	$this->_ref_lines_med_comments["comment"] = array();
  	
  	foreach($this->_ref_prescription_lines as &$line_med){
  		if($withRefs){
  			$line_med->loadRefsPrises();
  		  $this->_praticiens[$line_med->praticien_id] = $line_med->_ref_praticien->_view;
  		}
  		$this->_ref_lines_med_comments["med"][] = $line_med;
  	}
  	
  	if(isset($this->_ref_prescription_lines_comment["medicament"][""]["comment"])){
      foreach($this->_ref_prescription_lines_comment["medicament"][""]["comment"] as &$comment_med){
  	  	if($withRefs){
  	  	  $this->_praticiens[$comment_med->praticien_id] = $comment_med->_ref_praticien->_view;
  	  	}
  	  	$this->_ref_lines_med_comments["comment"][] = $comment_med;
      }
  	}
  }
  
  /*
   * Chargement des lignes d'element
   */
  function loadRefsLinesElement($chapitre = "", $withRefs = "1"){
  	$line = new CPrescriptionLineElement();
  	$where = array();
  	$ljoin = array();
  	
  	if($chapitre){
  	  $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	    $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = '$chapitre'";
  	}
  	
    $where["prescription_id"] = " = '$this->_id'";
    
    $order = "prescription_line_element_id DESC";
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    
    foreach ($this->_ref_prescription_lines_element as &$line_element){	
    	$line_element->loadRefElement();
    	if($withRefs){
	    	$line_element->loadRefsPrises();
	    	$line_element->loadRefExecutant();
	    	$this->_praticiens[$line_element->praticien_id] = $line_element->_ref_praticien->_view;
	    }
    	$line_element->_ref_element_prescription->loadRefCategory();
    }
  }
  
  
  /*
   * Chargement des lignes d'elements par cat�gorie
   */
  function loadRefsLinesElementByCat($withRefs = "1", $chapitre = ""){
  	$this->loadRefsLinesElement($chapitre, $withRefs);
  	$this->_ref_prescription_lines_element_by_cat = array();
  	
  	foreach($this->_ref_prescription_lines_element as $line){
  		$category = new CCategoryPrescription();
  		$category->load($line->_ref_element_prescription->category_prescription_id);
  		$this->_ref_prescription_lines_element_by_cat[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
  		$this->_ref_lines_elements_comments[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
  	}

  	ksort($this->_ref_prescription_lines_element_by_cat);
  }
  
  
  /*
   * Chargement des lignes de commentaires
   */
  function loadRefsLinesComment($chapitre = null, $withRefs = "1"){
  	$this->_ref_prescription_lines_comment = array();
    
  	// Initialisation des tableaux
  	$category = new CCategoryPrescription();
  	
  	foreach($category->_specs["chapitre"]->_list as $_chapitre){
  	  $this->_ref_prescription_lines_comment[$_chapitre] = array();	
  	}

  	$commentaires = array();
  	$line_comment = new CPrescriptionLineComment();
  	
  	$where["prescription_id"] = " = '$this->_id'";
  	$order = "prescription_line_comment_id DESC";
  	$ljoin = array();
  	
  	if ($chapitre && $chapitre != "medicament"){
  		$ljoin["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	  $where["category_prescription.chapitre"] = " = '$chapitre'"; 	
  	}
  	if ($chapitre == "medicament"){
  	  $where["category_prescription_id"] = " IS NULL"; 		
  	}
  	
  	$commentaires = $line_comment->loadList($where, $order, null, null, $ljoin);
  	
  	foreach($commentaires as $_line_comment){
  	  if ($withRefs){
          $_line_comment->loadRefExecutant();
  	  }
      if($_line_comment->category_prescription_id){
  	  	// Chargement de la categorie
          $_line_comment->loadRefCategory();
  	  	$cat = new CCategoryPrescription();
  	  	$cat->load($_line_comment->category_prescription_id);
  	  	$chapitre = $cat->chapitre;
  	  } else {
  	  	$chapitre = "medicament";
  	  }
      $this->_ref_prescription_lines_comment[$chapitre]["$_line_comment->category_prescription_id"]["comment"][$_line_comment->_id] = $_line_comment;
      $this->_ref_lines_elements_comments[$chapitre]["$_line_comment->category_prescription_id"]["comment"][$_line_comment->_id] = $_line_comment;
      $this->_praticiens[$_line_comment->praticien_id] = $_line_comment->_ref_praticien->_view;
    }
  }
  
  /*
   * Chargement de toutes les lignes (y compris medicaments)
   */
  function loadRefsLinesAllComments(){
  	$this->_ref_prescription_lines_all_comments = $this->loadBackRefs("prescription_line_comment");
  }
  
  /*
   * Chargement des lignes d'elements (Elements + commentaires)
   */
  function loadRefsLinesElementsComments($withRefs = "1", $chapitre=""){
  	$this->loadRefsLinesElementByCat($withRefs, $chapitre);
  	$this->loadRefsLinesComment("",$withRefs);
  	
  	// Suppression des ligne de medicaments
  	unset($this->_ref_lines_elements_comments["medicament"]);  	
  	ksort($this->_ref_prescription_lines_element_by_cat);

  	// Initialisation des tableaux
		if(count($this->_ref_lines_elements_comments)){
	  	foreach($this->_ref_lines_elements_comments as &$chapitre){
	  		foreach($chapitre as &$cat){
		    	if(!isset($cat["comment"])){
		    		$cat["comment"] = array();
		    	}
		      if(!isset($cat["element"])){
		    		$cat["element"] = array();
		    	}
	  		}
	    }
		}
  }
  
  /*
   * Chargement des medicaments favoris d'un praticien
   */
  static function getFavorisMedPraticien($praticien_id){
  	$favoris = array();
  	$listFavoris = array();
  	$listFavoris["medicament"] = array();
  	$favoris = CBcbProduit::getFavoris($praticien_id);
  	foreach($favoris as $_fav){
  		$produit = new CBcbProduit();
  		$produit->load($_fav["code_cip"],"0");
  		$listFavoris["medicament"][] = $produit;
    }
  	return $listFavoris["medicament"];
  }
  
  /*
   * Chargement des favoris de prescription pour un praticien donn�
   */
  static function getFavorisPraticien($praticien_id){
  	$listFavoris["medicament"] = CPrescription::getFavorisMedPraticien($praticien_id);
  	$category = new CCategoryPrescription();
  	
    foreach($category->_specs["chapitre"]->_list as $chapitre){
  	  $listFavoris[$chapitre] = array();
  	  $favoris[$chapitre] = CElementPrescription::getFavoris($praticien_id, $chapitre);	  
    }
    
	  foreach($favoris as $key => $typeFavoris) {
	  	foreach($typeFavoris as $curr_fav){
	  		$element = new CElementPrescription();
	  	  $element->load($curr_fav["element_prescription_id"]);
	  		$listFavoris[$key][] = $element;
      }
	  }
	  return $listFavoris;  	
  }
  

  /*
   * G�n�ration du Dossier/Feuille de soin
   */
  function calculPlanSoin($date, $mode_feuille_soin = 0, $heures = array()){  
    // Stockage du tableau de ligne de medicaments
  	$lines["medicament"] = $this->_ref_prescription_lines;
    if($this->object_id){
  	  $lines["traitement"] = $this->_ref_object->_ref_prescription_traitement->_ref_prescription_lines;
    }
    
  	// Parcours des lignes
    foreach($lines as $cat_name => $lines_cat){
		  if(count($lines_cat)){
		    foreach($lines_cat as &$_line_med){
		  	  // Si la ligne est prescrite pour la date donn�e
				  if(($date >= $_line_med->debut && $date <= mbDate($_line_med->_fin_reelle))){     	
				    // Chargement des administrations
						$_line_med->calculAdministrations($date, $mode_feuille_soin);
						// Si aucune prise
						if ((count($_line_med->_ref_prises) < 1) && (!isset($this->_lines["med"][$_line_med->_id]["aucune_prise"]))){
						  $this->_lines["med"][$_line_med->_id]["aucune_prise"] = $_line_med;
						  continue;
						}
						// Chargement des prises
						$_line_med->calculPrises($this, $date, $heures, $mode_feuille_soin);
							
						// Stockage d'une ligne possedant des administrations ne faisant pas reference � une prise ou unite de prise
						if(!$mode_feuille_soin){
						  if(@array_key_exists("aucune_prise", $_line_med->_administrations) && count($_line_med->_ref_prises) >= 1){
						    $this->_lines["med"][$_line_med->_id]["aucune_prise"] = $_line_med;
						  }
						}
				  }
		    }
		  }
    }
		// Parcours des elements
		foreach($this->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
		  foreach($elements_chap as $name_cat => $elements_cat){
		    // Initialisation du compteur de lignes
		    foreach($elements_cat as &$_elements){
		 	    foreach($_elements as &$_line_element){
		        if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_fin_reelle))){
		      	  // Chargement des administrations et des transmissions
		      	  $_line_element->calculAdministrations($date, $mode_feuille_soin);
		      	  // Si aucune prise  
			        if ((count($_line_element->_ref_prises) < 1) && (!isset($this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))){
			          $this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
					      continue;
					    }
					    // Chargement des prises
					    $_line_element->calculPrises($this, $date, $heures, $mode_feuille_soin, $name_chap, $name_cat);
					
					    // Stockage d'une ligne possedant des administrations ne faisant pas reference � une prise ou unite de prise
					    if(!$mode_feuille_soin){
					      if(@array_key_exists("aucune_prise", $_line_element->_administrations) && count($_line_element->_ref_prises) >= 1){
						      $this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element; 
					      }
					    }
			      }
			    }
			  }
		  }
    }
  }
}

?>