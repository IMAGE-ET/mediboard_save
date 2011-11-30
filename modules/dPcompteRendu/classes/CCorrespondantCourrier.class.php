<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

class CCorrespondantCourrier extends CMbMetaObject {
  // DB Table key
  var $correspondant_courrier_id = null;

  // DB References
  var $compte_rendu_id = null;
  
  // DB Fields
  var $tag             = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'correspondant_courrier';
    $spec->key   = 'correspondant_courrier_id';
    
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["compte_rendu_id"] = "ref class|CCompteRendu notNull cascade";
    $specs["object_class"] = "enum list|CMedecin|CPatient|CCorrespondantPatient notNull";
    $specs["tag"]          = "str";
    
    return $specs;
  }
}

?>