<?php

/**
 * K22 - Find Candidates response - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventPDQK22
 * K22 - Find Candidates response
 */
class CHL7v2EventQBPZV2 extends CHL7v2EventQBP implements CHL7EventQBPK22 {

  /** @var string */
  public $code = "ZV2";

  /**
   * Construct
   *
   * @return \CHL7v2EventQBPZV2
   */
  function __construct() {
    parent::__construct();

    $this->profil = "PDQ";
  }

  /**
   * Build ZV2 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
  }
}