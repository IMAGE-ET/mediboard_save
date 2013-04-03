<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Permet d'ajouter des transmissions m�dicales � un s�jour
 */

class CTransmissionMedicale extends CMbMetaObject {
  // DB Table key
  var $transmission_medicale_id = null;	

  // DB Fields
  var $sejour_id   = null;
  var $user_id     = null;
  var $degre       = null;
  var $date        = null;
  var $date_max    = null;
  var $text        = null;
  var $type        = null;
  var $libelle_ATC = null;
  var $locked      = null;

  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  var $_ref_cible  = null;

  // Form fields
  var $_cible       = null;
  var $_text_data   = null;
  var $_text_action = null;
  var $_text_result = null;
  var $_log_lock    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    $spec->measureable = true;
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["object_id"]    = "ref class|CMbObject meta|object_class";
    $props["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration|CPrescriptionLineMix show|0";
    $props["sejour_id"]    = "ref notNull class|CSejour";
    $props["user_id"]      = "ref notNull class|CMediusers";
    $props["degre"]        = "enum notNull list|low|high default|low";
    $props["date"]         = "dateTime notNull";
    $props["date_max"]     = "dateTime";
    $props["text"]         = "text helped|type|object_id";
    $props["type"]         = "enum list|data|action|result";
    $props["libelle_ATC"]  = "text";
    $props["locked"]       = "bool default|0";
    $props["_text_data"]   = "text helped|type|object_id";
    $props["_text_action"] = "text helped|type|object_id";
    $props["_text_result"] = "text helped|type|object_id";
    return $props;
  }

  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
    return $this->_ref_user;
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
    $this->_view = "Transmission de ".$this->_ref_user->_view;
  }

  function canEdit(){
    $nb_hours = CAppUI::conf("soins max_time_modif_suivi_soins");
    $datetime_max = CMbDT::dateTime("+ $nb_hours HOURS", $this->date);
    return $this->_canEdit = (CMbDT::dateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);
  }

  function calculCibles(&$cibles = array()){
    $state = $this->locked ? "closed" : "opened";
    if ($this->object_id && $this->object_class) {
      // Ligne de medicament, cible => classe ATC
      if ($this->object_class == "CPrescriptionLineMedicament") {
        $libelle_ATC = $this->_ref_object->_ref_produit->_ref_ATC_2_libelle;
        $this->_cible = $libelle_ATC;
        if (
            (!isset($cibles["opened"]["ATC"]) && !isset($cibles["closed"]["ATC"])) ||
            (!@in_array($libelle_ATC, $cibles["opened"]["ATC"]) && !@in_array($libelle_ATC, $cibles["closed"]["ATC"]))
        ) {
          $cibles[$state]["ATC"][] = $libelle_ATC;
        }
      }

      // Ligne d'element, cible => categorie
      if ($this->object_class == "CPrescriptionLineElement") {
        $category = $this->_ref_object->_ref_element_prescription->_ref_category_prescription;
        $this->_cible = $category->_view;
        if (!isset($cibles["opened"]["CCategoryPrescription"][$category->_id]) &&
            !isset($cibles["closed"]["CCategoryPrescription"][$category->_id])
        ) {
          $cibles[$state]["CCategoryPrescription"][$category->_id] = $category->_view;
        }
      }

      // Administration => ATC ou categorie
      if ($this->object_class == "CAdministration") {
        if ($this->_ref_object->object_class == "CPrescriptionLineMedicament") {
          $this->_ref_object->loadTargetObject();
          $libelle_ATC = $this->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle;
          $this->_cible = $libelle_ATC;

          if (!isset($cibles["opened"]["ATC"][$libelle_ATC]) &&
              !isset($cibles["closed"]["ATC"][$libelle_ATC])
          ) {
            $cibles[$state]["ATC"][$libelle_ATC] = $libelle_ATC;
          }
        }
        if ($this->_ref_object->object_class == "CPrescriptionLineElement") {
          $this->_ref_object->loadTargetObject();
          $category = $this->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription;
          $this->_cible = $category->_view;
          if (!isset($cibles["opened"]["CCategoryPrescription"][$category->_id]) &&
              !isset($cibles["closed"]["CCategoryPrescription"][$category->_id])
          ) {
            $cibles[$state]["CCategoryPrescription"][$category->_id] = $category->_view;
          }
        }
      }

      if ($this->object_class == "CCategoryPrescription") {
        $this->_cible = $this->_ref_object->_view;
        if (!isset($cibles["opened"][$this->object_class][$this->object_id]) &&
            !isset($cibles["closed"][$this->object_class][$this->object_id])
        ) {
          $cibles[$state][$this->object_class][$this->object_id] = $this->_ref_object->_view;
        }
      }

      if ($this->object_class == "CPrescriptionLineMix") {
        $this->_cible = "prescription_line_mix";
        if (!isset($cibles["opened"]["perf"][0]) && !isset($cibles["closed"]["perf"][0])) {
          $cibles[$state]["perf"][0] = "prescription_line_mix";
        }
      }
    }

    if ($this->libelle_ATC) {
      $this->_cible = $this->libelle_ATC;
      if ((!isset($cibles["opened"]["ATC"]) && !isset($cibles["closed"]["ATC"])) ||
          (!@in_array($this->libelle_ATC, $cibles["opened"]["ATC"]) && @!in_array($this->libelle_ATC, $cibles["closed"]["ATC"]))
      ) {
        $cibles[$state]["ATC"][] = $this->libelle_ATC;
      }
    }
  }

  function store() {
    // Si une cible est d�finie, on Unlock la pr�c�dente transmission sur la m�me cible
    // (classe ATC ou cat�gorie de prescription)
    $this->completeField("sejour_id", "libelle_ATC", "object_id", "object_class");

    if ($this->libelle_ATC || ($this->object_id && $this->object_class)) {
      $trans = new CTransmissionMedicale();
      if ($this->libelle_ATC) {
        $trans->libelle_ATC = $this->libelle_ATC;
      }
      else if ($this->object_id && $this->object_class) {
        $trans->object_class = $this->object_class;
        $trans->object_id    = $this->object_id;
      }
      $trans->sejour_id    = $this->sejour_id;
      $trans->locked       = 1;
      $trans->loadMatchingObject("transmission_medicale_id DESC");

      if ($trans->_id && $trans->_id != $this->_id) {
        $trans->locked = 0;
        $trans->store();
      }
    }
    return parent::store();
  }

  function getPerm($perm) {
    if (!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
}
