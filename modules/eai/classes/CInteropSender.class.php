<?php

/**
 * Interop Sender EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropSender 
 * Interoperability Sender
 */
class CInteropSender extends CInteropActor {
  public $user_id;
  public $save_unsupported_message;
  public $create_ack_file;
  public $delete_file;
  
  // Forward references
  /** @var CMediusers $_ref_user */
  public $_ref_user;
  /** @var CObjectToInteropSender[] $_ref_object_links */
  public $_ref_object_links;
  /** @var CEAIRoute[] $_ref_routes */
  public $_ref_routes;

  /**
   * @see parent::updateFormFields
   */
  function updateFormFields() {
    parent::updateFormFields();
       
    $this->_parent_class = "CInteropSender";
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]                  = "ref class|CMediusers";
    $props["save_unsupported_message"] = "bool default|1";
    $props["create_ack_file"]          = "bool default|1";
    $props["delete_file"]              = "bool default|1";
    
    return $props;
  }

  /**
   * @see parent::getSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->uniques["user"] = array ("user_id");
    
    return $spec;
  }

  /**
   * @see parent::getBackProps
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["messages_supported"] = "CMessageSupported object_id";
    $backProps["object_links"]       = "CObjectToInteropSender sender_id";
    $backProps["routes"]             = "CEAIRoute sender_id";

    return $backProps;
  }

  /**
   * Load object links
   *
   * @return CObjectToInteropSender[]
   */
  function loadRefsObjectLinks() {
    if ($this->_ref_object_links) {
      return $this->_ref_object_links;
    }

    return $this->_ref_object_links = $this->loadBackRefs("object_links");
  }

  /**
   * @see parent::loadRefUser
   */
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("user_id", 1);
  }

  /**
   * Load routes
   *
   * @param array $where Clause where
   *
   * @return CEAIRoute[]
   */
  function loadRefsRoutes($where = array()) {
    return $this->_ref_routes = $this->loadBackRefs("routes", null, null, null, null, null, null, $where);
  }
  
  /**
   * Get child senders
   * 
   * @return array CInteropSender collection 
   */
  static function getChildSenders() {    
    return CApp::getChildClasses("CInteropSender", array(), true);
  }
  
  /**
   * Get objects
   * 
   * @return array CInteropSender collection 
   */
  function getObjects() {
    $objects = array();
    foreach (self::getChildSenders() as $_interop_sender) {
      $itemSender = new $_interop_sender;
      
      // R�cup�ration de la liste des destinataires
      $where = array();
      $order = "group_id ASC, nom ASC";
      $objects[$_interop_sender] = $itemSender->loadList($where, $order);
      if (!is_array($objects[$_interop_sender])) {
        continue;
      }
      foreach ($objects[$_interop_sender] as $_sender) {
        $_sender->loadRefGroup();
        $_sender->isReachable();
        //$_sender->lastMessage();
      }
    }
    
    return $objects;
  }
  
  function read() {
  }
  
  function getConfigs(CExchangeDataFormat $data_format) {
    $data_format->getConfigs($this->_guid);
    $format_config = $data_format->_configs_format;
    
    if (!isset($format_config->_id)) {
      return;
    }
    
    foreach ($format_config->getConfigFields() as $_config_field) {
      $this->_configs[$_config_field] = $format_config->$_config_field;
    }
  }
}