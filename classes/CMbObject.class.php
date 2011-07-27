<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("CStoredObject");

/**
 * @abstract Mediboard business object layer 
 * Handles: notes, documents, aides, views, affectations personnels (!), model templates, echanges
 */
class CMbObject extends CStoredObject {
  var $_aides         = array(); // Aides � la saisie
  var $_aides_new     = array(); // Nouveau tableau des aides (sans hierarchie)
  var $_nb_files_docs = null;
  var $_nb_files      = null;
  var $_nb_docs       = null;
  var $_nb_exchanges           = null;
  var $_nb_exchanges_by_format = array();
    
  var $_ref_last_id400 = null;
  var $_ref_notes      = null; // Notes
  var $_ref_documents  = array(); // Documents
  var $_ref_files      = array(); // Fichiers
  var $_ref_affectations_personnel  = null;
  var $_ref_object_configs = null; // Object configs
  var $_ref_tag_items  = array(); // Object tag items

  /**
   * Chargement des notes sur l'objet
   * @param $perm One of PERM_READ | PERM_EDIT
   * @return int Note count
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = array();
    $this->_degree_notes = null;
    $notes_levels = array();
    
    if ($this->_id) {
      $this->_ref_notes = CNote::loadNotesForObject($this, $perm);
      
      // Find present levels
      foreach($this->_ref_notes as $_note) {
        $notes_levels[$_note->degre] = true;
      }
      
      // Note highest level 
      if (isset($notes_levels["low"   ])) $this->_degree_notes = "low";
      if (isset($notes_levels["medium"])) $this->_degree_notes = "medium";
      if (isset($notes_levels["high"  ])) $this->_degree_notes = "high";
    }

    return count($this->_ref_notes);
  }

  /**
   * Load files for object with PERM_READ
   * @return int file count
   */
  function loadRefsFiles() {
    if (null == $this->_ref_files = $this->loadBackRefs("files", "file_name")) {
      return;
    }
    
    // Read permission
    foreach ($this->_ref_files as $_file) {
      if (!$_file->canRead()){
        unset($this->_ref_files[$_file->_id]);
      }
    }
    
    return count($this->_ref_files);
  }

  /**
   * Load documents for object
   * @return int document count
   */
  function loadRefsDocs() {
    if (!$this->_id) return;
    $document = new CCompteRendu();

    if ($document->_ref_module) {
      $document->object_class = $this->_class_name;
      $document->object_id    = $this->_id;
      $this->_ref_documents = $document->loadMatchingList("nom");
      $is_editable = $this->docsEditable();
      foreach($this->_ref_documents as $_doc) {
        $_doc->_is_editable = $is_editable;
        if (!$_doc->canRead()){
           unset($this->_ref_documents[$_doc->_id]);
        }
      }
      return count($this->_ref_documents);
    }
  }
  
  /**
   * Load documents and files for object
   * @return int document + files count
   */
  function loadRefsDocItems() {
    $this->nb_files = $this->loadRefsFiles();
    $this->nb_docs  = $this->loadRefsDocs();
    $this->_nb_files_docs = $this->_nb_files + $this->_nb_docs;
  }
  
  /**
   * Count documents
   * @return int
   */
  function countDocs() {
    return $this->_nb_docs = $this->countBackRefs("documents");
  }
  
  /**
   * Count files
   * @return int
   */
  function countFiles(){
    return $this->_nb_files = $this->countBackRefs("files");
  }
  
  /**
   * Count doc items (that is documents and files), delegate when permission type defined
   * @param $permType int Permission type, one of PERM_READ, PERM_EDIT
   * @return int
   */
  function countDocItems($permType = null) {
    $this->_nb_files_docs = $permType ? 
      $this->countDocItemsWithPerm($permType) : 
      $this->countFiles() + $this->countDocs();
    return $this->_nb_files_docs;
  }
  
