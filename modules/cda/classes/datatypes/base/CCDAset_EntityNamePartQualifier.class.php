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
 * CCDAset_EntityNamePartQualifier Class
 */
class CCDAset_EntityNamePartQualifier extends CCDA_Datatype {

  public $listData = array();

  /**
   * ADD a CCDAEntityNamePartQualifier to the array listData
   *
   * @param \CCDAEntityNamePartQualifier $listData CCDAEntityNamePartQualifier
   *
   * @return void
   */
  function addData($listData) {
    $this->listData[] = $listData;
  }

  /**
   * Reinitialise la variable
   *
   * @return void
   */
  function razlistData () {
    $this->listData = array();
  }

  /**
   * retourne le nom de la classe
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    return $name;
  }

  /**
   * Getter listData
   *
   * @return array
   */
  public function getData() {
    $listdata = "";
    foreach ($this->listData as $_tel) {
      $listdata .= $_tel->getData()." ";
    }
    $listdata = substr($listdata, 0, strlen($listdata)-1);
    return $listdata;
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["listData"] = "CCDAEntityNamePartQualifier xml|data";
    return $props;
  }

  /**
   * fonction permettant de tester la validit� de la classe
   *
   * @return void
   */
  function test() {
    $tabTest = array();

    /**
     * Test avec les valeurs nulls
     */
    $tabTest[] = $this->sample("Test avec les valeurs null", "Document valide");
    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un EntityNamePartQualifier incorrecte
     */
    $entity = new CCDAEntityNamePartQualifier();
    $entity->setData("TESTTEST");
    $this->addData($entity);
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier erron�e", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un EntityNamePartQualifier correcte
     */
    $entity->setData("LS");
    $this->razlistData();
    $this->addData($entity);
    $tabTest[] = $this->sample("Test avec un EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/
    /**
     * Test avec deux EntityNamePartQualifier correcte
     */

    $entity2 = new CCDAEntityNamePartQualifier();
    $entity2->setData("TITLE");
    $this->addData($entity2);
    $tabTest[] = $this->sample("Test avec deux EntityNamePartQualifier correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
