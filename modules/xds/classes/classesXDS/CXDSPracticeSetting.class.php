<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe classification repr�sentant la variable Practicesetting
 * Ensemble de m�tadonn�es repr�sentant le cadre d?exercice de l?acte qui a engendr� la cr�ation du
 * document.
 */
class CXDSPracticeSetting extends CXDSClass {

  /**
   * @see parent::__construct()
   */
  function __construct($id, $classifiedObject, $nodeRepresentation) {
    parent::__construct($id, $classifiedObject, $nodeRepresentation);
    $this->classificationScheme = "urn:uuid:cccf5598-8b07-4b77-a05e-ae952c785ead";
  }
}