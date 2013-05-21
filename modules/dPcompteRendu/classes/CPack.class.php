<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Gestion de packs de documents
 */
class CPack extends CMbObject {
  // DB Table key
  public $pack_id;

  // DB References
  public $user_id;
  public $function_id;
  public $group_id;

  // DB fields
  public $nom;
  public $object_class;
  public $fast_edit;
  public $fast_edit_pdf;
  public $merge_docs;
  
  // Form fields
  public $_modeles;
  public $_new;
  public $_del;
  public $_source;
  public $_object_class;
  public $_owner;
  public $_header_found;
  public $_footer_found;
  public $_modeles_ids;

  /** @var CMediusers */
  public $_ref_user;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CGroups */
  public $_ref_group;

  /** @var CMediusers|CFunctions|CGroups */
  public $_ref_owner;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack';
    $spec->key   = 'pack_id';
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]       = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";
    $specs["group_id"]      = "ref class|CGroups";
    $specs["nom"]           = "str notNull seekable confidential";
    $specs["object_class"]  = "enum notNull list|CPatient|CConsultAnesth|COperation|CConsultation|CSejour default|COperation";
    $specs["fast_edit"]     = "bool default|0";
    $specs["fast_edit_pdf"] = "bool default|0";
    $specs["merge_docs"]    = "bool default|1";
    $specs["_owner"]        = "enum list|user|func|etab";
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["modele_links"] = "CModeleToPack pack_id";
    return $backProps;
  }
  
  function loadRefOwner() {
    $this->_ref_user     = $this->loadFwdRef("user_id");
    $this->_ref_function = $this->loadFwdRef("function_id");
    $this->_ref_group    = $this->loadFwdRef("group_id");
    
    if ($this->_ref_user->_id) {
      $this->_ref_owner = $this->_ref_user;
    }

    if ($this->_ref_function->_id) {
      $this->_ref_owner = $this->_ref_function;
    }

    if ($this->_ref_group->_id) {
      $this->_ref_owner = $this->_ref_group;
    }
    
    return $this->_ref_owner;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefOwner();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    
    if ($this->user_id) {
      $this->_owner = "user";
    }

    if ($this->function_id) {
      $this->_owner = "func";
    }

    if ($this->group_id) {
      $this->_owner = "etab";
    }

    if (!$this->_object_class) {
      $this->_object_class = "COperation";
    }
  }
  
  /**
   * R�unit les contenus des mod�les pour constituer la source html du pack
   * 
   * @return void
   */
  function loadContent() {
    $this->_source = "";
    $this->loadBackRefs("modele_links", "modele_to_pack_id");

    if (count($this->_back['modele_links']) > 0) {
      $last_key = end(array_keys($this->_back['modele_links']));

      /** @var CModeleToPack $_modeletopack */
      foreach ($this->_back['modele_links'] as $key => $_modeletopack) {
        $modele = $_modeletopack->_ref_modele;
        $modele->loadContent();
        $modele->loadIntroConclusion();
        
        if (!$this->_object_class) {
          $this->_object_class = $modele->object_class;
        }
        
        if ($modele->_ref_preface->_id) {
          $preface = $modele->_ref_preface;
          $preface->loadContent();
          $modele->_source = $preface->_source . "<br />" . $modele->_source;
        }
        
        if ($modele->_ref_ending->_id) {
          $ending = $modele->_ref_ending;
          $ending->loadContent();
          $modele->_source .= "<br />" . $ending->_source;
        }
        
        $this->_source .= $modele->_source;
        
        // Si on est au dernier mod�le, pas de page break
        if ($key === $last_key) {
          break;
        }
        $this->_source .= '<hr class="pagebreak" />';
      }
    }
  }
  
  /**
   * Charge les packs pour un propri�taire donn�
   * 
   * @param int    $id           identifiant du propri�taire
   * @param string $owner        [optional]
   * @param string $object_class [optional]
   * 
   * @todo: refactor this to be in a super class
   * 
   * @return array
   */
  static function loadAllPacksFor($id, $owner = 'user', $object_class = null) {
    $packs = array(
      "user" => array(), // warning: it's not prat like in CCompteRendu
      "func" => array(),
      "etab" => array(),
    );
    
    if (!$id) {
      return $packs;
    }
    
    // Clauses de recherche
    $pack = new CPack();
    $where = array();
    
    if ($object_class) {  
      $where["object_class"] = "= '$object_class'";
    }
    
    $order = "object_class, nom";

    switch ($owner) {
      case 'user': // Mod�le du praticien
        $user = new CMediusers();
        if (!$user->load($id)) {
          return $packs;
        }
        $user->loadRefFunction();

        $where["user_id"]     = "= '$user->_id'";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = "IS NULL";
        $packs["user"] = $pack->loadlist($where, $order);
        
      case 'func': // Mod�le de la fonction
        if (isset($user)) {
          $func_id = $user->function_id;
        }
        else {
          $func = new CFunctions();
          if (!$func->load($id)) {
            return $packs;
          }
          
          $func_id = $func->_id;
        }
        
        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "= '$func_id'";
        $where["group_id"]    = "IS NULL";
        $packs["func"] = $pack->loadlist($where, $order);
        
      case 'etab': // Mod�le de l'�tablissement
        $etab_id = CGroups::loadCurrent()->_id;
        if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) {
            return $packs;
          }
          
          $etab_id = $etab->_id;
        }
        else if (isset($func)) {
          $etab_id = $func->group_id;
        }
        else if (isset($func_id)) {
          $func = new CFunctions();
          $func->load($func_id);
          
          $etab_id = $func->group_id;
        }
        
        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = " = '$etab_id'";
        $packs["etab"] = $pack->loadlist($where, $order);
    }
    
    return $packs;
  }
  
  function loadHeaderFooter() {
    if (!isset($this->_back['modele_links'])) {
      $this->loadBackRefs("modele_links", "modele_to_pack_id");
    }
    
    $header_id = null;
    $footer_id = null;
    
    foreach ($this->_back['modele_links'] as $mod) {
      $modele = $mod->_ref_modele;
      
      if ($modele->header_id || $modele->footer_id) {
        $header_id = $modele->header_id;
        $footer_id = $modele->footer_id;
      }
      if (!$header_id && $modele->header_id) {
        $header_id = $modele->header_id;
      }
      if (!$footer_id && $modele->footer_id) {
        $footer_id = $modele->footer_id;
      }
      if ($header_id && $footer_id) {
        break;
      }
    }
    
    $this->_header_found = new CCompteRendu();
    if ($header_id) {
      $this->_header_found->load($header_id);
    }
    
    $this->_footer_found = new CCompteRendu();
    if ($footer_id) {
      $this->_footer_found->load($footer_id);
    }
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return $this->_ref_user->getPerm($permType);
  }
  
  function getModelesIds() {
    $ds = $this->_spec->ds;
    
    $request = new CRequest();
    $request->addSelect("modele_id");
    $request->addTable("modele_to_pack");
    $request->addWhere("pack_id = '$this->_id'");
    $this->_modeles_ids = $ds->loadColumn($request->getRequest());
  }
}
