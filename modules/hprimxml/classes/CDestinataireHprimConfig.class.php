<?php /* $Id: object_config.class.php 8220 2010-03-05 13:06:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8220 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDestinataireHprimConfig extends CMbObject {
  var $dest_hprim_config_id = null;
  
  var $object_id            = null; // CDestinataireHprim
  // Object configs
  var $send_sortie_prevue   = null; 
  var $receive_ack          = null;
  var $send_all_patients    = null;
  var $send_debiteurs_venue = null;
  var $send_mvt_patients    = null;
  var $send_default_serv_with_type_sej = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "destinataire_hprim_config";
    $spec->key   = "dest_hprim_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["object_id"]                       = "ref class|CDestinataireHprim";
    
    $props["send_sortie_prevue"]              = "bool default|1"; 
    $props["receive_ack"]                     = "bool default|1";
    $props["send_all_patients"]               = "bool default|0";
    $props["send_debiteurs_venue"]            = "bool default|1";
    $props["send_mvt_patients"]               = "bool default|0";
    $props["send_default_serv_with_type_sej"] = "bool default|0";
    
    return $props;
  }
}
?>