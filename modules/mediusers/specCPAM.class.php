<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage mediusers
 *  @version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * The CDiscipline Class
 */
class CSpecCPAM extends CMbObject {
  // DB Table key
  var $spec_cpam_id = null;

  // DB Fields
  var $text  = null;
  var $actes = null;

  // Object References
  var $_ref_users = null;

  function CSpecCPAM() {
    $this->CMbObject("spec_cpam", "spec_cpam_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "text"  => "notNull str",
      "actes" => "notNull str"
    );
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
  
  function updateFormFields () {
    parent::updateFormFields();

    $this->_view = strtolower($this->text);
    if(strlen($this->_view) > 25)
      $this->_shortview = substr($this->_view, 0, 23)."...";
    else
      $this->_shortview = $this->_view;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "utilisateurs", 
      "name"      => "users_mediboard", 
      "idfield"   => "user_id", 
      "joinfield" => "spec_cpam_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  // Backward references
  function loadRefsBack() {
    $where = array(
      "spec_cpam_id" => "= '$this->spec_cpam_id'");
    $this->_ref_users = new CMediusers;
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
}

?>