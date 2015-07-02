<?php

/**
 * Represents an HL7 LCH message segment (Location Characteristic Segment) - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentLCH
 * LCH - Represents an HL7 LCH message segment (Transporte des attributs supplémentaires non définis dans le segment LOC)
 */
class CHL7v2SegmentLCH extends CHL7v2Segment {

  /** @var string */
  public $name = "LCH";

  public $entity;
  public $code;

  public static $LCHKey = array('code', 'description', 'user_id', 'user_last_name', 'user_first_name', 'user_phone',
    'opening_date', 'closing_date', 'activation_date', 'inactivation_date'
  );

  /**
   * Build LCH segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $entity      = $this->entity;
    $code        = $this->code;

    $primary_key = array_search(get_class($entity), CHL7v2EventMFN::$entities);
    $primary_key = $primary_key . $entity->_id;



    mbTrace($entity);

    // LCH-1: Primary Key Value -LCH - LCH (PL) (Requis)
    $data[] = $primary_key;

    // LCH-2: Segment Action Code - LCH (ID) (Optional)
    $data[] = null;

    // LCH-3: Segment Unique Key - LCH (EI) (Optional)
    $data[] = null;

    // LCH-4: Location Characteristic ID - LCH (CWE) (Requis)
    $HL7_value = CHL7v2TableEntry::mapTo("7878", $code);
    $data[] = array(
      array(
        $HL7_value,
        CHL7v2TableEntry::getDescription("7878", $HL7_value),
        "L"
      )
    );

    // if si dans la chaine tu as RSPNSBL
    // alors :
    // $user = $entity->loadRefUser();
    // $user->$code
    // else
    // $entity->$code

    // LCH-5: Location Characteristic Value - LCH (CWE) (Requis)
    $data[] = null;

    $this->fill($data);
  }
}