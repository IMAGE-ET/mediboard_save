<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Cronjob log
 */
class CCronJobLog extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $cronjob_log_id;

  public $status;
  public $error;
  public $cronjob_id;
  public $start_datetime;
  public $end_datetime;
  public $server_address;

  /** @var CCronJob */
  public $_ref_cronjob;

  public $_duration;

  //filter
  public $_date_min;
  public $_date_max;

  //Log d'erreur utilis� pour v�rifier si le script s'est bien d�roul�
  static public $log;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "cronjob_log";
    $spec->key    = "cronjob_log_id";
    return $spec;  
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    if ($this->end_datetime && $this->start_datetime) {
      $this->_duration = CMbDT::timeRelative($this->start_datetime, $this->end_datetime);
    }
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["status"]         = "enum list|started|finished|error notNull";
    $props["error"]          = "str";
    $props["cronjob_id"]     = "ref class|CCronJob notNull autocomplete|name";
    $props["start_datetime"] = "dateTime notNull";
    $props["end_datetime"]   = "dateTime";
    $props["server_address"] = "str";

    //filter
    $props["_date_min"]      = "dateTime";
    $props["_date_max"]      = "dateTime";

    $props["_duration"]      = "str";

    return $props;
  }

  /**
   * Load the cronjob
   *
   * @return CCronJob|null
   */
  function loadRefCronJob() {
    return $this->_ref_cronjob = $this->loadFwdRef("cronjob_id");
  }
}