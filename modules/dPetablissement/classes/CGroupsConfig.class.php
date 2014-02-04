<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Group level configuration
 *
 * @deprecated It should migrate to CConfiguration
 */
class CGroupsConfig extends CMbObjectConfig {
  public $groups_config_id;
  
  public $object_id; // CGroups
  
  // Object configs
  public $max_comp;
  public $max_ambu;
  public $codage_prat;
  
  public $dPpatients_CPatient_nom_jeune_fille_mandatory;
  
  public $dPplanningOp_COperation_DHE_mode_simple;
  
  public $ecap_CRPU_notes_creation;
  
  // SIP
  public $sip_notify_all_actors;
  public $sip_idex_generator;
  
  // SMP
  public $smp_notify_all_actors;
  public $smp_idex_generator;
  
  public $dPprescription_CPrescription_show_trash_24h;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "groups_config";
    $spec->key   = "groups_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"]          = "ref class|CGroups";
    
    $props["max_comp"]    = "num min|0";
    $props["max_ambu"]    = "num min|0";
    $props["codage_prat"] = "bool default|0";
    
    $props["dPpatients_CPatient_nom_jeune_fille_mandatory"] = "bool default|0";
    
    $props["dPplanningOp_COperation_DHE_mode_simple"]       = "bool default|0";
    
    $props["ecap_CRPU_notes_creation"] = "bool default|0";
    
    // SIP
    $props["sip_notify_all_actors"] = "bool default|0";
    $props["sip_idex_generator"]    = "bool default|0";
    
    // SMP
    $props["smp_notify_all_actors"] = "bool default|0";
    $props["smp_idex_generator"]    = "bool default|0";
    
    $props["dPprescription_CPrescription_show_trash_24h"] = "bool default|0";
    
    return $props;
  }
}
