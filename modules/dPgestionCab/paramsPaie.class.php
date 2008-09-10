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
  var $employecab_id = null;
  
  // Fiscalit�
  var $smic    = null; // valeur du smic horaire
  var $csgnis  = null; // CSG non imposable salariale
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
  var $csp     = null; // contribution solidarit� patronnale
  var $ms      = null; // mutuelle salariale
  var $mp      = null; // mutuelle patronale
  
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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'params_paie';
    $spec->key   = 'params_paie_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["fiches"] = "CFichePaie params_paie_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["employecab_id"] = "notNull ref class|CEmployeCab";
    $specs["smic"]          = "notNull currency|min|0";
    $specs["csgnis"]        = "notNull pct";
    $specs["csgds"]         = "notNull pct";
    $specs["csgnds"]        = "notNull pct";
    $specs["ssms"]          = "notNull pct";
    $specs["ssmp"]          = "notNull pct";
    $specs["ssvs"]          = "notNull pct";
    $specs["ssvp"]          = "notNull pct";
    $specs["rcs"]           = "notNull pct";
    $specs["rcp"]           = "notNull pct";
    $specs["agffs"]         = "notNull pct";
    $specs["agffp"]         = "notNull pct";
    $specs["aps"]           = "notNull pct";
    $specs["app"]           = "notNull pct";
    $specs["acs"]           = "notNull pct";
    $specs["acp"]           = "notNull pct";
    $specs["aatp"]          = "notNull pct";
    $specs["csp"]           = "notNull pct";
    $specs["ms"]            = "notNull currency min|0";
    $specs["mp"]            = "notNull currency min|0";
    $specs["nom"]           = "notNull str confidential";
    $specs["adresse"]       = "text confidential";
    $specs["cp"]            = "numchar length|5 confidential";
    $specs["ville"]         = "str confidential";
    $specs["siret"]         = "numchar length|14 confidential";
    $specs["ape"]           = "str maxLength|6 confidential";
    $specs["matricule"]     = "code insee confidential";
    return $specs;
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_employe = new CEmployeCab;
    $this->_ref_employe->load($this->employecab_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
  
  function loadFromUser($employecab_id) {
    $where = array();
    $where["employecab_id"] = "= '$employecab_id'";
    $this->loadObject($where);
  }
}

?>