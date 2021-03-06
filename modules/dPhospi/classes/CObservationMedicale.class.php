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
 * Permet d'ajouter des observations m�dicales � un s�jour
 */
class CObservationMedicale extends CMbMetaObject implements IIndexableObject {

  // DB Table key
  public $observation_medicale_id;
  
  // DB Fields
  public $sejour_id;
  public $user_id;
  
  public $degre;
  public $date;
  public $text;
  public $type;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var CMediusers */
  public $_ref_user;

  /** @var CAlert */
  public $_ref_alerte;

  static $tag_alerte = "observation";

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'observation_medicale';
    $spec->key   = 'observation_medicale_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]    = "ref class|CMbObject meta|object_class cascade";
    $specs["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineMix show|0";
    $specs["sejour_id"]    = "ref notNull class|CSejour";
    $specs["user_id"]      = "ref notNull class|CMediusers";
    $specs["degre"]        = "enum notNull list|low|high|info default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["text"]         = "text helped|degre";
    $specs["type"]         = "enum list|reevaluation";
    return $specs;
  }

  /**
   * @see parent::canEdit()
   */
  function canEdit() {
    $nb_hours = CAppUI::conf("soins Other max_time_modif_suivi_soins", CGroups::loadCurrent()->_guid);
    $datetime_max = CMbDT::dateTime("+ $nb_hours HOURS", $this->date);
    return $this->_canEdit = (CMbDT::dateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);
  }

  /**
   * Charge le s�jour
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * Charge l'utilisateur
   *
   * @return CMediusers
   */
  function loadRefUser() {
    /** @var CMediusers $user */
    $user = $this->loadFwdRef("user_id", true);
    $user->loadRefFunction();

    return $this->_ref_user = $user;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
    $this->_view = "Observation du Dr ".$this->_ref_user->_view;
  }

  /**
   * Chargement de l'alerte
   *
   * @return void
   */
  function loadRefAlerte() {
    $this->_ref_alerte = new CAlert();
    $this->_ref_alerte->setObject($this);
    $this->_ref_alerte->tag = self::$tag_alerte;
    $this->_ref_alerte->level = "medium";
    $this->_ref_alerte->loadMatchingObject();
  }

  function store() {
    $msg_alerte = "";

    $manual_alerts = CAppUI::conf("soins Observations manual_alerts", CGroups::loadCurrent());
    if ($manual_alerts) {
      $this->completeField("degre", "text");
      if (!$this->_id || $this->fieldModified("text") || $this->fieldModified("degre")) {
        $msg_alerte = CAppUI::tr("CObservationMedicale-degre") . ": " . $this->getFormattedValue("degre") . "\n" . $this->text;
      }
    }

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($manual_alerts) {
      $this->loadRefAlerte();

      if ($msg_alerte) {
        $this->_ref_alerte->handled  = 0;
        $this->_ref_alerte->comments = $msg_alerte;
        if ($msg = $this->_ref_alerte->store()) {
          return $msg;
        }
      }
    }

    return null;
  }

  static function massLoadRefAlerte(&$observations = array(), $handled = true) {
    $alerte = new CAlert();
    $where = array(
      "object_class" => "= 'CObservationMedicale'",
      "object_id"    => CSQLDataSource::prepareIn(CMbArray::pluck($observations, "_id")),
      "level"        => "= 'medium'",
      "tag"          => "= '" . self::$tag_alerte . "'"
    );

    if (!$handled) {
      $where["handled"] = "= '0'";
    }

    $alertes = $alerte->loadList($where);

    CStoredObject::massLoadFwdRef($alertes, "handled_user_id");

    foreach ($alertes as $_alerte) {
      $observations[$_alerte->object_id]->_ref_alerte = $_alerte;
    }

    foreach ($observations as $_observation) {
      if (!$_observation->_ref_alerte) {
        $_observation->_ref_alerte = new CAlert();
      }
      $_observation->_ref_alerte->loadRefHandledUser();
    }
  }

  /**
   * @see parent::check()
   */
  /*
  function check(){
    if (!$this->_id && $this->degre == "info" && $this->text == "Visite effectu�e") {
      if ($this->countNotifSiblings()) {
        return "Notification deja effectu�e";
      }
    }
    return parent::check();
  }
  */

  /**
   * Compte les visites effectu�es
   *
   * @return int
   */
  function countNotifSiblings() {
    $date = CMbDT::date($this->date);
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->sejour_id'";
    $where["user_id"]  = " = '$this->user_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    $where["text"] = " = 'Visite effectu�e'";
    return $observation->countList($where);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($perm) {
    if (!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return CPatient
   */
  function getIndexablePatient () {
    $sejour = $this->loadRefSejour();
    $sejour->loadRelPatient();
    return $sejour->_ref_patient;
  }
  /**
   * Loads the related fields for indexing datum (patient_id et date)
   *
   * @return array
   */
  function getIndexableData () {
    /**@var $user CMediusers**/
    $prat = $this->getIndexablePraticien();
    $array["id"]          = $this->_id;
    $array["author_id"]   = $this->user_id;
    $array["prat_id"]     = $prat->_id;
    $array["title"]       = $this->type;
    $array["body"]        = $this->text;
    $array["date"]        = str_replace("-", "/", $this->date);
    $array["function_id"] = $prat->function_id;
    $array["group_id"]    = $prat->loadRefFunction()->group_id;
    $array["patient_id"]  = $this->getIndexablePatient()->_id;
    $array["object_ref_id"]  = $this->loadRefSejour()->_id;
    $array["object_ref_class"]  = $this->loadRefSejour()->_class;

    return $array;
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody ($content) {
    return $content;
  }
  /**
   * Get the praticien_id of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien () {
    return $this->loadRefSejour()->loadRefPraticien();
  }
}

