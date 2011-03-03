<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCategoryPrescription extends CMbObject {
  // DB Table key
  var $category_prescription_id = null;
  
  // DB Fields
  var $chapitre               = null;
  var $nom                    = null;
  var $description            = null;
  var $header                 = null;
  var $group_id               = null;
	var $color                  = null;
  var $prescription_executant = null;
	var $cible_importante       = null;

  // BackRefs
  var $_ref_elements_prescription = null;
	var $_count_elements_prescription = null;
  
	static $chapitres_elt = array('anapath','biologie','imagerie','consult','kine','soin','dm','dmi','ds','med_elt');
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'category_prescription';
    $spec->key   = 'category_prescription_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    //$specs["chapitre"]    = "enum notNull list|".implode('|',self::$chapitres_elt);
    $specs["nom"]         = "str notNull seekable";
    $specs["description"] = "text";
    $specs["header"]      = "text";
    $specs["group_id"]    = "ref class|CGroups";
		$specs["color"]       = "str length|6";
		$specs["prescription_executant"] = "bool default|0";
		$specs["cible_importante"]       = "bool default|0";
		
		$_chapitres = array();
		$conf_presc = CAppUI::conf("dPprescription CPrescription");
		foreach(self::$chapitres_elt as $_chapitre){
			if($conf_presc["show_chapter_$_chapitre"]){
				$_chapitres[] = $_chapitre;
			}
		}
		$specs["chapitre"]  = "enum notNull list|".implode('|', $_chapitres);
		return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["elements_prescription"]   = "CElementPrescription category_prescription_id";
    $backProps["executants_prescription"] = "CExecutantPrescriptionLine category_prescription_id";
    $backProps["functions_category"]      = "CFunctionCategoryPrescription category_prescription_id";
    $backProps["comments_prescription"]   = "CPrescriptionLineComment category_prescription_id";
    $backProps["transmissions"]           = "CTransmissionMedicale object_id";
    $backProps["prescription_category_group_items"] = "CPrescriptionCategoryGroupItem category_prescription_id";
    return $backProps;
  }     
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_view = $this->nom;
  }
  
  
  function loadRefGroup(){
    $group = new CGroups();
    $this->_ref_group = $group->getCached($this->group_id);
  }
  
  function loadElementsPrescription($with_cancelled = true) {
    //$this->_ref_elements_prescription = $this->loadBackRefs("elements_prescription","libelle");
    $element = new CElementPrescription();
		if(!$with_cancelled){
		  $element->cancelled = '0';
		}
		$element->category_prescription_id = $this->_id;
		$this->_ref_elements_prescription = $element->loadMatchingList("libelle");
	}
  
	function countElementsPrescription() {
    $this->_count_elements_prescription = $this->countBackRefs("elements_prescription","libelle");
  }
	
  /**
   * Charge toutes les categories tri�es par chapitre
   * @param $chapitre string Permet de restreindre � un seul chapitre
   * @param $group string 'no_group'     => non associ�es � une clinique
   * 											'current_group => associ�es � la clinique courante
   *                      'current'      => no_group OR current_group 
   *                      'all'          => toutes les categories
   * 								 int	 group_id => group selectionn�
   * @return array[CCategoryPrescription] Les cat�gories
   */
  static function loadCategoriesByChap($chapitre = null, $group="all") {
    global $g;
    
		$categorie = new CCategoryPrescription;
		$where = array();

		if($chapitre){
		  $where["chapitre"] = " = '$chapitre'";
		}
    
		// Permet de filtrer les categories
    if($group != 'all'){
      if(is_numeric($group)){
        $where["group_id"] = " = '$group'";
      }
      if($group == 'no_group'){
        $where["group_id"] = "IS NULL";
      }
      if($group == 'current_group'){
        $where["group_id"] = " = '$g'";
      }
      if($group == 'current'){
        $where[] = "group_id = '$g' OR group_id IS NULL"; 
      }
    }
		
		// Initialisation des chapitres
		$chapitres = explode("|", $categorie->_specs["chapitre"]->list);
		
		$categories_par_chapitre = array();
		foreach ($chapitres as $chapitre) {
		  $categories_par_chapitre[$chapitre] = array();
		}
		
		// Chargement et classement par chapitre
    $order = "nom";
    $categories = $categorie->loadList($where, $order);
    foreach ($categories as &$categorie) {
		  $categories_par_chapitre[$categorie->chapitre]["$categorie->_id"] =& $categorie;
		}
  	ksort($categories_par_chapitre);
  	return $categories_par_chapitre;
  }
}

?>