<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * The CFunction Class
 */
class CFunctions extends CMbObject {
  // DB Table key
	var $function_id = null;

  // DB References
  var $group_id = null;

  // DB Fields
  var $type      = null;
	var $text      = null;
  var $soustitre = null;
	var $color     = null;
  var $adresse   = null;
  var $cp        = null;
  var $ville     = null;
  var $tel       = null;
  var $fax       = null;
  
  // Object References
  var $_ref_group = null;
  var $_ref_users = null;

  // Form fields
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
  var $_fax1        = null;
  var $_fax2        = null;
  var $_fax3        = null;
  var $_fax4        = null;
  var $_fax5        = null;
  
	function CFunctions() {
		$this->CMbObject("functions_mediboard", "function_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "group_id" => "notNull ref",
      "type"     => "notNull enum list|administratif|cabinet",
      "text"     => "notNull str confidential",
      "color"    => "notNull str length|6",
      "adresse"  => "text",
      "cp"       => "numchar length|5",
      "ville"    => "str maxLength|50",
      "tel"      => "numchar length|10",
      "fax"      => "numchar length|10",
      "soustitre"=> "text"
    );
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
  
  function updateFormFields() {
		parent::updateFormFields();

    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
    
    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);
    
    $this->_fax1 = substr($this->fax, 0, 2);
    $this->_fax2 = substr($this->fax, 2, 2);
    $this->_fax3 = substr($this->fax, 4, 2);
    $this->_fax4 = substr($this->fax, 6, 2);
    $this->_fax5 = substr($this->fax, 8, 2);
	}
  
  function updateDBFields() {
    if (($this->_tel1 != null) && ($this->_tel2 != null) && ($this->_tel3 != null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }
    
    if (($this->_fax1 != null) && ($this->_fax2 != null) && ($this->_fax3 != null) && ($this->_fax4 !== null) && ($this->_fax5 !== null)) {
      $this->fax = 
        $this->_fax1 .
        $this->_fax2 .
        $this->_fax3 .
        $this->_fax4 .
        $this->_fax5;
    }
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "utilisateurs", 
      "name"      => "users_mediboard", 
      "idfield"   => "user_id", 
      "joinfield" => "function_id"
    );
    
    $tables[] = array (
      "label"     => "plages op�ratoires", 
      "name"      => "plagesop", 
      "idfield"   => "plageop_id", 
      "joinfield" => "spec_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }

  // Forward references
  function loadRefsFwd() {
    $this->_ref_group = new CGroups();
    $this->_ref_group->load($this->group_id);
  }
  
  // Backward references
  function loadRefsBack() {
    $this->loadRefsUsers();
  }
  
  function loadRefsUsers($type = null) {
    if(!$type) {
      $where = array();
      $where["function_id"] = "= '$this->function_id'";
      $ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
      $order = "`users`.`user_last_name`, `users`.`user_first_name`";
      $this->_ref_users = new CMediusers;
      $this->_ref_users = $this->_ref_users->loadList($where, $order, null, null, $ljoin);
    } else {
      $this->_ref_users = new CMediusers;
      $this->_ref_users = $this->_ref_users->loadListFromType($type, PERM_READ, $this->function_id);
    }
  }
  
  // @todo : ameliorer le choix des sp�cialit�s
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites($perm_type = null) {
    global $g;
    $where = array();
    $where["type"] = "= 'cabinet'";
    $where["group_id"] = "= '$g'";
    $order = "text";
    $specs = $this->loadList($where, $order);
  
    // Filter with permissions
    if ($perm_type) {
      foreach ($specs as $keySpec => $spec) {
        if (!$spec->canRead()) {
          unset($specs[$keySpec]);
        }          
      }
    }
    
    return $specs;
  }
  
  function fillTemplate(&$template) {
    $template->addProperty("Cabinet - nom"         , $this->text      );
    $template->addProperty("Cabinet - sous-titre"  , $this->soustitre );
    $template->addProperty("Cabinet - adresse"     , $this->adresse   );
    $template->addProperty("Cabinet - cp ville"    , $this->cp ." ". $this->ville );
    $template->addProperty("Cabinet - t�l�phone"   , $this->tel       );
    $template->addProperty("Cabinet - fax"         , $this->fax       );
  }
}
?>