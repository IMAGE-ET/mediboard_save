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
 * CCDA_adxp_deliveryAddressLine class
 */
class CCDA_adxp_deliveryAddressLine extends CCDAADXP {

   private $XMLName = "adxp.deliveryAddressLine";

  /**
   * Return the name of the class
   *
   * @return string
   */
  function getNameClass() {
    return $this->XMLName;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["partType"] = "CCDAAddressPartType xml|attribute fixe|DAL";
    return $props;
  }

  /**
   * fonction permettant de tester la validit� de la classe
   *
   * @return array()
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec le parttype correcte
     */

    $tabTest[] = $this->sample("Test avec le parttype fixe correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
