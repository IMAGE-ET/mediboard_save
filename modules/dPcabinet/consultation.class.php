<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPccam", "codableCCAM"));

class CConsultation extends CCodableCCAM {
  const PLANIFIE = 16;
  const PATIENT_ARRIVE = 32;
  const EN_COURS = 48;
  const TERMINE = 64;
 
  
  // DB Table key
  var $consultation_id = null;

  // DB References
  var $plageconsult_id = null;
  var $patient_id      = null;

  // DB fields
  var $heure         = null;
  var $duree         = null;
  var $secteur1      = null;
  var $secteur2      = null;
  var $chrono        = null;
  var $annule        = null;
  var $paye          = null;
  var $date_paiement = null;
  var $motif         = null;
  var $rques         = null;
  var $examen        = null;
  var $traitement    = null;
  var $premiere      = null;
  var $adresse       = null; // Le patient a-t'il �t� adress� ?
  var $tarif         = null;
  var $type_tarif    = null;
  var $arrivee       = null;
  var $banque_id     = null;
  var $categorie_id  = null;

  // Form fields
  var $_etat           = null;
  var $_hour           = null;
  var $_min            = null;
  var $_check_premiere = null;
  var $_check_adresse  = null;
  var $_somme          = null;
  var $_types_examen   = null;
  var $_precode_acte   = null;
  var $_store_ngap     = null;
  // Fwd References
  var $_ref_patient      = null;
  var $_ref_plageconsult = null;
  
  // FSE
  var $_bind_fse = null;
  var $_ids_fse = null;

  // Back References
  var $_ref_consult_anesth = null;
  var $_ref_examaudio      = null;
  var $_ref_examcomp       = null;
  var $_ref_examnyha       = null;
  var $_ref_exampossum     = null;
  
  var $_ref_banque         = null;
  var $_ref_categorie      = null;

   // Distant fields
   var $_ref_chir  = null;
   var $_date      = null;
   var $_is_anesth = null; 
   var $_codes_ngap = null;

   // Filter Fields
   var $_date_min	 	= null;
   var $_date_max 		= null;
   var $_prat_id 		= null;
   var $_etat_paiement  = null;
   var $_type_affichage = null;