  /**
   * Count doc items according to given permission
   * @todo Merge with countDocItems(), unnecessary delegation
   * @param $permType int Permission type, one of PERM_READ, PERM_EDIT
   * @return int
   */
  function countDocItemsWithPerm($permType = PERM_READ){
    $this->loadRefsFiles();
    if ($this->_ref_files) {
      self::filterByPerm($this->_ref_files, $permType);
      $this->_nb_files = count($this->_ref_files);
    }
    
    $this->loadRefsDocs();
    if ($this->_ref_documents) {
      self::filterByPerm($this->_ref_documents, $permType);
      $this->_nb_docs = count($this->_ref_documents);
    }
    
    return $this->_nb_files + $this->_nb_docs;
  }
  
  /**
   * Count exchanges, make totals by format
   * @return int The absolute total
   */
  function countExchanges() {
    foreach (CExchangeDataFormat::getAll() as $_data_format) {
      $data_format = new $_data_format;
      if (!$data_format->hasTable()) {
        continue;
      }
      $data_format->object_id    = $this->_id;
      $data_format->object_class = $this->_class_name;

      $this->_nb_exchanges_by_format[$_data_format] = $data_format->countMatchingList();
    }
    
    foreach ($this->_nb_exchanges_by_format as $_nb_exchange_format) {
      $this->_nb_exchanges += $_nb_exchange_format;
    }
    
    return $this->_nb_exchanges;
  }
      
  
  /**
   * Chargement du dernier identifiant id400
   * @param $tag string Tag � utiliser comme filtre
   */
  function loadLastId400($tag = null) {
    $id400 = new CIdSante400();
    if($id400->_ref_module) {
      $id400->loadLatestFor($this, $tag);
      $this->_ref_last_id400 = $id400;
    }
  } 
    
  /**
   * Load object view information 
   */
  function loadView() {
    $this->loadRefsNotes();
    $this->loadAllFwdRefs();
  }
  
  /**
   * Load complete object view information 
   */
  function loadComplete() {
    $this->loadRefsNotes();
    $this->loadRefs();
  }
  
  /**
   * Back references global loader
   * DEPRECATED: out of control resouce consumption
   * @return id Object id
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadExternal();
  }
  
  function loadExternal() {
    $this->_external = $this->countBackRefs("identifiants");
  }

  /**
   * Get backward reference specifications
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    return parent::getBackProps() + array(
      "alerts"                 => "CAlert object_id",
      "identifiants"           => "CIdSante400 object_id",
      "notes"                  => "CNote object_id",
      "files"                  => "CFile object_id",
      "documents"              => "CCompteRendu object_id",
      "permissions"            => "CPermObject object_id",
      "logs"                   => "CUserLog object_id",
      "affectations_personnel" => "CAffectationPersonnel object_id",
      "contextes_constante"    => "CConstantesMedicales context_id",
      "modeles_etiquettes"     => "CModeleEtiquette object_id",
      "tag_items"              => "CTagItem object_id",
      "echange_generique"      => "CExchangeAny object_id",
      //"ex_objects"             => "CExObject object_id", // NE PAS DECOMMENTER CETTE LIGNE, backref impossible pour le moment (cf. Fabien)
    );
  }
  
  /**
   * Charge toutes les aides � la saisie de l'objet pour un utilisateur donn�
   *
   * @param ref|CUser $user_id  Utilisateur
   * @param string    $keywords Permet de filtrer les aides comman�ant par le filtre, si non null
   */
  function loadAides($user_id, $keywords = null, $depend_value_1 = null, $depend_value_2 = null, $object_field = null, $strict = "true") {
    foreach ($this->_specs as $field => $spec) {
      if (isset($spec->helped)) {
        $this->_aides[$field] = array("no_enum" => null);
      }
    }

    // Chargement de l'utilisateur courant
    $user = new CMediusers();
    $user->load($user_id);
    $user->loadRefFunction();
    
    // Pr�paration du chargement des aides
    $ds =& $this->_spec->ds;
    
    // Construction du Where
    $where = array();

    $where[] = "(user_id = '$user_id' OR 
      function_id = '$user->function_id' OR 
      group_id = '{$user->_ref_function->group_id}')";
                
