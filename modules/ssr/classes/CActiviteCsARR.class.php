<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Activite CsARR
 */
class CActiviteCsARR extends CCsARRObject {
  public $code;
  public $hierarchie;
  public $libelle;
  public $libelle_court;
  public $ordre;

  // Refs
  public $_ref_reference;
  public $_ref_hierarchie;
  public $_ref_hierarchies;
  public $_ref_modulateurs;
  public $_ref_notes_activites;
  public $_ref_gestes_complementaires;
  public $_ref_activites_complementaires;

  // Counts
  public $_count_elements;
  public $_count_actes;
  public $_count_actes_by_executant;

  // Distant refs
  public $_ref_elements;
  public $_ref_elements_by_cat;
  public $_ref_all_executants;

  static $cached = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'activite';
    $spec->key   = 'code';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
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

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }

  /**
   * Charge la hi�rarchie parente
   *
   * @return CActiviteCdARR
   */
  function loadRefHierarchie() {
    return $this->_ref_hierarchie = CHierarchieCsARR::get($this->hierarchie);
  }

  /**
   * Charge toutes les hi�rarchies anc�tres
   *
   * @return CHierarchieCsARR[]
   */
  function loadRefsHierarchies() {
    // Codes des hi�rarchies interm�diaires
    $parts = explode(".", $this->hierarchie);
    $codes = array();
    foreach ($parts as $_part) {
      $codes[] = count($codes) ? end($codes) . ".$_part" : $_part;
    }

    // Chargement des hi�rarchies interm�diaires
    $hierarchie = new CHierarchieCsARR();
    $hierarchies = $hierarchie->loadAll($codes);
    return $this->_ref_hierarchies = $hierarchies;
  }

  /**
   * Charge les notes associ�s, par type puis par ordre
   *
   * @return CNoteActiviteCsARR[][]
   */
  function loadRefsNotesActivites() {
    $note = new CNoteActiviteCsARR();
    $note->code = $this->code;
    $notes = array();
    /** @var CNoteActiviteCsARR $_note */
    foreach ($note->loadMatchingList("ordre") as $_note) {
      $notes[$_note->typenote][$_note->ordre] = $_note;
    }

    return $this->_ref_notes_activites = $notes;
  }

  /**
   * Charge les modulateurs associ�s
   *
   * @return CModulateurCsARR[]
   */
  function loadRefsModulateurs() {
    $modulateur = new CModulateurCsARR();
    $modulateur->code = $this->code;
    $modulateurs = $modulateur->loadMatchingList();
    return $this->_ref_modulateurs = $modulateurs;
  }

  /**
   * Charge la reference asoci�e
   *
   * @return CReferenceActiviteCsARR[]
   */
  function loadRefReference() {
    $reference = new CReferenceActiviteCsARR();
    $reference->code = $this->code;
    $reference->loadMatchingObject();
    return $this->_ref_reference = $reference;
  }


  /**
   * Chage les gestes compl�mentaires associ�s
   *
   * @return CActiviteCsARR[]
   */
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

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
  }

  /**
   * Compte les liaisons avec de �l�ments de prescription
   *
   * @return int
   */
  function countElements() {
    $element = new CElementPrescriptionToCsarr();
    $element->code = $this->code;
    return $this->_count_elements = $element->countMatchingList();
  }

  /**
   * Charge les liaisons avec des �l�ments de prescription
   *
   * @return CElementPrescriptionToCdarr[]
   */
  function loadRefsElements() {
    $element = new CElementPrescriptionToCsarr();
    $element->code = $this->code;
    return $this->_ref_elements = $element->loadMatchingList();
  }

  /**
   * Charge les �l�ments de prescriptions associ�s par cat�gorie
   *
   * @return CElementPrescription[][]
   */
  function loadRefsElementsByCat() {
    $this->_ref_elements_by_cat = array();
    foreach ($this->loadRefsElements() as $_element) {
      if ($element = $_element->loadRefElementPrescription()) {
        $this->_ref_elements_by_cat[$element->category_prescription_id][] = $_element;
      }
    }
    return $this->_ref_elements_by_cat;
  }

  /**
   * Compte les actes CdARR pour ce code d'activit�
   *
   * @return int
   */
  function countActes() {
    $acte = new CActeCdARR();
    $acte->code = $this->code;
    return $this->_count_actes = $acte->countMatchingList();
  }

  /**
   * Charge les ex�cutants de cet activit� et fournit le nombre d'occurences par ex�cutants
   *
   * @return CMediusers[]
   *
   * @see self::_count_actes_by_executant
   */
  function loadRefsAllExecutants() {
    // Comptage par executant
    $query = "SELECT therapeute_id, COUNT(*)
      FROM `acte_csarr` 
      LEFT JOIN `evenement_ssr` ON  `evenement_ssr`.`evenement_ssr_id` = `acte_csarr`.`evenement_ssr_id`
      WHERE `code` = '$this->code'
      GROUP BY `therapeute_id`";
    $acte = new CActeCsARR();  
    $ds = $acte->getDS();
    $counts = $ds->loadHashList($query);
    arsort($counts);

    // Chargement des executants
    $user = new CMediusers;
    /** @var CMediusers[] $executants */
    $executants = $user->loadAll(array_keys($counts));
    foreach ($executants as $_executant) {
      $_executant->loadRefFunction();
    }

    // Valeurs de retour
    $this->_count_actes_by_executant = $counts;
    return $this->_ref_all_executants = $executants;
  }


  /**
   * Charge une activit� par le code
   *
   * @param string $code Code d'activit�
   *
   * @return self
   */
  static function get($code) {
    if (!$code) {
      return new self();
    }

    if ($activite = SHM::get("activite_csarr_$code")) {
      $activite->loadRefReference();
      $activite->loadRefsModulateurs();
      return $activite;
    }

    $activite = new self();
    $activite->load($code);
    SHM::put("activite_csarr_$code", $activite);

    $activite->loadRefReference();
    $activite->loadRefsModulateurs();

    return $activite;
  }
}
