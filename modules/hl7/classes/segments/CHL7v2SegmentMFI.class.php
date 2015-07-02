<?php

/**
 * Represents an HL7 MFI message segment (Master File Identification) - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentMFI
 * MFI - Represents an HL7 MFI message segment (Identifie l'ensemble du catalogue)
 */

class CHL7v2SegmentMFI extends CHL7v2Segment {
  /** @var string */
  public $name   = "MFI";

  /**
   * Build MFI segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    // MFI-1: Master File Identifier - MFI (CE) (Requis)
    $data[] = "LOC";

    // MFI-2: Master File Application Identifier - MFI (HD) (Requis)
    $data[] = "Mediboard_LOC_FRA";

    // MFI-3: File-Level Event Code - MFI (ID) (Requis)
    $data[] = "REP";

    // MFI-4: Enterd Date/Time - MFI (TS) (Optional)
    $data[] = null;

    // MFI-5: Effective Date/Time - MFI (TS) (Requis)
    $data[] = CMbDT::dateTime();

    // MFI-6: Response Level Code - MFI (ID) (Requis)
    $data[] = "AL";

    $this->fill($data);
  }
}