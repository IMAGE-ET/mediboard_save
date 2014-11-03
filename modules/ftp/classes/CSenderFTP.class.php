<?php

/**
 * Interop Sender FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSenderFTP 
 * Interoperability Sender FTP
 */
class CSenderFTP extends CInteropSender {
  // DB Table key
  public $sender_ftp_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_ftp';
    $spec->key   = 'sender_ftp_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"]   = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]    = "CEchangeHprim21 sender_id";
    $backProps["expediteur_hprimsante"] = "CExchangeHprimSante sender_id";
    $backProps["expediteur_hl7v2"]      = "CExchangeHL7v2 sender_id";
    $backProps["expediteur_hl7v3"]      = "CExchangeHL7v3 sender_id";
    $backProps["expediteur_dmp"]        = "CExchangeDMP sender_id";
    $backProps["expediteur_phast"]      = "CExchangePhast sender_id";
    $backProps["expediteur_any"]        = "CExchangeAny sender_id";
    $backProps["expediteur_mvsante"]    = "CExchangeMVSante sender_id";
    
    $backProps["config_hprimxml"]       = "CHprimXMLConfig sender_id";
    $backProps["config_hprimsante"]     = "CHPrimSanteConfig sender_id";
    $backProps["config_hl7"]            = "CHL7Config sender_id";
    $backProps["config_mvsante"]        = "CMVSanteXMLConfig sender_id";
    
    
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $source_ftp = CExchangeSource::get("$this->_guid", "ftp", true, $this->_type_echange, false);
    $this->_ref_exchanges_sources[$source_ftp->_guid] = $source_ftp;
  }
  
  function read() {
  }
}