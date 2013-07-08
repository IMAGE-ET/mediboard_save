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
 * Classe classification repr�sentant la variable confidentiality d"ExtrinsicObject
 * Ensemble de m�tadonn�es contenant les informations d�finissant le niveau de confidentialit� d?un
 * document d�pos� dans l?entrep�t.
 */
class CXDSConfidentiality extends CXDSClass {

  /**
   * Construction de la classe
   *
   * @param String $id                 String
   * @param String $classifiedObject   String
   * @param String $nodeRepresentation String
   */
  function __construct($id, $classifiedObject, $nodeRepresentation) {
    parent::__construct($id, $classifiedObject, $nodeRepresentation);
    $this->classificationScheme = "urn:uuid:f4f85eac-e6cb-4883-b524-f2705394840f";
  }
}