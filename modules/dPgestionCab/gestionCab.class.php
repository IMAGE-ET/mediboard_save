<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CGestionCab Class
 */
class CGestionCab extends CMbObject {
  // DB Table key
  var $gestioncab_id = null;

  // DB Fields
  var $function_id      = null;
  var $libelle          = null;
  var $date             = null;
  var $rubrique_id      = null;
  var $montant          = null;
  var $mode_paiement_id = null;
  var $num_facture      = null;
  var $rques            = null;

  // Object References
  var $_ref_function      = null;
  var $_ref_rubrique      = null;
  var $_ref_mode_paiement = null;

  function CGestionCab() {
    $this->CMbObject("gestioncab", "gestioncab_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "function_id"      => "ref|notNull",
      "libelle"          => "str|notNull",
      "date"             => "date|notNull",
      "rubrique_id"      => "ref|notNull",
      "montant"          => "currency|min|0|notNull",
      "mode_paiement_id" => "ref|notNull",
      "num_facture"      => "num|notNull",
      "rques"            => "text"
    );
    $this->_props =& $props;

    static $seek = array (
      "libelle" => "like"
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
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Fiche '".$this->libelle."'";
  }

  // Forward references
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);

    // rubrique
    $this->_ref_rubrique = new CRubrique();
    $this->_ref_rubrique->load($this->rubrique_id);

    // mode de paiement
    $this->_ref_mode_paiement = new CModePaiement();
    $this->_ref_mode_paiement->load($this->mode_paiement_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_function->getPerm($permType));
  }
}

?>