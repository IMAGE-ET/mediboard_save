<?php

/**
 * Represents an HL7 LRL message segment (Location Relationship Segment) - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentLRL
 * LRL - Represents an HL7 LRL message segment (Transporte les liens entre entités)
 */

class CHL7v2SegmentLRL extends CHL7v2Segment {

  /** @var string */
  public $name   = "LRL";

  /**
   * Build LRL segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $message  = $event->message;

    // LRL-1: Primary Key Value-LRL - LRL (PL) (Requis)
    $data[] = null;

    // LRL-2: Segment Action Code (ID) (Optional)
    $data[] = null;

    // LRL-3: Segment Unique Key - LRL (EI) (Optional)
    $data[] = null;

    // LRL-4: Location Relationship ID - LRL (CWE) (Requis)
    $data[] = null;

    // LRL-5: Organizational Location Relationship Value - LRL (XON) (Conditional)
    $data[] = null;

    // LRL-6: Patient Location Relationship Value - LRL (PL) (Conditional)
    $data[] = null;

    $this->fill($data);
  }
}