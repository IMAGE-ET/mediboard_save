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
 * Classe dont h�riteront toutes les classes
 */
class CCDA_Datatype extends CCDAClasseBase{

  public $data;

  /**
   * Setter Data
   *
   * @param String $data String
   *
   * @return void
   */
  function setData($data) {
    $this->data = $data;
  }

  /**
   * Getter Data
   *
   * @return mixed
   */
  function getData() {
    return $this->data;
  }

  /**
   * Initialise les props
   *
   * @return array
   */
  function getProps() {
    $props = array();

    return $props;
  }

  /**
   * Retourne le nom du type utilis� dans le XSD
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    if (strpos($name, "_") !== false) {
      $name = substr($name, 1);
    }
    return $name;
  }

  /**
   * Retourne le r�sultat de la validation par le xsd de la classe appell�e
   *
   * @return bool
   */
  function validate() {

    $domDataType = $this->toXML(null, null);
    return @$domDataType->schemaValidate("modules/cda/resources/TestClasses.xsd");
  }

}