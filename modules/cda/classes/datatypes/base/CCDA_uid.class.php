<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * A unique identifier string is a character string which
 * identifies an object in a globally unique and timeless
 * manner. The allowable formats and values and procedures
 * of this data type are strictly controlled by HL7. At this
 * time, user-assigned identifiers may be certain character
 * representations of ISO Object Identifiers (OID) and DCE
 * Universally Unique Identifiers (UUID). HL7 also reserves
 * the right to assign other forms of UIDs, such as mnemonic
 * identifiers for code systems.
 */
class CCDA_uid extends CCDA_Datatype {

  public $data;
  public $union = array("oid", "uuid", "ruid");

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  function getPropsUnion() {
    $pattern = "";
    foreach ($this->union as $_union) {
      $_union = "CCDA_".$_union;
      $class = new $_union;
      $spec = $class->getSpecs();
      $pattern .= $spec["data"]["pattern"];
    }
    return $pattern;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();

    $props["data"] = "str xml|data pattern|".$this->getPropsUnion();
    return $props;
  }

  function test() {
    $tabTest = array();
    /**
     * Test avec une valeur null
     */
    $tabTest[] = $this->sample("Test avec une valeur null", "Document invalide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur bonne
     */
    $this->setData("HL7");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData("4TESTTEST");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
