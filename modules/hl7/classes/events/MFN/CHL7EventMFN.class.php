<?php

/**
 * Master File Notification HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventMFN
 * Master File Notification
 */
interface CHL7EventMFN {
  /**
   * Construct
   *
   * @return CHL7EventMFN
   */
  function __construct();

  /**
   * Build MFN message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}