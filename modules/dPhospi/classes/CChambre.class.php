<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe CChambre. 
 * @abstract G�re les chambre d'hospitalisation
 * - contient des lits
 */
class CChambre extends CMbObject {
  
  static $_prefixe;
  
  // DB Table key
  public $chambre_id;
  
  // DB References
  public $service_id;

  // DB Fields
  public $nom;
  public $caracteristiques; // c�t� rue, fen�tre, lit accompagnant, ...
  public $lits_alpha;
  public $annule;

  // Form Fields
  public $_nb_lits_dispo;
  public $_nb_affectations;
  public $_overbooking;
  public $_ecart_age;
  public $_genres_melanges;
  public $_chambre_seule;
  public $_chambre_double;
  public $_conflits_chirurgiens;
  public $_conflits_pathologies;

  // Object references
  /** @var CService */
  public $_ref_service;

  /** @var CLit[] */
  public $_ref_lits;

  /** @var CEmplacement */
  public $_ref_emplacement;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'chambre';
    $spec->key   = 'chambre_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lits"]         = "CLit chambre_id";
    $backProps["ufs"]          = "CAffectationUniteFonctionnelle object_id";
    $backProps["emplacement"]  = "CEmplacement chambre_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["service_id"]       = "ref notNull class|CService seekable";
    $specs["nom"]              = "str notNull seekable";
    $specs["caracteristiques"] = "text";
    $specs["lits_alpha"]       = "bool default|0";
    $specs["annule"]           = "bool";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_shortview = self::$_prefixe . $this->nom;
    $this->_view      = $this->_shortview;
  }

  /**
   * Load service
   *
   * @return CMbObject|null
   */
  function loadRefService() {
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefService();
  }

  /**
   * Load lits
   *
   * @param bool $annule Annul�
   *
   * @return CLit[]
   */
  function loadRefsLits($annule = false) {
    $lit = new CLit();
    $where = array(
      "chambre_id" => "= '$this->_id'"
    );
    
    if (!$annule) {
      $where["annule"] = " ='0'";
    }
    
    if ($this->lits_alpha) {
      $order = "lit.nom ASC";
    }
    else {
      $order = "lit.nom DESC";
    }
    
    return $this->_ref_lits = $this->_back["lits"] = $lit->loadList($where, $order);
  }

  /**
   * Load emplacements
   *
   * @return CEmplacement
   */
  function loadRefEmplacement() {
    $emplacement = new CEmplacement();
    $emplacement->loadObject("chambre_id = '$this->chambre_id'");
    $this->_ref_emplacement = $emplacement;

    return $this->_ref_emplacement;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $this->loadRefsLits();
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefService();
    return ($this->_ref_service->getPerm($permType));
  }

  /**
   * Check room
   *
   * @return void
   */
  function checkChambre() {
    static $pathos = null;
    if (!$pathos) {
      $pathos = new CDiscipline();
    }
    
    assert($this->_ref_lits !== null);
    $this->_nb_lits_dispo = count($this->_ref_lits);

    /** @var CAffectation[] $listAff */
    $listAff = array();
    
    $this->_chambre_seule        = 0;
    $this->_chambre_double       = 0;
    $this->_conflits_pathologies = 0;
    $this->_ecart_age            = 0;
    $this->_genres_melanges      = false;
    $this->_conflits_chirurgiens = 0;

    foreach ($this->_ref_lits as $lit) {
      assert($lit->_ref_affectations !== null);

      // overbooking
      $lit->checkOverBooking();
      $this->_overbooking += $lit->_overbooking;

      // Lits dispo
      if (count($lit->_ref_affectations)) {
        $this->_nb_lits_dispo--;
      }
      
      // Liste des affectations
      foreach ($lit->_ref_affectations as $aff) {
        $listAff[] = $aff;
      }
    }
    $this->_nb_affectations = count($listAff);

    $systeme_presta = CAppUI::conf("dPhospi systeme_prestations");

    foreach ($listAff as $affectation1) {
      if (!$affectation1->sejour_id) {
        continue;
      }
      $sejour1     = $affectation1->_ref_sejour;
      $patient1    = $sejour1->_ref_patient;
      $chirurgien1 = $sejour1->_ref_praticien;

      if ($systeme_presta == "standard") {
        if ((count($this->_ref_lits) == 1) && $sejour1->chambre_seule == 0) {
          $this->_chambre_double++;
        }

        if ((count($this->_ref_lits) > 1) && $sejour1->chambre_seule == 1) {
          $this->_chambre_seule++;
        }
      }

      foreach ($listAff as $affectation2) {
        if (!$affectation2->sejour_id) {
          continue;
        }

        if ($affectation1->_id == $affectation2->_id) {
          continue;
        }
        
        if ($affectation1->lit_id == $affectation2->lit_id) {
          continue;
        }
        
        if (!$affectation1->collide($affectation2)) {
          continue;
        }
        
        $sejour2     = $affectation2->_ref_sejour;
        $patient2    = $sejour2->_ref_patient;
        $chirurgien2 = $sejour2->_ref_praticien;

        // Conflits de pathologies
        if (!$pathos->isCompat($sejour1->pathologie, $sejour2->pathologie, $sejour1->septique, $sejour2->septique)) {
          $this->_conflits_pathologies++;
        }

        // Ecart d'�ge
        $ecart = max($patient1->_annees, $patient2->_annees) - min($patient1->_annees, $patient2->_annees);
        $this->_ecart_age = max($ecart, $this->_ecart_age);

        // Genres m�lang�s
        if (($patient1->sexe != $patient2->sexe) && (($patient1->sexe == "m") || ($patient2->sexe == "m"))) {
          $this->_genres_melanges = true;
        }

        // Conflit de chirurgiens
        if (($chirurgien1->user_id != $chirurgien2->user_id) && ($chirurgien1->function_id == $chirurgien2->function_id)) {
           $this->_conflits_chirurgiens++;
        }
      }
    }
    $this->_conflits_pathologies /= 2;
    $this->_conflits_chirurgiens /= 2;
  }

  /**
   * Construit le tag Chambre en fonction des variables de configuration
   *
   * @param string $group_id Permet de charger l'id externe d'une chambre pour un �tablissement donn� si non null
   *
   * @return string|null
   */
  static function getTagChambre($group_id = null) {
    // Pas de tag Chambre
    if (null == $tag_chambre = CAppUI::conf("dPhospi CChambre tag")) {
      return null;
    }

    // Permettre des id externes en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }
    
    return str_replace('$g', $group_id, $tag_chambre);
  }
}

CChambre::$_prefixe = CAppUI::conf("dPhospi CChambre prefixe");