  function CConsultation() {
    $this->CMbObject("consultation", "consultation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["consult_anesth"] = "CConsultAnesth consultation_id";
      $backRefs["examaudio"] = "CExamAudio consultation_id";
      $backRefs["examcomp"] = "CExamComp consultation_id";
      $backRefs["examnyha"] = "CExamNyha consultation_id";
      $backRefs["exampossum"] = "CExamPossum consultation_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["plageconsult_id"] = "notNull ref class|CPlageconsult";
    $specs["patient_id"]      = "ref class|CPatient";
    $specs["heure"]           = "notNull time";
    $specs["duree"]           = "numchar maxLength|1";
    $specs["secteur1"]        = "currency min|0";
    $specs["secteur2"]        = "currency min|0";
    $specs["chrono"]          = "notNull enum list|16|32|48|64";
    $specs["annule"]          = "bool";
    $specs["paye"]            = "bool";
    $specs["date_paiement"]   = "date";
    $specs["motif"]           = "text";
    $specs["rques"]           = "text";
    $specs["examen"]          = "text";
    $specs["traitement"]      = "text";
    $specs["premiere"]        = "bool";
    $specs["adresse"]         = "bool";
    $specs["tarif"]           = "str";
    $specs["arrivee"]         = "dateTime";
    $specs["type_tarif"]      = "enum list|cheque|CB|especes|tiers|autre default|cheque";
    $specs["banque_id"]       = "ref class|CBanque";
    $specs["categorie_id"]    = "ref class|CConsultationCategorie";
    $specs["_date_min"]       = "date";
    $specs["_date_max"] 	  = "date moreEquals|_date_min";
    $specs["_etat_paiement"]  = "enum list|paye|impaye default|paye";
    $specs["_type_affichage"] = "enum list|complete|totaux";
    $specs["_prat_id"]        = "text";
    return $specs;
  }
  
  function getSeeks() {
    return array(
      "plageconsult_id" => "ref|CPlageconsult",
      "patient_id"      => "ref|CPatient",
      "motif"           => "like",
      "rques"           => "like",
      "examen"          => "like",
      "traitement"      => "like"
    ); 
  }
  
  function getHelpedFields(){
    return array(
      "motif"         => null,
      "rques"         => null,
      "examen"        => null,
      "traitement"    => null,
    );
  }
  
  function getEtat() {
    $etat = array();
    $etat[self::PLANIFIE]       = "Plan.";
    $etat[self::PATIENT_ARRIVE] = mbTranformTime(null, $this->arrivee, "%Hh%M");
    $etat[self::EN_COURS]       = "En cours";
    $etat[self::TERMINE]        = "Term.";
    if($this->chrono)
      $this->_etat = $etat[$this->chrono];
    if ($this->annule) {
      $this->_etat = "Ann.";
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  	$this->_somme = $this->secteur1 + $this->secteur2;
    if($this->date_paiement == "0000-00-00")
      $this->date_paiement = null;
    $this->_hour = intval(substr($this->heure, 0, 2));
    $this->_min  = intval(substr($this->heure, 3, 2));
    $this->_check_premiere = $this->premiere;
    $this->_check_adresse = $this->adresse;
    $this->getEtat();
    $this->_view = "Consultation ".$this->_etat;
  }
  

  
   
  function updateDBFields() {
  	if (($this->_hour !== null) && ($this->_min !== null)) {
      $this->heure = $this->_hour.":".$this->_min.":00";
    }
    
    if ($this->date_paiement == "0000-00-00") {
      $this->date_paiement = null;
    }

    if (($this->_somme !== null) && ($this->_somme != $this->secteur1 + $this->secteur2)){
      $this->secteur1 = 0;
      $this->secteur2 = $this->_somme;
    }
  }

  function loadRefActesNGAP(){
    $acte = new CActeNGAP();
    $where["consultation_id"] = " = '$this->_id'";
    $codesNGAP = $acte->loadList($where);
    
    foreach($codesNGAP as $key => $_ngap){
      $this->_codes_ngap[] = $_ngap->quantite."-".$_ngap->code."-".$_ngap->coefficient; 
    }
  }
  
  function check() {
    // Data checking
    $msg = null;
    if(!$this->consultation_id) {
      if (!$this->plageconsult_id) {
        $msg .= "Plage de consultation non valide<br />";
      }
      if ($this->patient_id === 0) {
        $msg .= "Patient non valide<br />";
      }
    }
    return $msg . parent::check();
  }
  
  
  function loadView() {
  	$this->loadRefPlageConsult();
    $this->loadRefsExamAudio(); 
    $this->loadRefsFwd();
    $this->loadRefsActesCCAM();
  }

  /**
   * Chargement des identifiants des FSE associ�es
   */
  function loadIdsFSE() {
    $id_fse = new CIdSante400();
    $id_fse->setObject($this);
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse = $id_fse->loadMatchingList();
    $this->_ids_fse = CMbArray::pluck($id_fse, "id400");
  }
  
  function bindFSE() {
    // Make id400
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }
    
    $fse = $intermax["FSE"];
    $fseNumero = $fse["FSE_NUMERO_FSE"];
    $id_fse = new CIdSante400();
    $id_fse->object_class = $this->_class_name;
    $id_fse->id400 = $fseNumero;
    $id_fse->tag = "LogicMax FSENumero";
    $id_fse->loadMatchingObject();
    
    // Autre association ?
    if ($id_fse->object_id && $id_fse->object_id != $this->_id) {
      $id_fse->loadTargetObject();
      $consOther =& $id_fse->_ref_object;
      $consOther->loadRefsFwd();
      return sprintf ("FSE d�j� associ�e � la consultation du patient %s par le Dr. %s le %s",
        $consOther->_ref_patient->_view,
        $consOther->_ref_chir->_view,
        mbDateToLocale($consOther->_date));
    }
    
    $id_fse->object_id = $this->_id;
    $id_fse->last_update = mbDateTime();
    return $id_fse->store();
  }

  
  function precodeActe(){
    $this->loadRefPlageConsult();
    foreach($this->_codes_ccam as $key => $code){
      $acte = new CActeCCAM();
      $acte->setCodeComplet($code);
      
      // si le code ccam est compos� de 3 elements, on le precode
      if($acte->code_activite != "" && $acte->code_phase != ""){
        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class_name;
        $acte->executant_id = $this->_ref_chir->_id;
        $acte->execution = mbDateTime();
        if($msg = $acte->store()){
          return $msg;
        }
      }
    }
  }
  
  
  
  function storeCodeNGAP(){
    $listCodesNGAP = array();
    $listCodesNGAP = explode("|",$_POST["codes_ngap"]);
    foreach($listCodesNGAP as $key => $code_ngap){
      $detailCodeNGAP = explode("-", $code_ngap);
      $acteNGAP = new CActeNGAP();
      $where = array();
      $where["quantite"] = " = '$detailCodeNGAP[0]'";
      $where["code"] = " = '$detailCodeNGAP[1]'";
      $where["coefficient"] = " = '$detailCodeNGAP[2]'";
      $where["consultation_id"] = " = '$this->_id'";
      if(!$acteNGAP->loadList($where)){
        $acteNGAP->quantite = $detailCodeNGAP[0];
        $acteNGAP->code = $detailCodeNGAP[1];
        $acteNGAP->coefficient = $detailCodeNGAP[2];
        $acteNGAP->consultation_id = $this->_id;
        $acteNGAP->store();  
      }
    } 
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // Store code NGAP
    if($this->_store_ngap && $this->_id && $_POST["del"] == 0){
      return $this->storeCodeNGAP();
    }
    
    // Precodage des actes
    if($this->_precode_acte && $this->_id){
      return $this->precodeActe();
    }
    
    // Bind FSE
    if ($this->_bind_fse && $this->_id) {
      return $this->bindFSE();
    }
  }
  
  function loadRefCategorie() {
    $this->_ref_categorie = new CConsultationCategorie();
    $this->_ref_categorie->load($this->categorie_id);
  }
  
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }
  
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
  
