<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoriCCAM Class
 */
class CFavoriCCAM extends CMbObject {
  
  var $object_class = null;
  var $favoris_id   = null;
  var $favoris_user = null;
  var $favoris_code = null;
  var $filter_class = null;
  
  var $_ref_code = null;

  function CFavoriCCAM() {
	$this->CMbObject("ccamfavoris", "favoris_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
  	$specs["favoris_user"] = "notNull ref class|CUser";
  	$specs["favoris_code"] = "notNull str length|7";
  	$specs["object_class"] = "notNull str";
  	$specs["filter_class"] = "enum list|CConsultation|COperation|CSejour";
  	return $specs;
  }
  
  function getSeeks() {
    return array (
      "favoris_code" => "equal"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_code = CCodeCCAM::get($this->favoris_code, CCodeCCAM::LITE);
    $this->_ref_code->getChaps();
  }
  
  static function getOrdered($user_id = 0,$class) {
    $listOrdered = array();
    if($user_id) {
      $where["favoris_user"] = "= '$user_id'";
      if($class){
  		$where["object_class"] = "= '$class'";
  	  }
      $order = "favoris_code";
      $fav = new CFavoriCCAM();
      $listFav = $fav->loadList($where, $order);
   
      foreach($listFav as $key => $curr_fav) {
        $code = CCodeCCAM::get($curr_fav->favoris_code, CCodeCCAM::LITE);
        $code->getChaps();
        
        $code->class = $curr_fav->object_class;
        $code->favoris_id = $curr_fav->favoris_id;
        $code->occ = 0;
      
        $chapitre =& $code->chapitres[0];
        $listOrdered[$chapitre["code"]]["nom"] = $chapitre["nom"];
        $listOrdered[$chapitre["code"]]["codes"][$curr_fav->favoris_code] = $code;
      }
    }
    return $listOrdered;
    
  }
}
?>