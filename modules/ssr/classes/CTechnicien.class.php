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
 * Technicien de SSR, association entre un plateau technique et un utilisateur
 */
class CTechnicien extends CMbObject {
  // DB Table key
  public $technicien_id;

  // References fields
  public $plateau_id;
  public $kine_id;

  // DB Fields
  public $actif;

  // Form fields
  public $_transfer_id;
  public $_count_sejours_date;

  // References
  /** @var CMediusers */
  public $_ref_kine;
  /** @var CPlateauTechnique */
  public $_ref_plateau;

  // Distant references
  /** @var CPlageConge */
  public $_ref_conge_date;
  /** @var CSejour[] */
  public $_ref_sejours_date;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'technicien';
    $spec->key   = 'technicien_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $props["kine_id"]    = "ref notNull class|CMediusers";
    $props["actif"]      = "bool notNull default|1";

    $props["_transfer_id"]        = "ref class|CTechnicien";
    $props["_count_sejours_date"] = "num";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bilan_ssr"] = "CBilanSSR technicien_id";
    return $backProps;
  }

  /**
   * @see parent::store()
   */
  function store() {
    // Transfert de s�jours vers un autre technicien
    if ($this->_transfer_id) {
      foreach ($this->loadRefsSejours(CMbDT::date()) as $_sejour) {
        $bilan = $_sejour->loadRefBilanSSR();
        $bilan->technicien_id = $this->_transfer_id;
        if ($msg = $bilan->store()) {
          return $msg;
        }
      }
    }

    return parent::store();
  }

  /**
   * Update view under certain changes
   *
   * @return void
   */
  function updateView() {
    $parts = array();
    if ($this->_ref_kine && $this->_ref_kine->_id) {
      $parts[] = $this->_ref_kine->_view;
    }

    if ($this->_ref_plateau && $this->_ref_plateau->_id) {
      $parts[] = $this->_ref_plateau->_view;
    }

    $this->_view = implode(" &ndash; ", $parts);
  }

  /**
   * Charge le plateau technique
   *
   * @return CPlateauTechnique
   */
  function loadRefPlateau() {
    $plateau = $this->loadFwdRef("plateau_id", true);
    $this->updateView();
    return $this->_ref_plateau = $plateau;
  }


  /**
   * Charge le kin� technicien
   *
   * @return CMediusers
   */
  function loadRefKine() {
    /** @var CMediusers $kine */
    $kine = $this->loadFwdRef("kine_id", true);
    $kine->loadRefFunction();
    $this->updateView();
    return $this->_ref_kine = $kine;
  }

  /**
   * Charge la plage de cong�s pour un technicien � une date donn�e
   *
   * @param date $date Date de r�f�rence
   *
   * @return CPlageConge
   */
  function loadRefCongeDate($date) {
    $this->_ref_conge_date = new CPlageConge;
    $this->_ref_conge_date->loadFor($this->kine_id, $date);
    return $this->_ref_conge_date;
  }

  /**
   * Compte les s�jours pour le technicien � une date de r�f�rence
   *
   * @param date $date Date de r�f�rence
   *
   * @return int
   */
  function countSejoursDate($date) {
    $group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_count_sejours_date = CSejour::countForDate($date, $where, $leftjoin);
  }

  /**
   * Charge les s�jours pour ce technicien en tant que r�f�rent � une date donn�e
   *
   * @param date $date Date de reference
   *
   * @return CSejour[]
   */
  function loadRefsSejours($date) {
    $group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_ref_sejours_date = CSejour::loadListForDate($date, $where, null, null, null, $leftjoin);
  }
}