  function loadRefPlageConsult() {
    if ($this->_ref_plageconsult) {
      return; 
    }

    $this->_ref_plageconsult = new CPlageconsult;
    $this->_ref_plageconsult->load($this->plageconsult_id);
    $this->_ref_plageconsult->loadRefsFwd();
    
    // Foreign fields
    $this->_ref_chir =& $this->_ref_plageconsult->_ref_chir;
    $this->_date = $this->_ref_plageconsult->date;
    $this->_acte_execution = mbAddDateTime($this->heure,$this->_date);
    $this->_is_anesth = $this->_ref_chir->isFromType(array("Anesth�siste"));
    $this->_praticien_id = $this->_ref_plageconsult->_ref_chir->_id;
  }
  
  function loadRefPraticien(){
  	$this->loadRefPlageConsult();
  }
  
  function preparePossibleActes() {
  	$this->loadRefPlageConsult();
  }
  
  
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefPlageConsult();
    $this->_view = "Consult. de ".$this->_ref_patient->_view." par le Dr. ".$this->_ref_plageconsult->_ref_chir->_view;
    $this->_view .= " (".mbTranformTime(null, $this->_ref_plageconsult->date, "%d/%m/%Y").")";
    $this->loadRefsCodesCCAM();
  }

  function loadRefsDocs() {
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $this->_ref_documents = new CCompteRendu();
      $where = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $order = "nom";
      $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
      $docs_valid = count($this->_ref_documents);
    }else{
      $docs_valid = parent::loadRefsDocs();
    }
    return $docs_valid;
  }

  
  function getExecutant_id($code) {
  	$this->loadRefPlageConsult();
    return $this->_praticien_id;
  }
  
  
  function getNumDocsAndFiles(){
  	if(!$this->_nb_files_docs){
      parent::getNumDocsAndFiles();
    }
    if($this->_nb_files_docs) {
      $this->getEtat();
      $this->_etat .= " ($this->_nb_files_docs Doc)";
    }
  }
  
  function getNumDocs(){
  	$this->loadRefConsultAnesth();
    if($this->_ref_consult_anesth->consultation_anesth_id) {
      $select = "count(`compte_rendu_id`) AS `total`";  
      $table  = "compte_rendu";
      $where  = array();
      $where[] = "(`object_class` = 'CConsultation' && `object_id` = '$this->consultation_id')
               || (`object_class` = 'CConsultAnesth' && `object_id` = '".$this->_ref_consult_anesth->consultation_anesth_id."')";
      $sql = new CRequest();
      $sql->addTable($table);
      $sql->addSelect($select);
      $sql->addWhere($where);
      $nbDocs = $this->_spec->ds->loadResult($sql->getRequest());
    }else{
      $nbDocs = parent::getNumDocs();
    }
    return $nbDocs;
  }
  
  function loadRefBanque(){
  	$this->_ref_banque = new CBanque();
  	$this->_ref_banque->load($this->banque_id);	
  }
  
  function loadRefConsultAnesth() {
   
  	$this->_ref_consult_anesth = new CConsultAnesth;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_consult_anesth->loadObject($where);
  }
  
  function loadRefsExamAudio(){
  	 $this->_ref_examaudio = new CExamAudio;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examaudio->loadObject($where);
  }
  
  function loadRefsExamNyha(){
    $this->_ref_examnyha = new CExamNyha;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_examnyha->loadObject($where);
  }
  function loadRefsExamPossum(){
    $this->_ref_exampossum = new CExamPossum;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $this->_ref_exampossum->loadObject($where);
  }
  
  function loadRefsBack() {
    // Backward references
    $this->loadRefsFilesAndDocs();
    $this->getNumDocsAndFiles();
    $this->loadRefConsultAnesth();
    
    $this->loadRefsExamAudio();
    $this->loadExamsComp();
    $this->loadRefsExamNyha();
    $this->loadRefsExamPossum();
    $this->loadRefsActesCCAM();
  }
  
  function loadExamsComp(){
  	$this->_ref_examcomp = new CExamComp;
    $where = array();
    $where["consultation_id"] = "= '$this->consultation_id'";
    $order = "examen";
    $this->_ref_examcomp = $this->_ref_examcomp->loadList($where,$order);
    
    foreach($this->_ref_examcomp as $keyExam => &$currExam){
      $this->_types_examen[$currExam->realisation][$keyExam] = $currExam;
    }
    
  }
  
  function getPerm($permType) {
    if (!$this->_ref_plageconsult) {
      $this->loadRefPlageConsult();
    }
    return $this->_ref_plageconsult->getPerm($permType);
  }
  
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    $this->_ref_plageconsult->loadRefsFwd();
    $this->_ref_plageconsult->_ref_chir->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
    $this->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd();
    $template->addProperty("Consultation - date"      , mbTranformTime("+0 DAY", $this->_ref_plageconsult->date, "%d / %m / %Y") );
    $template->addProperty("Consultation - heure"     , $this->heure);
    $template->addProperty("Consultation - motif"     , $this->motif);
    $template->addProperty("Consultation - remarques" , $this->rques);
    $template->addProperty("Consultation - examen"    , $this->examen);
    $template->addProperty("Consultation - traitement", $this->traitement);
  }
    
  function canDeleteEx() {
    // Date d�pass�e
    $this->loadRefPlageConsult();
    if ($this->_ref_plageconsult->date < mbDate()) {
      return "Imposible de supprimer une consultation pass�e";
    }
    return parent::canDeleteEx();
  }
}

?>