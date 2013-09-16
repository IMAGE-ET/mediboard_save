<?php

/**
 * Transfert de r�sultats d'analyses - H'2.1
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CORU
 * Transfert de r�sultats d'analyses,
 */
class CORU {
  static $versions = array (
    "2.1"
  );
  
  static $evenements = array(
    // L - Liaisons entre laboratoires
    "L" => "CHPrim21ORUL",
    // C - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
    "C" => "CHPrim21ORUC",
    // R - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
    "R" => "CHPrim21ORUR",
  );
  
  function __construct() {
    $this->type = "ORU";
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