    $where["class"]   = $ds->prepare("= %", $this->_class_name);

    if ($strict == "true") {
      if ($depend_value_1){
        $where["depend_value_1"] = " = '$depend_value_1'";
      }
      
      if ($depend_value_2){
        $where["depend_value_2"] = " = '$depend_value_2'";
      }
    }
    else {
      if ($depend_value_1){
        $where[] = "(depend_value_1 = '$depend_value_1' OR depend_value_1 IS NULL)";
      }
      if ($depend_value_2){
        $where[] = "(depend_value_2 = '$depend_value_2' OR depend_value_2 IS NULL)";
      }
    }
    
    if($object_field){
      $where["field"] = " = '$object_field'";
    }
    
    // tri par user puis function puis group (ordre invers� pour avoir ce r�sultat)
    $order = "group_id, function_id, user_id, depend_value_1, depend_value_2, name, text";
    
    // Chargement des Aides de l'utilisateur
    $aide = new CAideSaisie();
    $aides = $aide->seek($keywords, $where, null, null, null, $order); // TODO: si on veut ajouter un $limit, il faudrait l'ajouter en argument de la fonction loadAides
    $this->orderAides($aides, $depend_value_1, $depend_value_2);
  }
  
  function orderAides($aides, $depend_value_1 = null, $depend_value_2 = null) {
    foreach ($aides as $aide) { 
      $owner = CAppUI::tr("CAideSaisie._owner.$aide->_owner");
      $aide->loadRefOwner();
      
      // si on filtre seulement sur depend_value_1, il faut afficher les resultats suivant depend_value_2
      if ($depend_value_1) {
        $depend_field_2 = $aide->_depend_field_2;
        $depend_2 = CAppUI::tr("$this->_class_name.$aide->_depend_field_2.$aide->depend_value_2");
        if ($aide->depend_value_2){
          $this->_aides[$aide->field][$owner][$depend_2][$aide->text] = $aide->name;
        } 
        else {
          $depend_name_2 = CAppUI::tr("$this->_class_name-$depend_field_2");
          $this->_aides[$aide->field][$owner]["$depend_name_2 non sp�cifi�"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      // ... et r�ciproquement 
      if ($depend_value_2){
        $depend_field_1 = $aide->_depend_field_1;
        $depend_1 = CAppUI::tr("$this->_class_name.$aide->_depend_field_1.$aide->depend_value_1");
        if ($aide->depend_value_1){    
          $this->_aides[$aide->field][$owner][$depend_1][$aide->text] = $aide->name;
        } 
        else {
          $depend_name_1 = CAppUI::tr("$this->_class_name-$depend_field_1");
          $this->_aides[$aide->field][$owner]["$depend_name_1 non sp�cifi�"][$aide->text] = $aide->name;
        }
        continue;
      }
      
      $this->_aides_all_depends[$aide->field][$aide->depend_value_1][$aide->depend_value_2][$aide->_id] = $aide;
      
      // Ajout de l'aide � la liste g�n�rale
      $this->_aides[$aide->field]["no_enum"][$owner][$aide->text] = $aide->name;
    }
    
    $this->_aides_new = $aides;
  }
  
  /**
   * Chargement des affectations de personnel par emplacements
   * @return unknown_type
   */
  function loadAffectationsPersonnel() {
    // Initialisation
    $personnel = new CPersonnel();
    foreach ($personnel->_specs["emplacement"]->_list as $emplacement) {
      $this->_ref_affectations_personnel[$emplacement] = array();
    }
    
    // Module actif
    if (null == $affectations = $this->loadBackRefs("affectations_personnel")) {
      return;
    }
    
    // Chargement et classement
    foreach ($affectations as $key => $affectation) {
      $affectation->loadRefPersonnel();
      $affectation->_ref_personnel->loadRefUser();
      $affectation->_ref_personnel->_ref_user->loadRefFunction();
      $this->_ref_affectations_personnel[$affectation->_ref_personnel->emplacement][$affectation->_id] = $affectation;
    }
    
    return $this->_ref_affectations_personnel;
  }

  /**
   * Load the object's tag items
   * @return array
   */
  function loadRefsTagItems($cache = true) {
    if ($cache && !empty($this->_ref_tag_items)) {
      return $this->_ref_tag_items;
    }
    
    return $this->_ref_tag_items = $this->loadBackRefs("tag_items");
  }

  /**
   * Get the object's tags
   * @return array
   */
  function getTags($cache = true) {
    $tag_items = $this->loadRefsTagItems($cache = true);
    return CMbArray::pluck($tag_items, "_ref_tag");
  }
  
  /**
   * Get the related object by class for template filling
   * @return array Collection of class => id relations
   */
  function getTemplateClasses(){
    return array($this->_class_name => $this->_id);
  }
  
  /**
   * This function register all templated properties for the object
   * Will load as necessary and fill in values
   * @param $template CTemplateManager
   */
  function fillTemplate(&$template) {
  }
   
  /**
   * This function register most important templated properties for the object
   * Won't register distant properties
   * Will load as necessary and fill in values
   * @param $template CTemplateManager
   **/
  function fillLimitedTemplate(&$template) {
  }
   
  /**
   * Load object config 
   * @return array contains config of class and/or object
   */
  function loadConfigValues() {
    $object_class = $this->_class_name."Config";
    
    if (!class_exists($object_class)) {
      return;
    }
    
    // Chargement des configs de la classe
    $where = array();
    $where["object_id"]    = " IS NULL";
    $class_config = new $object_class;
    $class_config->loadObject($where);
    
    if (!$class_config->_id) {
      $class_config->valueDefaults();
    }
    
    // Chargement des configs de l'objet
    $object_config = $this->loadUniqueBackRef("object_configs");

    $class_config->extendsWith($object_config);

    $this->_configs = $class_config->getConfigValues();
  }
  
  /**
   * Get value of the object config 
   */
  function getConfigValues() {
    $configs = array();
    
    $fields = $this->getDBFields();
    unset($fields[$this->_spec->key]);
    unset($fields["object_id"]);
    foreach($fields as $_name => $_value) {
      $configs[$_name] = $_value;
    }
    
    return $configs;
  }
  
  
  /**
   * Backward references
   */
  function loadRefObjectConfigs() {
    $object_class = $this->_class_name."Config";
    if (class_exists($object_class)) {
      $this->_ref_object_configs = $this->loadUniqueBackRef("object_configs");
    }
  }
  
  /**
   * Evaluate if an object is editable according to a date. 
   * return bool 
   */
  function docsEditable() {
    // Un admin doit toujours pouvoir modifier un document
    global $can;
    return $can->admin;
  }
    
  /**
   * Returns the path to the class-specific template
   * 
   * @param string $type view|autocomplete|edit
   * @return string
   */
  function getTypedTemplate($type) {
    if (!in_array($type, array("view", "autocomplete", "edit"))) {
      return;
    }
    
    $mod_name = $this->_ref_module->mod_name;
    $template = "$mod_name/templates/{$this->_class_name}_$type.tpl";
    
    if (!is_file("modules/$template")) {
      $template = "system/templates/CMbObject_$type.tpl";
    }
    
    return "../../$template";
  }
  
  /*
   * Make and return usefull template paths for given object
   * @param string $name One of "view" and "complete"
   * @return string Path to wanted template, null if module undefined for object
   */
  function makeTemplatePath($name) {
    if ($module = $this->_ref_module) {
      $path = "$module->mod_name/templates/$this->_class_name";
      return "{$path}_{$name}.tpl";
    }
  }
  
}
