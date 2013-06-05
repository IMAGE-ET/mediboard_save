<?php

/**
 * Transfert de donn�es de regl�ment - H'2.1
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CREG 
 * Transfert de donn�es de regl�ment
 */
class CREG {
  static $versions = array (
    "2.1"
  );
  
  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrim21REGL",
    // C - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
    "C" => "CHPrim21REGC",
    // R - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
    "R" => "CHPrim21REGR",
  );
  
  function __construct() {
    $this->type = "REG";
  }
  
  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
}

