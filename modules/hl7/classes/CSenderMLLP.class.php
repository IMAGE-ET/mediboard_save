<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CSenderMLLP 
 * Interoperability Sender MLLP
 */
class CSenderMLLP extends CInteropSender {
  // DB Table key
  public $sender_mllp_id;
  
  public $_duplicate;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_mllp';
    $spec->key   = 'sender_mllp_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["expediteur_hprimsante"] = "CExchangeHprimSante sender_id";
    $backProps["expediteur_hl7v2"]      = "CExchangeHL7v2 sender_id";
    $backProps["expediteur_mvsante"]    = "CMVSanteXMLConfig sender_id";

    $backProps["config_hprimxml"]       = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]            = "CHL7Config sender_id";
    $backProps["config_hprimsante"]     = "CHPrimSanteConfig sender_id";
        
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $source_mllp = CExchangeSource::get("$this->_guid", "mllp", true, $this->_type_echange, false);
    $this->_ref_exchanges_sources[$source_mllp->_guid] = $source_mllp;
  }
  
  function read() {
    $this->loadRefsExchangesSources();
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->_duplicate) {
      $duplicate = new self;
      
      foreach ($this->getProperties() as $name => $value) {
        if ($name[0] !== "_" && $name != $this->_spec->key) {
          $duplicate->$name = $value;
        }
      }
      
      $duplicate->nom     .= " (Copy)";
      if ($duplicate->libelle) {
        $duplicate->libelle .= " (Copy)";
      }
      
      $duplicate->store();
    }
    
    $this->_duplicate = null;
  }
}
