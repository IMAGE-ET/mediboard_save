<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Observation value unit, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueCodingSystem extends CMbObject {
  public $code;
  public $label;
  public $desc;
  public $coding_system;

  static $_list = array(
    "MB"                  => "Standard Mediboard",
    "MDIL"                => "MDIL",
  );

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]          = "str notNull seekable";
    $props["label"]         = "str notNull seekable";
    $props["desc"]          = "str seekable";
    $props["coding_system"] = "str notNull";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    if ($this->desc) {
      $this->_view = "$this->desc [$this->label]";
    }
    else {
      $this->_view = $this->label;
    }
  }

  /**
   * Load a matching coding system, creates one if unknown
   *
   * @param string $code          The code
   * @param string $coding_system The coding system
   * @param string $label         Label
   * @param string $desc          Optional description
   *
   * @return int The cding system ID
   */
  function loadMatch($code, $coding_system, $label, $desc = null) {
    $ds = $this->_spec->ds;
    
    $where = array(
      "code"          => $ds->prepare("=%", $code),
      "coding_system" => $ds->prepare("=%", $coding_system),
    );
    
    if (!$this->loadObject($where)) {
      $this->code          = $code;
      $this->coding_system = $coding_system;
      $this->label         = $label;
      $this->desc          = $desc;
      if ($this instanceof CObservationValueType) {
        $this->datatype = "NM";
      }
      $this->store();
    }
    
    return $this->_id;
  }
}

if (CModule::getActive("patientMonitoring")) {
  CObservationValueCodingSystem::$_list["Kheops-Concentrator"] = "Concentrateur Kheops";
}