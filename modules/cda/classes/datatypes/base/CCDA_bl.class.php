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
 * The Boolean type stands for the values of two-valued logic.
 * A Boolean value can be either true or
 * false, or, as any other value may be NULL.
 */
class CCDA_bl extends CCDAANY {


  public $data;
  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    parent::getProps();
    $props["data"] = "booleen xml|data pattern|true|false";
    return $props;
  }

  /**
   * Modifie la variable data
   *
   * @param String $data Data
   *
   * @return void
   */
  function setData($data) {
    $this->data = $data;
  }

  function getData() {
    return $this->data;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return void
   */
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
    $this->setData("true");
    $tabTest[] = $this->sample("Test avec une valeur bonne", "Document valide");
    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec une valeur incorrecte
     */
    $this->setData("TESTTEST");
    $tabTest[] = $this->sample("Test avec une valeur incorrecte", "Document invalide");
    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
