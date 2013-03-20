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
 * A ST that optionally may have a code attached.
 * The text must always be present if a code is present. The
 * code is often a local code.
 */
class CCDASC extends CCDAST {

  /**
   * The plain code symbol defined by the code system.
   * For example, "784.0" is the code symbol of the ICD-9
   * code "784.0" for headache.
   * @var CCDA_base_cs
   */
  public $code;

  /**
   * Specifies the code system that defines the code.
   * @var CCDA_base_uid
   */
  public $codeSystem;

  /**
   * A common name of the coding system.
   * @var CCDA_base_st
   */
  public $codeSystemName;

  /**
   * If applicable, a version descriptor defined
   * specifically for the given code system.
   * @var CCDA_base_st
   */
  public $codeSystemVersion;

  /**
   * A name or title for the code, under which the sending
   * system shows the code value to its users.
   * @var CCDA_base_st
   */
  public $displayName;

  /**
   * Getter code
   *
   * @return CCDA_base_cs CCDA_base_cs code
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Getter CodeSystem
   *
   * @return \CCDA_base_uid
   */
  public function getCodeSystem() {
    return $this->codeSystem;
  }


  /**
   * Getter CodeSystemName
   *
   * @return \CCDA_base_st
   */
  public function getCodeSystemName() {
    return $this->codeSystemName;
  }

  /**
   * Getter CodeSystemVersion
   *
   * @return \CCDA_base_st
   */
  public function getCodeSystemVersion() {
    return $this->codeSystemVersion;
  }

  /**
   * Getter DisplayName
   *
   * @return \CCDA_base_st
   */
  public function getDisplayName() {
    return $this->displayName;
  }

  /**
   * Setter Code
   *
   * @param \CCDA_base_cs $code CCDA_base_cs
   *
   * @return void
   */
  public function setCode($code) {
    $this->code = $code;
  }

  /**
   * Setter CodeSystem
   *
   * @param \CCDA_base_uid $codeSystem CCDA_base_uid
   *
   * @return void
   */
  public function setCodeSystem($codeSystem) {
    $this->codeSystem = $codeSystem;
  }

  /**
   * Setter codeSystemName
   *
   * @param \CCDA_base_st $codeSystemName CCDA_base_st
   *
   * @return void
   */
  public function setCodeSystemName($codeSystemName) {
    $this->codeSystemName = $codeSystemName;
  }

  /**
   * Setter codeSystemVersion
   *
   * @param \CCDA_base_st $codeSystemVersion CCDA_base_st
   *
   * @return void
   */
  public function setCodeSystemVersion($codeSystemVersion) {
    $this->codeSystemVersion = $codeSystemVersion;
  }

  /**
   * Setter displayName
   *
   * @param \CCDA_base_st $displayName CCDA_base_st
   *
   * @return void
   */
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["code"] = "CCDA_base_cs xml|attribute";
    $props["codeSystem"] = "CCDA_base_uid xml|attribute";
    $props["codeSystemName"] = "CCDA_base_st xml|attribute";
    $props["codeSystemVersion"] = "CCDA_base_st xml|attribute";
    $props["displayName"] = "CCDA_base_st xml|attribute";
    return $props;
  }


  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec code incorrecte
     */
    $codeTest = new CCDA_base_cs();
    $codeTest->setData(" ");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec code correct
     */
    $codeTest->setData("TEST");
    $this->setCode($codeTest);

    $tabTest[] = $this->sample("Test avec un code correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem incorrecte
     */

    $codeSystemTest = new CCDA_base_uid();
    $codeSystemTest->setData("*");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec un codeSystem incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystem correct
     */

    $codeSystemTest->setData("HL7");
    $this->setCodeSystem($codeSystemTest);

    $tabTest[] = $this->sample("Test avec un codeSystem correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName incorrecte
     */
    $codeSystemNameTest = new CCDA_base_st();
    $codeSystemNameTest->setData("");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec un codeSystemName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemName correct
     */


    $codeSystemNameTest->setData("test");
    $this->setCodeSystemName($codeSystemNameTest);

    $tabTest[] = $this->sample("Test avec un codeSystemName correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion incorrecte
     */
    $codeSystemVersionTest = new CCDA_base_st();
    $codeSystemVersionTest->setData("");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec un codeSystemVersion incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec codeSystemVersion correct
     */

    $codeSystemVersionTest->setData("test");
    $this->setCodeSystemVersion($codeSystemVersionTest);

    $tabTest[] = $this->sample("Test avec un codeSystemVersion correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName incorrecte
     */

    $displayNameTest = new CCDA_base_st();
    $displayNameTest->setData("");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec un displayName incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec displayName correct
     */

    $displayNameTest->setData("test");
    $this->setDisplayName($displayNameTest);

    $tabTest[] = $this->sample("Test avec un displayName correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
