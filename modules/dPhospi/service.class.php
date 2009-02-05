<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CService. 
 * @abstract Gère les services d'hospitalisation
 * - contient de chambres
 */
class CService extends CMbObject {
  // DB Table key
	var $service_id = null;	
  
  // DB references
  var $group_id = null;

  // DB Fields
  var $nom = null;
  var $description = null;
  var $urgence = null;
  
  // Object references
  var $_ref_chambres = null;
  var $_ref_group    = null;
  var $_ref_validrepas = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'service';
    $spec->key   = 'service_id';
    return $spec;
  }

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["chambres"]               = "CChambre service_id";
	  $backRefs["product_deliveries"]     = "CProductDelivery service_id";
	  $backRefs["product_stock_services"] = "CProductStockService service_id";
	  $backRefs["valid_repas"]            = "CValidationRepas service_id";
	  return $backRefs;
	}

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["group_id"]    = "ref notNull class|CGroups";
    $specs["nom"]         = "str notNull";
    $specs["description"] = "text";
    $specs["urgence"]     = "bool";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom"         => "like",
      "description" => "like"
    );
  }
  
  function updateFormFields(){
    $this->_view = $this->nom;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["group_id"] = "= '$g->_id'";
    
    $list = $this->loadList($where, $order, $limit, $groupby, $ljoin);
    return $list;
  }

  function loadRefsBack() {
    // Backward references
    $where["service_id"] = "= '$this->service_id'";
    $order = "nom";
    $this->_ref_chambres = new CChambre;
    $this->_ref_chambres = $this->_ref_chambres->loadList($where, $order);
  }

  function loadRefsFwd(){
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_group) {
      $this->loadRefsFwd();
    }
    return (CPermObject::getPermObject($this, $permType) && $this->_ref_group->getPerm($permType));
  }
   
  function validationRepas($date, $listTypeRepas = null){
    $this->_ref_validrepas[$date] = array();
    $validation =& $this->_ref_validrepas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }
    
    $where               = array();
    $where["date"]       = $this->_spec->ds->prepare(" = %", $date);
    $where["service_id"] = $this->_spec->ds->prepare(" = %", $this->service_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $validrepas = new CValidationRepas;
      $validrepas->loadObject($where);
      $validation[$keyType] = $validrepas;
    }
  }
  
  /**
   * Charge les services d'urgence de l'établissement courant
   * @return array|CService
   */
  static function loadServicesUrgence() {
    global $g;
    $service = new CService();
    $service->group_id = $g;
    $service->urgence = "1";
    $services = $service->loadMatchingList();
    foreach ($services as $_service) {
      $_service->loadRefsBack();
      foreach ($_service->_ref_chambres as $_chambre) {
        $_chambre->loadRefsBack();
      }
    }
    
    return $services;
  }
}
?>