<?php

/**
 * $Id: object_config.class.php 8220 2010-03-05 13:06:52Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CDestinataireHprimConfig
 */
class CDestinataireHprimConfig extends CMbObjectConfig {
  public $dest_hprim_config_id;
  
  public $object_id; // CDestinataireHprim
  
  // Format
  public $encoding;
  public $uppercase_fields;
  
  // Send
  public $send_sortie_prevue;
  public $send_all_patients;
  public $send_default_serv_with_type_sej;
  public $send_volet_medical;
  public $send_movement_location;
  public $send_insured_without_admit;
  
  // Application
  public $receive_ack;
  
  public $_categories = array(
    // Format
    "format" => array(
      "encoding", 
      "uppercase_fields",
    ),
    
    // Application
    "application" => array(
      "receive_ack" 
    ),
    
    // Send
    "send" => array(
      "send_sortie_prevue", 
      "send_all_patients",
      "send_default_serv_with_type_sej",
      "send_volet_medical",
      "send_movement_location",
      "send_insured_without_admit"
    )
  );

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "destinataire_hprim_config";
    $spec->key   = "dest_hprim_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"] = "ref class|CDestinataireHprim";
    
    // Format
    $props["encoding"]         = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    $props["uppercase_fields"] = "bool default|0";
    
    // Send
    $props["send_sortie_prevue"]              = "bool default|1"; 
    $props["send_all_patients"]               = "bool default|0";
    $props["send_default_serv_with_type_sej"] = "bool default|0";
    $props["send_volet_medical"]              = "bool default|0";
    $props["send_movement_location"]          = "bool default|0";
    $props["send_insured_without_admit"]      = "bool default|0";
    
    // Application
    $props["receive_ack"]          = "bool default|1";
    
    return $props;
  }
}