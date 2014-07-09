<?php

/**
 * Echange XML EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEchangeXML 
 * Echange XML
 */

class CEchangeXML extends CExchangeDataFormat {
  public $identifiant_emetteur;
  public $initiateur_id;  
 
  // Forward references
  public $_ref_notifications;
  
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    return $spec;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["identifiant_emetteur"]    = "str";
    $props["message_content_id"]      = "ref class|CContentXML show|0 cascade";
    $props["acquittement_content_id"] = "ref class|CContentXML show|0 cascade";   
    
    $props["_message"]                = "xml";
    $props["_acquittement"]           = "xml";
    
    return $props;
  }
   
  /**
   * Load content
   * 
   * @return void
   */ 
  function loadContent() {
    $content = new CContentXML();
    $content->load($this->message_content_id);
    $this->_message = $content->content;
    
    $content = new CContentXML();
    $content->load($this->acquittement_content_id);
    $this->_acquittement = $content->content;
  }
  
  /**
   * @see parent::updatePlainFields()
   */   
  function updatePlainFields() {
    parent::updatePlainFields();

    if ($this->_message !== null) {
      /** @var CContentXML $content */
      $content = $this->loadFwdRef("message_content_id", true);
      $content->content = $this->_message;
      if ($msg = $content->store()) {
        return;
      }
      if (!$this->message_content_id) {
        $this->message_content_id = $content->_id;
      }
    }
    
    if ($this->_acquittement !== null) {
      /** @var CContentXML $content */
      $content = $this->loadFwdRef("acquittement_content_id", true);
      $content->content = $this->_acquittement;
      if ($msg = $content->store()) {
        return;
      }
      if (!$this->acquittement_content_id) {
        $this->acquittement_content_id = $content->_id;
      }
    }
  }

  /**
   * Set ACK error
   *
   * @param DOMDocument     $dom_acq      Acquittement
   * @param array           $code_erreur  Error codes
   * @param string|null     $commentaires Comments
   * @param CMbObject|array $values       Values
   *
   * @return void
   */
  function setAckError($dom_acq, $code_erreur, $commentaires = null, $values = null) {
  }

  /**
   * @see parent::isWellFormed()
   */
  function isWellFormed($data) {
    $dom = new CMbXMLDocument();
    if ($dom->loadXML($data, LIBXML_NOWARNING | LIBXML_NOERROR) !== false) {
      return $dom;
    }

    return null;
  }

  /**
   * @see parent::understand()
   */
  function understand($data, CInteropSender $actor = null) {
    if (!$dom = $this->isWellFormed($data)) {
      return false;
    }

    $root = $dom->documentElement;
    $nodeName = $root->nodeName;
    foreach ($this->getFamily() as $_message) {
      $message_class = new $_message;
      $document_elements = $message_class->getDocumentElements();

      if (array_key_exists($nodeName, $document_elements)) {
        $this->_family_message = new $document_elements[$nodeName];
        return true;
      }
    }

    return false;
  }
}


