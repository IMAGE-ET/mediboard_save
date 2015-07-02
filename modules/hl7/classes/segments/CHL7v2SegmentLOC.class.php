<?php

/**
 * Represents an HL7 LOC message segment (Location Identification Segment) - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentLOC
 * LOC - Represents an HL7 LOC message segment ()
 */
class CHL7v2SegmentLOC extends CHL7v2Segment {

  /** @var string */
  public $name = "LOC";

  /** @var CEntity */
  public $entity;

  /**
   * Build LOC segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $entity = $this->entity;
    $localisation_type = array_search(get_class($entity), CHL7v2EventMFN::$entities);
    $primary_key = $localisation_type.$entity->_id;

    // LOC-1: Primary Key Value-LOC - LOC (PL) (Requis)
    $data[] = $primary_key;

    // LOC-2: Location Description - LOC (ST) (Optional)
    $data[] = null;

    // LOC-3: Location Type - LOC (IS) (Requis)
    $data[] = $localisation_type;

    // LOC-4: Organization Name - LOC (XON) (Optional)
    $data[] = $entity->_name;

    // LOC-5: Location Address - LOC (XAD) (Optional)
    $data[] = null;

    // LOC-6: Location Phone - LOC (XTN) (Optional)
    $data[] = null;

    // LOC-7: License Number - LOC (CE) (Optional)
    $data[] = null;

    // LOC-8: Location Equipment - LOC (IS) (Optional)
    $data[] = null;

    // LOC-9: Location Service Code - LOC (IS) (Optional)
    $data[] = null;

    $this->fill($data);
  }
}