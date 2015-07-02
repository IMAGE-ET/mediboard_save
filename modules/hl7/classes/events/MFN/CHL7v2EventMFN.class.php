<?php

/***
 * Transporter des structures spécifiques dans des messages HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventMFN
 * Transporter des structures spécifiques dans des messages HL7
 */
class CHL7v2EventMFN extends CHL7v2Event {
  /** @var string */
  public $event_type = "MFN";

  public static $entities = array(
    'M'           => 'CLegalEntity',
    'ETBL_GRPQ'   => 'CGroups',
    'D'           => 'CService',
    'H'           => 'CUniteFonctionnelle',
    'N'           => 'CUniteFonctionnelle',
    'B'           => 'CLit',
    'STRCTR_INTR' => 'CInternalStructure',
    'R'           => 'CChambre',
  );


  /**
   * Construct
   *
   * @param string $i18n i18n
   */
  function __construct($i18n = null) {
    $this->profil    = "MFN";
    $this->msg_codes = array(
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->struct_code}"
      )
    );
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);

    // Message Header
    $this->addMSH();

    // Master File Identification
    $this->addMFI();
    foreach ($object->_objects as $_entity) {

      // Master File Entry
      $this->addMFE($_entity);

      // Location Identification
      $this->addLOC($_entity);

      // Location Characteristic
      foreach (CHL7v2TableEntry::getTable("7878", false) as $_code) {
        if (!$_code) {
          continue;
        }

        $this->addLCH($_entity, $_code);
      }

      // Location Relationship
      /*  $this->addLRL();*/
    }
  }

  /**
   * MSH - Represents an HL7 MSH message segment (Message Header)
   *
   * @return void
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }

  /**
   * MFI - Represents an HL7 MFI message segment (Master File Identification)
   *
   * @return void
   */
  function addMFI() {
    $MFI = CHL7v2Segment::create("MFI", $this->message);
    $MFI->build($this);
  }

  /**
   * MFE - Represents an HL7 MFE message segment (Master File Entry)
   *
   * @param CEntity $entity entity
   *
   * @return void
   */
  function addMFE($entity) {
    /** @var CHL7v2SegmentMFE $MFE */
    $MFE         = CHL7v2Segment::create("MFE", $this->message);
    $MFE->entity = $entity;
    $MFE->build($this, $entity);
  }

  /**
   * LOC - Represents an HL7 LOC message segment (Location Identification)
   *
   * @param CEntity $entity entity
   *
   * @return void
   */
  function addLOC($entity) {
    /** @var CHL7v2SegmentLOC $LOC */
    $LOC         = CHL7v2Segment::create("LOC", $this->message);
    $LOC->entity = $entity;
    $LOC->build($this);
  }

  /**
   * LCH - Represents an HL7 LCH message segment (Location Characteristic)
   *
   * @param CEntity $entity entity
   *
   * @return void
   */
  function addLCH($entity, $code) {
    /** @var CHL7v2SegmentLCH $LCH */
    $LCH         = CHL7v2Segment::create("LCH", $this->message);
    $LCH->entity = $entity;
    $LCH->code   = $code;
    $LCH->build($this);
  }

  /**
   * LRL - Represents an HL7 LRL message segment (Location Relationship)
   *
   * @return void
   */
  function addLRL() {
    $LRL = CHL7v2Segment::create("LRL", $this->message);
    $LRL->build($this);
  }
}