<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
 */

CAppUI::requireModuleClass("admin");

/**
 * The CMediusers class
 */
class CMediusers extends CMbObject {
  // DB Table key
  var $user_id = null;

  // DB Fields
  var $remote        = null;
  var $adeli         = null;
  var $titres        = null;
  var $commentaires  = null;
  var $actif         = null;
  var $deb_activite  = null;
  var $fin_activite  = null;
  var $compte        = null;
  var $banque_id     = null;

  // DB References
  var $function_id   = null;
  var $discipline_id = null;
  var $spec_cpam_id  = null;

  // dotProject user fields
  var $_user_type       = null;
  var $_user_username   = null;
  var $_user_password   = null;
  var $_user_password2  = null;
  var $_user_first_name = null;
  var $_user_last_name  = null;
  var $_user_email      = null;
  var $_user_phone      = null;
  var $_user_adresse    = null;
  var $_user_cp         = null;
  var $_user_ville      = null;
  var $_user_last_login = null;
  var $_user_template   = null;

  // Other fields
  var $_view                 = null;
  var $_shortview            = null;
  var $_profile_id           = null;
  var $_is_praticien         = null;
  var $_is_secretaire        = null;
  var $_user_password_weak   = null;
  var $_user_password_strong = null;

  // CPS
  var $_bind_cps = null;
  var $_id_cps   = null;

  // Object references
  var $_ref_banque     = null;
  var $_ref_function   = null;
  var $_ref_discipline = null;
  var $_ref_packs      = array();
  var $_ref_protocoles = array();

  // Object references per day
  var $_ref_plages = null;
  var $_ref_urgences = null;
  var $_ref_deplacees = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'users_mediboard';
    $spec->key   = 'user_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["remote"]        = "bool";
    $specs["adeli"]         = "numchar length|9 confidential mask|99S9S99999S9";
    $specs["function_id"]   = "notNull ref class|CFunctions";
    $specs["discipline_id"] = "ref class|CDiscipline";
    $specs["titres"]        = "text";
    $specs["commentaires"]  = "text";
    $specs["actif"]         = "bool default|1";
    $specs["deb_activite"]  = "date";
    $specs["fin_activite"]  = "date";
    $specs["spec_cpam_id"]  = "ref class|CSpecCPAM";
    $specs["compte"]        = "code rib confidential mask|99999S99999S99999999999S99";
    $specs["banque_id"]     = "ref class|CBanque";
    $specs["_user_username"]   = "notNull str minLength|4";
    $specs["_user_password2"]  = "password sameAs|_user_password";
    $specs["_user_first_name"] = "str";
    $specs["_user_last_name"]  = "notNull str confidential";
    $specs["_user_email"]      = "str confidential";
    $specs["_user_phone"]      = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["_user_adresse"]    = "str confidential";
    $specs["_user_cp"]         = "num length|5 confidential";
    $specs["_user_ville"]      = "str confidential";
    $specs["_profile_id"]      = "num";
    $specs["_user_type"]       = "notNull num minMax|0|20";
      
    // @TODO refactor with only $spec["propName"] = "propSpec" syntax
    
    // The different levels of security are stored to be usable in JS
    $specs["_user_password_weak"]   = "password minLength|4";
    $specs["_user_password_strong"] = "password minLength|6 notContaining|_user_username notNear|_user_username alphaAndNum";

    $specs["_user_password"] = $specs["_user_password_weak"];

