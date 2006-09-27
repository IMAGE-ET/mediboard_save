<?php /* $Id: modePaiement.class.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

/**
 * The CParamsPaie Class
 */
class CParamsPaie extends CMbObject {
  // DB Table key
  var $params_paie_id = null;

  // DB Fields
  var $user_id = null;
  
  // Fiscalit�
  var $smic    = null; // valeur du smic horaire
  var $csgds   = null; // CSG d�ductible salariale
  var $csgnds  = null; // CSG non d�ductible salariale
  var $ssms    = null; // s�curit� sociale maladie salariale
  var $ssmp    = null; // s�curit� sociale maladie patronale
  var $ssvs    = null; // s�curit� sociale vieillesse salariale
  var $ssvp    = null; // s�curit� sociale vieillesse patronale
  var $rcs     = null; // retraite compl�mentaire salariale
  var $rcp     = null; // retraite compl�mentaire patronale
  var $agffs   = null; // AGFF salariale
  var $agffp   = null; // AGFF patronale
  var $aps     = null; // assurance pr�voyance salariale
  var $app     = null; // assurance pr�voyance patronale
  var $acs     = null; // assurance chomage salariale
  var $acp     = null; // assurance chomage patronale
  var $aatp    = null; // assurance accident de travail patronale
  
  // Employeur
  var $nom     = null;
  var $adresse = null;
  var $cp      = null;
  var $ville   = null;
  var $siret   = null;
  var $ape     = null;
  
  // Utilisateur
  var $matricule = null; // num�ro de s�curit� sociale

  // Object References
  var $_ref_user = null;

  function CParamsPaie() {
    $this->CMbObject("params_paie", "params_paie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "user_id"   => "ref|notNull",
      "smic"      => "currency|min|0|notNull",
      "csgds"     => "pct|notNull",
      "csgnds"    => "pct|notNull",
      "ssms"      => "pct|notNull",
      "ssmp"      => "pct|notNull",
      "ssvs"      => "pct|notNull",
      "ssvp"      => "pct|notNull",
      "rcs"       => "pct|notNull",
      "rcp"       => "pct|notNull",
      "agffs"     => "pct|notNull",
      "agffp"     => "pct|notNull",
      "aps"       => "pct|notNull",
      "app"       => "pct|notNull",
      "acs"       => "pct|notNull",
      "acp"       => "pct|notNull",
      "aatp"      => "pct|notNull",
      "nom"       => "str|notNull|confidential",
      "adresse"   => "str|confidential",
      "cp"        => "num|length|5|confidential",
      "ville"     => "str|confidential",
      "siret"     => "num|length|14|confidential",
      "ape"       => "str|length|4|confidential",
      "matricule" => "code|insee|confidential"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }

  // Forward references
  function loadRefsFwd() {
    // user
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
  
  function loadFromUser($user_id) {
    $where = array();
    $where["user_id"] = "= '$user_id'";
    $this->loadObject($where);
  }
}

?>