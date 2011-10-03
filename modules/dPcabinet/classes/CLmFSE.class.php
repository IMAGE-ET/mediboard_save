<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPcabinet", "CLmObject");

/**
 * FSE produite par LogicMax
 */
class CLmFSE extends CLmObject {  
  // DB Table key
  var $S_FSE_NUMERO_FSE = null;
  
  var $_annulee = null;

  // DB Fields : see getProps();

  // Filter Fields
  var $_date_min = null;
  var $_date_max = null;
  
  // References
  var $_ref_id = null;
  var $_ref_lot = null;
  
  // Distant field
  var $_consult_id = null;

	function updateFormFields() {
	  parent::updateFormFields();
	  $this->_annulee = $this->S_FSE_ETAT == "3";
	}
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CConsultation';
    $spec->table   = 'S_F_FSE';
    $spec->key     = 'S_FSE_NUMERO_FSE';
    return $spec;
  }
 	
  function getProps() {
    $specs = parent::getProps();
    
    // DB Fields
    $specs["S_FSE_NUMERO_FSE"]        = "ref class|CLmFSE";
    $specs["S_FSE_ETAT"]              = "enum list|1|2|3|4|5|6|7|8|9|10";
    $specs["S_FSE_MODE_SECURISATION"] = "enum list|0|1|2|3|4|5|255";
    $specs["S_FSE_DATE_FSE"]          = "date";
    $specs["S_FSE_CPS"]               = "num";
    $specs["S_FSE_VIT"]               = "num";
    $specs["S_FSE_LOT"]               = "ref class|CLmLot";
    $specs["S_FSE_TOTAL_FACTURE"]     = "currency";
    $specs["S_FSE_TOTAL_AMO"]         = "currency";
    $specs["S_FSE_TOTAL_ASSURE"]      = "currency";
    $specs["S_FSE_TOTAL_AMC"]         = "currency";
    //infos suppl�mentaires sur le patient
    $specs["S_FSE_IMM_NUM"]           = "num";
    $specs["S_FSE_IMM_CLE"]           = "num";
    $specs["S_FSE_DATE_NAISSANCE"]    = "num";
    //infos suppl�mentaires sur le praticien
    $specs["S_FSE_CPS_NOM"]           = "text";

    // Filter Fields
    $specs["_date_min"] = "date";
    $specs["_date_max"] = "date moreThan|_date_min";
    
    // Distant field
    $specs["_consult_id"] = "ref class|CConsultation";
    
    return $specs;
  }
  
  function loadRefLot() {
    $lot = new CLmLot();
    $this->_ref_lot = $lot->getCached($this->S_FSE_LOT);
  }
  
  function loadRefIdExterne() {
    $this->_ref_id = new CIdSante400();
    $this->_ref_id->object_class = "CConsultation";
    $this->_ref_id->tag = "LogicMax FSENumero";
    $this->_ref_id->id400 = $this->_id;
    $this->_ref_id->loadMatchingObject();
    
    $this->_consult_id = $this->_ref_id->object_id;
  }
  
  /*
   * Detecte si une FSE est mal ou non associ�e
   * Renvoie un message pour associer � une consultation trouv�e
   * Ou un message pour supprimer la FSE non associ�e en double
   * sinon un message null
   */
  function detectlink(){
  	//on �limine d'entr�e la recherche sur les FSE annul�es
  	if($this->S_FSE_ETAT==3){
  		return;
  	}
    $this->loadRefIdExterne();
    //chargement de la consultation associ�e � la FSE
    $consult = new CConsultation();
    $is_consult = $consult->load($this->_consult_id);
    
    //chargement de la plage de consultation
    $plage   = new CPlageconsult();
    $plage->load($consult->plageconsult_id);
    
    //chargement du patient associ�e � la consultation
    $patient = new CPatient();
    $patient->load($consult->patient_id);
    $patient->loadIdVitale();
    
    //chargement du praticien qui a fait la consultation
    $mediuser = new CMediusers();
    $mediuser->load($consult->getExecutantId());
    $mediuser->loadIdCPS();
  
    /*v�rification des champs de chaque cot�
     * les donn�es sur le praticien, le patient doivent corespondre avec la FSE
     */
    $check_prat      = ($this->S_FSE_CPS == $mediuser->_id_cps);
    $check_patient   = ($this->S_FSE_VIT == $patient->_id_vitale);
   //parfois la fse est format� un autre jour que la consultation donc ce check n'est pas n�cessaire:
    //$check_dateFSE   = ($this->S_FSE_DATE_FSE == $plage->date);
  
    $msg = array(); 
    /*
     * rechercher la consultation � associer...
     */
    if (!$check_prat | !$check_patient){
    	//chargement du praticien qui a fait la FSE
			$medi = new CMediusers();
			$medi->loadFromIdCPS($this->S_FSE_CPS);
			
			//chargement des plages de consultation de ce praticien corespondant � la date de la FSE
			$plage   = new CPlageconsult();
			$where   = array();
			$where["date"]    = "= '$this->S_FSE_DATE_FSE'";
			$where["chir_id"] = "= '$medi->user_id'";
			$plages  = $plage->loadList($where);
			//loadObject plus simple mais si plusieurs plages le m�me jour!!
			if ($plages){
			  $cle = 0;
			  foreach($plages as $plage) {
			   // $spec = $plage->getSpec();
			   // $tab_plage_id[$cle]= $spec->key;
			    $tab_plage_id[$cle] = $plage->plageconsult_id;
			    $cle = $cle+1;
			  }
			}else{
			  $msg[] = "FSE non ou mal associ�e, pas de plages horaires � la date";
			  return $msg;
			}
			
			//chargement du patient corespondant � la FSE
			$patient = new CPatient();
			
			$id_vitale = new CIdSante400();
			$id_vitale->object_class = $patient->_class;
			$id_vitale->id400 = $this->S_FSE_VIT; 
			$id_vitale->tag = "LogicMax VitNumero";
			$id_vitale->loadMatchingObject();
			    
			// Load patient from found id vitale
			if ($id_vitale->object_id) {
			$patient->load($id_vitale->object_id);
			}
			//chargement de la consultation corespondant aux donn�es de la FSE
			$consult_search = new CConsultation();
			$where   = array();
			
			$where["patient_id"]      = "= '$patient->patient_id'";
			$where["plageconsult_id"] = "IN ("; 
			$pass = false;
			foreach ($tab_plage_id as $plage_id){
				if ($pass){
			  $where["plageconsult_id"] = $where["plageconsult_id"]." , ";  
			  }
			  $where["plageconsult_id"] = $where["plageconsult_id"]."'$plage_id' ";
			  $pass = true;
			}
			$where["plageconsult_id"] = $where["plageconsult_id"]." ) ";
			$is_consult = $consult_search->loadObject($where);
			if (!$is_consult){
			  $msg[] = "FSE non ou mal associ�e, consultation introuvable";
			  return $msg;
			}
			else{
			  $consult_search->loadIdsFSE();
			  if(!$consult_search->_current_fse || $consult_search->_current_fse->S_FSE_ETAT==3){
			    //si la consultation trouv�e n'est pas d�j� associ�e ou la FSE associ�e est annul�e
			    //alors on peut associer la FSE � la consultation trouv�e � faire dans un contr�leur et un bouton "associer"
			   $msg[] = "FSE non ou mal associ�e, 1 bonne consultation trouv�e";
			   $msg[] = $consult_search->_id;// ou bien cr�er une var $consultTrouvee acc�s par $this->consultTrouvee
			  }
			  else{//la consultation est d�j� associ�e
			    $detect = $consult_search->_current_fse->detectlink();
			    if(count($detect)==0){
			      $msg[] = "FSE non ou mal associ�e car en double, proc�der � l'annulation";
			    }else{ 
			      if($this->S_FSE_NUMERO_FSE != $consult_search->_current_fse->S_FSE_NUMERO_FSE){
			        $msg[] = "FSE non ou mal associ�e, 1 bonne consultation trouv�e";
			        $msg[] = $consult_search->_id;
			      }
			    }
			  }
			}
			$this->loadRefIdExterne();
    } 
    return $msg;
  }
  
  /*
   * Detecter les erreurs de FSE asscoci�es � une m�me consultation
   * 1 seule requ�te vers la base ammax
   * Renvoie un tableau de doublons de FSE � supprimer 
   */
  function detectDouble(){
  	$doubleToDelete = null;
  	$tempfse = new CLmFSE();
    //rechercher si une consultation a des doublons de fse
    $id_sante_400 = new CIdSante400();
    $query = "SELECT object_id AS consult_id, COUNT(*) AS total, GROUP_CONCAT(`id400`)AS fses
              FROM id_sante400
              WHERE object_class= 'CConsultation'
              AND tag = 'LogicMax FSENumero'
              GROUP BY `object_id`
              having count(*)>1
              ORDER BY total DESC";
    $list = $id_sante_400->_spec->ds->loadList($query);
    foreach($list as $array) {
    	$temp = null;
      // Retirer les FSE annul�es des groupes
      $fses = mb_split(',',$array["fses"]);
      foreach($fses as $fse){
      	$tempfse->load($fse);
      	if($tempfse->S_FSE_ETAT!=3 && $tempfse->S_FSE_NUMERO_FSE!=null){
      		$temp[]=$tempfse->S_FSE_NUMERO_FSE;
      	}
      }
      //si il y a des doubles on les passe dans un tableau
      if(count($temp)>1){
      	for($i=0; $i<(count($temp)-1); $i++){
      		$doubleToDelete[]=$temp[$i];   		
      	}
      }
    }
    return $doubleToDelete;       
  }
}

?>