    return $specs;
  }
  
  /** Update the object's specs */
  function updateSpecs() {
  	$oldSpec = $this->_specs['_user_password'];
  	
  	$strongPassword = ((CAppUI::conf("admin CUser strong_password") == "1") && ($this->remote == 0));
  	
  	// If the global strong password config is set to TRUE and the user can connect remotely
    $this->_specs['_user_password'] = $strongPassword?
      $this->_specs['_user_password_strong']:
      $this->_specs['_user_password_weak'];
    
    $this->_specs['_user_password']->fieldName = $oldSpec->fieldName;
    
    $this->_props['_user_password'] = $strongPassword?
      $this->_props['_user_password_strong']:
      $this->_props['_user_password_weak'];
  }

  function getSeeks() {
    return array (
      "user_id"  => "ref|CUser"
      );
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["actes_CCAM"]          = "CActeCCAM executant_id";
    $backRefs["aides_saisi"]         = "CAideSaisie user_id";
    $backRefs["modeles"]             = "CCompteRendu chir_id";
    $backRefs["documents_ged"]       = "CDocGed user_id";
    $backRefs["suivi_documents_ged"] = "CDocGedSuivi user_id";
    $backRefs["examens"]             = "CExamenLabo realisateur";
    $backRefs["users"]               = "CFicheEi user_id";
    $backRefs["valid_users"]         = "CFicheEi valid_user_id";
    $backRefs["service_valid_users"] = "CFicheEi service_valid_user_id";
    $backRefs["qualite_users"]       = "CFicheEi qualite_user_id";
    $backRefs["files"]               = "CFile file_owner";
    $backRefs["listes_choix"]        = "CListeChoix chir_id";
    $backRefs["notes"]               = "CNote user_id";
    $backRefs["operations_chir"]     = "COperation chir_id";
    $backRefs["operations_anesth"]   = "COperation anesth_id";
    $backRefs["packs"]               = "CPack chir_id";
    $backRefs["plages_op_chir"]      = "CPlageOp chir_id";
    $backRefs["plages_op_anesth"]    = "CPlageOp anesth_id";
    $backRefs["plages_consult"]      = "CPlageconsult chir_id";
    $backRefs["plages_ressource"]    = "CPlageressource prat_id";
    $backRefs["prescriptions_labo"]  = "CPrescriptionLabo praticien_id";
    $backRefs["protocoles"]          = "CProtocole chir_id";
    $backRefs["sejours"]             = "CSejour praticien_id";
    $backRefs["tarifs"]              = "CTarif chir_id";
    $backRefs["temps_hospi"]         = "CTempsHospi praticien_id";
    $backRefs["temps_chir"]          = "CTempsOp chir_id";
    $backRefs["temps_prepa"]         = "CTempsPrepa chir_id";
    return $backRefs;
  }
   
  function createUser() {
    $user = new CUser();
    $user->user_id = $this->user_id;

    $user->user_type        = $this->_user_type;
    $user->user_username    = $this->_user_username;
    $user->_user_password   = $this->_user_password;
    $user->user_first_name  = $this->_user_first_name;
    $user->user_last_name   = $this->_user_last_name;
    $user->user_email       = $this->_user_email;
    $user->user_phone       = $this->_user_phone;
    $user->user_address1    = $this->_user_adresse;
    $user->user_zip         = $this->_user_cp;
    $user->user_city        = $this->_user_ville;
    $user->template         = 0;
    $user->profile_id       = $this->_profile_id;

    return $user;
  }

  function delete() {
    $msg = null;
    // Delete corresponding dP user first
    if (!$msg = $this->canDeleteEx()) {
      $dPuser = $this->createUser();
      if ($msg = $dPuser->delete()) {
        return $msg;
      }
    }

    return parent::delete();
  }

  function updateFormFields() {
     
    parent::updateFormFields();

    $user = new CUser();
    if($result = $user->load($this->user_id)) {
      $this->_user_type       = $user->user_type;
      $this->_user_username   = $user->user_username;
      $this->_user_password   = $user->user_password;
      $this->_user_first_name = ucwords(strtolower($user->user_first_name));
      $this->_user_last_name  = strtoupper($user->user_last_name) ;
      $this->_user_email      = $user->user_email;
      $this->_user_phone      = $user->user_phone;
      $this->_user_adresse    = $user->user_address1;
      $this->_user_cp         = $user->user_zip;
      $this->_user_ville      = $user->user_city;
      $this->_user_last_login = $user->user_last_login;
      $this->_user_template   = $user->template;
      $this->_profile_id      = $user->profile_id;

      // Encrypt this datas
      $this->checkConfidential();
      $this->_view            = $this->_user_last_name." ".$this->_user_first_name;
      $this->_shortview       = "";
      $arrayLastName = explode(" ", $this->_user_last_name);
      $arrayFirstName = explode("-", $this->_user_first_name);
      foreach($arrayFirstName as $key => $value) {
        if($value != '')
        $this->_shortview .=  strtoupper($value[0]);
      }
      foreach($arrayLastName as $key => $value) {
        if($value != '')
        $this->_shortview .=  strtoupper($value[0]);
      }
    }
    
    $this->updateSpecs();
  }

  function loadRefBanque(){
    $this->_ref_banque = new CBanque();
    $this->_ref_banque->load($this->banque_id);
  }

  function loadRefFunction() {
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }

  function loadRefDiscipline() {
    $this->_ref_discipline = new CDiscipline;
    $this->_ref_discipline->load($this->discipline_id);
  }

  function loadRefsFwd() {
    // Forward references
    $this->loadRefFunction();
    $this->loadRefDiscipline();
  }

  function loadRefsBack() {
    $where = array(
      "chir_id" => "= '$this->user_id'");
    $this->_ref_packs = new CPack;
    $this->_ref_packs = $this->_ref_packs->loadList($where);
  }

  function getPerm($permType) {
    if (!$this->_ref_function) {
      $this->loadRefFunction();
    }
    return $this->_ref_function->getPerm($permType);
  }

  function loadProtocoles() {
    $where["chir_id"] = "= '$this->user_id'";
    $order = "codes_ccam";
    $protocoles = new CProtocole;
    $this->_ref_protocoles = $protocoles->loadList($where, $order);
  }

  /**
   * Lie une num�ro de lecture de CPS au Mediuser
   * @return string Store-like message
   */
  function bindCPS() {
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }

    // Make id400
    $cps = $intermax["CPS"];
    $cpsNumero = $cps["CPS_NUMERO_LOGICMAX"];
    $id_cps = new CIdSante400();
    $id_cps->object_class = $this->_class_name;
    $id_cps->id400 = $cpsNumero;
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();

    // Autre association ?
    if ($id_cps->object_id && $id_cps->object_id != $this->_id) {
      $id_cps->loadTargetObject();
      $medOther =& $id_cps->_ref_object;
      return sprintf("CPS d�j� associ�e � l'utilisateur %s (ADELI: '%s')",
      $medOther->_view,
      $medOther->adeli);
    }

    $id_cps->object_id = $this->_id;
    $id_cps->last_update = mbDateTime();
    return $id_cps->store();
  }

  function loadIdCPS() {
    $id_cps = new CIdSante400();
    if (!$id_cps->_ref_module) {
      return;
    }

    $id_cps->setObject($this);
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();
    $this->_id_cps = $id_cps->id400;
  }

  function loadFromIdCPS($numero_cps) {
    // Make id vitale
    $id_cps = new CIdSante400();
    $id_cps->object_class = $this->_class_name;
    $id_cps->id400 = $numero_cps;
    $id_cps->tag = "LogicMax CPSNumero";
    $id_cps->loadMatchingObject();

    // Load patient from found id vitale
    if ($id_cps->object_id) {
      $this->load($id_cps->object_id);
    }
  }

  /**
   * Map les valeurs venant d'une CPS
   * @return void
   */
  function getValuesFromCPS() {
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }

    $cps = $intermax["CPS"];

    $this->adeli = $cps["CPS_ADELI_NUMERO_CPS"];
    $this->_user_first_name = $cps["CPS_PRENOM"];
    $this->_user_last_name  = $cps["CPS_NOM"];
  }

  function check() {
    // TODO: voir a fusionner cette fonction avec celle de admin.class.php qui est exactement la meme
    // Chargement des specs des attributs du mediuser	
    $this->updateSpecs();
    
    $specsObj = $this->getSpecsObj();

    // On se concentre dur le mot de passe (_user_password)
    $pwdSpecs = $specsObj['_user_password'];

    $pwd = $this->_user_password;

    // S'il a �t� d�fini, on le contr�le (necessaire de le mettre ici a cause du md5)
    if ($pwd) {

      // minLength
      if ($pwdSpecs->minLength > strlen($pwd)) {
        return "Mot de passe trop court (minimum {$pwdSpecs->minLength})";
      }

      // notContaining
      if($target = $pwdSpecs->notContaining) {
        if ($field = $this->$target) {
          if (stristr($pwd, $field)) {
          return "Le mot de passe ne doit pas contenir '$field'";
      } } }
      
	    // notNear
	    if($target = $pwdSpecs->notNear) {
	    	if ($field = $this->$target) {
		      if (levenshtein($pwd, $field) < 3) {
		        return "Le mot de passe ressemble trop � '$field'";
		  } } }
       
      // alphaAndNum
      if($pwdSpecs->alphaAndNum) {
        if (!preg_match("/[A-z]/", $pwd) || !preg_match("/\d+/", $pwd)) {
          return 'Le mot de passe doit contenir au moins un chiffre ET une lettre';
        }
      }
    } else {
      $this->_user_password = null;
    }

    return parent::check();
  }
   
  function store() {
    $this->updateDBFields();
    $this->updateSpecs();

    if ($msg = $this->check()) {
      return CAppUI::tr(get_class( $this )) .
      CAppUI::tr("CMbObject-msg-check-failed") .
      CAppUI::tr($msg);
    }

    // Store corresponding dP user first
    $dPuser = $this->createUser();
    if ($msg = $dPuser->store()) {
      return $msg;
    }

    // User might have been re-created
    if ($this->user_id != $dPuser->user_id) {
      $this->user_id = null;
    }

    // Can't use parent::store cuz user_id don't auto-increment
    if ($this->user_id) {
      $ret = $this->_spec->ds->updateObject($this->_spec->table, $this, $this->_spec->key);
    } else {
      $this->user_id = $dPuser->user_id;
      $ret = $this->_spec->ds->insertObject($this->_spec->table, $this, $this->_spec->key);
    }

    if ($msg = $this->_spec->ds->error()) {
      return $msg;
    }

    // Bind CPS
    if ($this->_bind_cps && $this->_id) {
      return $this->bindCPS();
    }
  }

  function delFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if($perm->loadObject($where)) {
      $perm->delete();
    }
  }

  function insFunctionPermission() {
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CFunctions'";
    $where["object_id"]    = "= '$this->function_id'";

    $perm = new CPermObject;
    if(!$perm->loadObject($where)) {
      $perm = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CFunctions";
      $perm->object_id    = $this->function_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  function insGroupPermission() {
    $function = new CFunctions;
    $function->load($this->function_id);
    $where = array();
    $where["user_id"]      = "= '$this->user_id'";
    $where["object_class"] = "= 'CGroups'";
    $where["object_id"]    = "= '$function->group_id'";

    $perm = new CPermObject;
    if(!$perm->loadObject($where)) {
      $perm = new CPermObject;
      $perm->user_id      = $this->user_id;
      $perm->object_class = "CGroups";
      $perm->object_id    = $function->group_id;
      $perm->permission   = PERM_EDIT;
      $perm->store();
    }
  }

  function loadListFromType($user_types = null, $permType = PERM_READ, $function_id = null, $name = null) {
    $functions = $this->loadFonctions($permType);

    // Filter on a single function
    if ($function_id) {
      $functions = array_key_exists($function_id, $functions) ?
      array($function_id => $functions[$function_id]) :
      array();
    }

    $where = array();
    $where["users_mediboard.function_id"] = $this->_spec->ds->prepareIn(array_keys($functions));
    $where["users_mediboard.actif"] = "= '1'";

    // Filters on users' values
    $ljoin = array();
    $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";

    if ($name) {
      $where["users.user_last_name"] = "LIKE '$name%'";
    }

    $utypes_flip = array_flip(CUser::$types);
    if (is_array($user_types)) {
      foreach ($user_types as $key => $value) {
        $user_types[$key] = $utypes_flip[$value];
      }

      $where["users.user_type"] = $this->_spec->ds->prepareIn($user_types);
    }

    $order = "`users`.`user_last_name`, `users`.`user_first_name`";

    // Get all users
    $mediuser = new CMediusers;
    $mediusers = $mediuser->loadList($where, $order, null, null, $ljoin);

    // Associate already loaded function
    foreach ($mediusers as $keyUser => $mediuser) {
      $mediuser->_ref_function =& $functions[$mediuser->function_id];
    }

    return $mediusers;
  }

  static function loadEtablissements($permType = PERM_READ) {
    // Liste de Tous les �tablissements
    $group = new CGroups;
    $order = "text";
    $groups = $group->loadList(null, $order);

    // Filtre
    foreach($groups as $keyGroupe => $groupe){
      if (!$groupe->getPerm($permType)) {
        unset($groups[$keyGroupe]);
      }
    }

    return $groups;
  }

  /**
   * Load functions with permissions for given group, current group by default
   * @param $permType perm_constant Level of permission
   * @param $group_id ref|CGroup filter on group
   * @return array<CFunctions> Found functions
   */
  static function loadFonctions($permType = PERM_READ, $group_id = null, $type = null) {
    global $g;
    $functions = new CFunctions;
    $functions->group_id = mbGetValue($group_id, $g);
    if($type) {
      $functions->type = $type;
    }
    $order = "text";
    $functions = $functions->loadMatchingList($order);

    if ($permType) {
      foreach ($functions as $_id => $function) {
        if (!$function->getPerm($permType)) {
          unset($functions[$_id]);
        }
      }
    }
    return $functions;
  }

  function loadUsers($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(null, $permType, $function_id, $name);
  }

  function loadMedecins($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("M�decin"), $permType, $function_id, $name);
  }

  function loadChirurgiens($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien"), $permType, $function_id, $name);
  }

  function loadAnesthesistes($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Anesth�siste"), $permType, $function_id, $name);
  }

  function loadPraticiens($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Chirurgien", "Anesth�siste", "M�decin"), $permType, $function_id, $name);
  }

  function loadPersonnels($permType = PERM_READ, $function_id = null, $name = null) {
    return $this->loadListFromType(array("Personnel"), $permType, $function_id, $name);
  }

  function isFromType($user_types) {
    // Warning: !== operator
    return array_search(@CUser::$types[$this->_user_type], $user_types) !== false;
  }

  /**
   * Check whether user is a pratician
   * @return bool
   */
  function isPraticien () {
    return $this->_is_praticien = $this->isFromType(array("M�decin", "Chirurgien", "Anesth�siste"));
  }
  
  /**
   * Check whether user is a secretary
   * @return bool
   */
  function isSecretaire () {
    return $this->_is_secretaire = $this->isFromType(array("Secr�taire", "Administrator"));
  }
  
  /**
   * Check whether user is a medical user
   * @return bool
   */
  function isMedical() {
    return in_array($this->_user_type, array(1, 3, 4, 7, 13));
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_function->fillTemplate($template);
    $template->addProperty("Praticien - nom"       , $this->_user_last_name );
    $template->addProperty("Praticien - pr�nom"    , $this->_user_first_name);
    $template->addProperty("Praticien - sp�cialit�", $this->_ref_discipline->_view);
    $template->addProperty("Praticien - titres"    , $this->titres);
    $template->addProperty("Praticien - ADELI"     , $this->adeli);
  }
  
  /**
   * Charge la liste de plages et op�rations pour un jour donn�
   * Analogue � CSalle::loadRefsForDay
   * @param $date date Date to look for
   */
  function loadRefsForDay($date) {
    // Plages d'op�rations
	  $plages = new CPlageOp;
	  $where = array();
	  $where["date"] = "= '$date'";
  	$where["chir_id"] = "= '$this->_id'";
	  $order = "debut";
		$this->_ref_plages = $plages->loadList($where, $order);
		foreach ($this->_ref_plages as &$plage) {
		  $plage->loadRefs(0);
		  $plage->_unordered_operations = array();
		  foreach ($plage->_ref_operations as &$operation) {
		    $operation->loadRefPatient();
		    $operation->loadExtCodesCCAM();
		    $operation->updateSalle();
		    
		    // Extraire les interventions non plac�es
		    if ($operation->rank == 0) {
		      $plage->_unordered_operations[$operation->_id] = $operation;
		      unset($plage->_ref_operations[$operation->_id]);
		    }
		  }
		}
		
		// Interventions d�plac�s
		$deplacees = new COperation;
		$ljoin = array();
		$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
		$where = array();
		$where["operations.plageop_id"] = "IS NOT NULL";
		$where["plagesop.salle_id"]     = "!= operations.salle_id";
		$where["plagesop.date"]         = "= '$date'";
	  $where["plagesop.chir_id"]      = "= '$this->_id'";
		$order = "operations.time_operation";
		$this->_ref_deplacees = $deplacees->loadList($where, $order, null, null, $ljoin);
		foreach ($this->_ref_deplacees as &$deplacee) {
		  $deplacee->loadRefChir();
		  $deplacee->loadRefPatient();
		  $deplacee->loadExtCodesCCAM();
		}

		// Urgences
	  $urgences = new COperation;
	  $where = array();
	  $where["plageop_id"] = "IS NULL";
	  $where["date"]     = "= '$date'";
		$where["chir_id"]    = "= '$this->_id'";
		$where["annulee"]    = "= '0'";
	  $order = "chir_id";
	  $this->_ref_urgences = $urgences->loadList($where);
	  foreach($this->_ref_urgences as &$urgence) {
	    $urgence->loadRefChir();
	    $urgence->loadRefPatient();
	    $urgence->loadExtCodesCCAM();
	  }
  }
  
  
}

?>