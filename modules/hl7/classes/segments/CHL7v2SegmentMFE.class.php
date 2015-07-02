<?php

/**
 * Represents an HL7 MFE message segment (Master File Identification) - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentMFE
 * MFE - Represents an HL7 MFE message segment (Identifie chaque entité de la structure)
 */
class CHL7v2SegmentMFE extends CHL7v2Segment {

  /** @var string */
  public $name = "MFE";

  public $entity;
  /**
   * Build MFE segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $entity = $this->entity;
    $primary_key = array_search(get_class($entity), CHL7v2EventMFN::$entities);
    $primary_key = $primary_key.$entity->_id;

    $data = array();

    // MFE-1: Record-Level Event Code - MFE (ID) (Requis)
    $data[] = "MAD";

    // MFE-2: MFN Control ID - MFE (ST) (Conditional)
    $data[] = $event->_exchange_hl7v2->_id;

    // MFE-3: Effective Date/Time - MFE (TS) (Optional)
    $data[] = null;

    // MFE-4: Primary Key Value - MFE - MFE (PL) (Requis)
    $data[] = $primary_key;

    // MFE-5: Primary Key Value Type - MFE (ID) (Requis)
    $data[] = "PL";

    $this->fill($data);
  }
}