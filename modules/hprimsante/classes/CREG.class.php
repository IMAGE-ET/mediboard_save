<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CREG
 * Transfert de donn�es de regl�ment
 */
class CREG extends CHPrimSante {

  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrimSanteREGL",
    // C - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
    "C" => "CHPrimSanteREGC",
    // R - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
    "R" => "CHPrimSanteREGR",
  );

  /**
   * construct
   */
  function __construct() {
    $this->type = "REG";

    parent::__construct();
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object|null An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code = $exchange->code;
    //@todo voir pour la gestion
    $classname = "CHPrimSanteREG$code";

    return new $classname;
  }
}

