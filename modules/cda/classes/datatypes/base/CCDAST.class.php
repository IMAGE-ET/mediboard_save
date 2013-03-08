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
 * The character string data type stands for text data,
 * primarily intended for machine processing (e.g.,
 * sorting, querying, indexing, etc.) Used for names,
 * symbols, and formal expressions.
 */
class CCDAST extends CCDAED {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    $props["reference"] = "CCDATEL xml|element prohibited";
    $props["thumbnail"] = "CCDAthumbnail xml|element prohibited";
    $props["mediaType"] = "CCDACS xml|attribute fixed|text/plain";
    $props["compression"] = "CCDACompressionAlgorithm xml|attribute prohibited";
    $props["integrityCheck"] = "CCDbin xml|attribute prohibited";
    $props["integrityCheckAlgorithm"] = "CCDAintegrityCheckAlgorithm xml|attribute prohibited";
    return $props;
  }

  /**
   * fonction permettant de tester la validit� de la classe
   *
   * @return void
   */
  function test() {

    $tabTest = parent::test();

    /**
     * Test avec une valeur correcte mais refuser dans ce contexte
     */

    $binaryDataEncoding = new CCDABinaryDataEncoding();
    $binaryDataEncoding->setData("B64");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation correcte mais pas dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec une valeur correcte dans ce contexte
     */

    $binaryDataEncoding->setData("TXT");
    $this->setRepresentation($binaryDataEncoding);

    $tabTest[] = $this->sample("Test avec une representation correcte dans ce contexte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un mediaType erron� dans ce contexte
     *
     */

    $codeTest = new CCDA_cs();
    $codeTest->setData(" ");
    $this->setMediaType($codeTest);

    $tabTest[] = $this->sample("Test avec un mediaType correcte mais pas dans ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un mediaType correcte
     *
     */

    $codeTest->setData("text/plain");
    $this->setMediaType($codeTest);

    $tabTest[] = $this->sample("Test avec un mediaType correcte dans ce contexte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    if (get_class($this) !== "CCDAST") {
      return $tabTest;
    }

    /**
     * Test avec un compression incorrecte
     *
     */

    $compression = new CCDACompressionAlgorithm();
    $compression->setData(" ");
    $this->setCompression($compression);

    $tabTest[] = $this->sample("Test avec un compression incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un compression correcte
     *
     */

    $compression->setData("GZ");
    $this->setCompression($compression);

    $tabTest[] = $this->sample("Test avec un compression correcte mais pas de ce contexte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }

}