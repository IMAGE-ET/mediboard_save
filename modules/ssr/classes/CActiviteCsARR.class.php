<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Activite CsARR
 */
class CActiviteCsARR extends CCsARRObject {
  var $code          = null;
  var $hierarchie    = null;
  var $libelle       = null;
  var $libelle_court = null;
  var $ordre         = null;
  
  public $_ref_hierarchie;
  public $_ref_hierarchies;
  public $_ref_modulateurs;
  public $_ref_notes_activites;
  public $_ref_gestes_complementaires;
  public $_ref_activites_complementaires;
    
  static $cached = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'activite';
    $spec->key   = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]          = "str notNull length|7 seekable show|0";
    $props["hierarchie"]    = "str notNull maxLength|12 seekable show|0";
    $props["libelle"]       = "str notNull seekable";
    $props["libelle_court"] = "str notNull seekable show|0";
    $props["ordre"]         = "num max|100";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }
  
  function loadRefHierarchie() {
    return $this->_ref_hierarchie = CHierarchieCsARR::get($this->hierarchie);
  }
  
  function loadRefsHierarchies() {
    // Codes des hi�rarchies interm�diaires
    $parts = explode(".", $this->hierarchie);
    $codes = array();
    foreach ($parts as $_part) {
      $last = $codes[] = count($codes) ? end($codes) . ".$_part" : $_part;
    }
    
    // Chargement des hi�rarchies interm�diaires
    $hierarchie = new CHierarchieCsARR;
    $hierarchies = $hierarchie->loadAll($codes);
    return $this->_ref_hierarchies = $hierarchies;
  }

  function loadRefsNotesActivites() {
    $note = new CNoteActiviteCsARR;
    $note->code = $this->code;
    $notes = array();
    foreach ($note->loadMatchingList("ordre") as $_note) {
      $notes[$_note->typenote][$_note->ordre] = $_note;
    }
    
    return $this->_ref_notes_activites = $notes;
  }
  
  function loadRefsModulateurs() {
    $modulateur = new CModulateurCsARR;
    $modulateur->code = $this->code;
    $modulateurs = $modulateur->loadMatchingList();
    return $this->_ref_modulateurs = $modulateurs;
  }
  
  function loadRefsGestesComplementaires() {
    // Chargement des gestes
    $geste = new CGesteComplementaireCsARR;
    $geste->code_source = $this->code;
    $gestes = $geste->loadMatchingList();
    $this->_ref_gestes_complementaires = $gestes;
    
    // Chargement directes des activit�s correspondantes.
    $codes = CMbArray::pluck($gestes, "code_cible");
    $activite = new CActiviteCsARR;
    $this->_ref_activites_complementaires = $activite->loadAll($codes);
    
    // Retour de gestes
    return $this->_ref_gestes_complementaires;
    
  }
  
	function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
  }

	function loadRefsElements() {
		$element = new CElementPrescriptionToCsarr();
		$element->code = $this->code;
		return $this->_ref_elements = $element->loadMatchingList();
	}
	
	function loadRefsElementsByCat() {
		foreach ($this->loadRefsElements() as $_element){
      $_element->loadRefElementPrescription();
      $this->_ref_elements_by_cat[$_element->_ref_element_prescription->category_prescription_id][] = $_element;
    }
	}
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
  static function get($code) {
  	if (!$code) {
  	  return new self(); 
  	}
  	
    if (!isset(self::$cached[$code])) {
      $activite = new self();
      $activite->load($code);
      self::$cached[$code] = $activite;
    }
    
    return self::$cached[$code];
  }
}

?>