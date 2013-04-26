<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * POCD_MT000040_Component3 Class
 */
class CCDAPOCD_MT000040_Component3 extends CCDARIMActRelationship {

  /**
   * @var CCDAPOCD_MT000040_Section
   */
  public $section;

  /**
   * Setter section
   *
   * @param CCDAPOCD_MT000040_Section $inst CCDAPOCD_MT000040_Section
   *
   * @return void
   */
  function setSection(CCDAPOCD_MT000040_Section $inst) {
    $this->section = $inst;
  }

  /**
   * Getter section
   *
   * @return CCDAPOCD_MT000040_Section
   */
  function getSection() {
    return $this->section;
  }

  /**
   * Assigne typeCode � COMP
   *
   * @return void
   */
  function setTypeCode() {
    $actRela = new CCDAActRelationshipHasComponent();
    $actRela->setData("COMP");
    $this->typeCode = $actRela;
  }

  /**
   * Getter typeCode
   *
   * @return CCDAActRelationshipHasComponent
   */
  function getTypeCode() {
    return $this->typeCode;
  }

  /**
   * Assigne contextConductionInd � true
   *
   * @return void
   */
  function setContextConductionInd() {
    $bl = new CCDA_base_bl();
    $bl->setData("true");
    $this->contextConductionInd = $bl;
  }

  /**
   * Getter contextConductionInd
   *
   * @return CCDA_base_bl
   */
  function getContextConductionInd() {
    return $this->contextConductionInd;
  }


  /**
   * Retourne les propri�t�s
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["typeId"]               = "CCDAPOCD_MT000040_InfrastructureRoot_typeId xml|element max|1";
    $props["section"]              = "CCDAPOCD_MT000040_Section xml|element required";
    $props["typeCode"]             = "CCDAActRelationshipHasComponent xml|attribute fixed|COMP";
    $props["contextConductionInd"] = "CCDA_base_bl xml|attribute fixed|true";
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
     * Test avec un section correcte
     */

    $sec = new CCDAPOCD_MT000040_Section();
    $sec->setClassCode();
    $this->setSection($sec);
    $tabTest[] = $this->sample("Test avec un section correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un typeCode correcte
     */

    $this->setTypeCode();
    $tabTest[] = $this->sample("Test avec un typeCode correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un contextConductionInd correcte
     */

    $this->setContextConductionInd();
    $tabTest[] = $this->sample("Test avec un contextConductionInd correct", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}