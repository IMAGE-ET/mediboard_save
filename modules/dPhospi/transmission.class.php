<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CTransmissionMedicale. 
 * @abstract Permet d'ajouter des transmissions m�dicales � un s�jour
 */

class CTransmissionMedicale extends CMbMetaObject {

  // DB Table key
  var $transmission_medicale_id = null;	
  
  // DB Fields
  var $sejour_id = null;
  var $user_id   = null;
  
  var $degre = null;
  var $date  = null;
  var $text  = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  var $_ref_cible  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["object_id"]    = "ref class|CMbObject meta|object_class";
  	$specs["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration";
    $specs["sejour_id"]    = "notNull ref class|CSejour";
    $specs["user_id"]      = "notNull ref class|CMediusers";
    $specs["degre"]        = "notNull enum list|low|high default|low";
    $specs["date"]         = "notNull dateTime";
    $specs["text"]         = "text";
    return $specs;
  }
  
  function getHelpedFields(){
    return array(
      "text" => null
    );
  }
  
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
  	if($this->_ref_object){
  	  $this->_ref_object->loadRefsFwd();
  	}
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_view = "Transmission de ".$this->_ref_user->_view;
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
  
}

?>