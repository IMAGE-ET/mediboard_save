<?php /* $Id: compteRendu.class.php 9309 2010-06-28 16:17:19Z flaviencrochard $ */
  
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 9309 $
* @author Romain Ollivier
*/

class CContentHTML extends CMbObject {
  // DB Table key
  var $content_id = null;
  
  // DB Fields
  var $content = null;

  // Form fields
  var $_list_classes = null;
  
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_html';
    $spec->key   = 'content_id';
    return $spec;
  }
  
  function getProps() { 
    $specs = parent::getProps();
    $specs["_list_classes"]    = "enum list|CBloodSalvage|CConsultAnesth|CConsultation|CDossierMedical|CFunctions|CGroups|CMediusers|COperation|CPatient|CPrescription|CSejour";
    $specs["content"] = "html helped|_list_classes";
    return $specs;
  }
}
