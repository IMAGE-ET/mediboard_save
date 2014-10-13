<?php

/**
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Used in header of a view to check for access to a medical object needed to be checked
 */
class CAccessMedicalData extends CMbObject {

  /**
   * check access for a sejour
   *
   * @param string|CMbObject $sejour
   */
  static function checkForSejour($sejour) {
    if (is_string($sejour)) {
      $sejour = CMbObject::loadFromGuid($sejour);
    }
    CBrisDeGlace::checkForSejour($sejour);
    CLogAccessMedicalData::logForSejour($sejour);
  }

  static function checkForObject($object_class, $object_id) {
    //@TODO with meta
  }
}
