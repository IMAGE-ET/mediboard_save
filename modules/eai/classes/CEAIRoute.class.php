<?php

/**
 * EAI router
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIRoute
 * EAI router
 */

class CEAIRoute extends CMbObject {
  // DB Table key
  public $eai_router_id;

  // DB fields
  public $sender_id;
  public $sender_class;
  public $receiver_id;
  public $receiver_class;
  public $active;
  public $description;

  /** @var CInteropSender */
  public $_ref_sender;

  /** @var CInteropSender */
  public $_ref_receiver;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'eai_router';
    $spec->key   = 'eai_router_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["sender_class"]   = "str notNull class maxLength|80";
    $props["sender_id"]      = "ref notNull class|CInteropSender meta|sender_class";
    $props["receiver_class"] = "str notNull class maxLength|80";
    $props["receiver_id"]    = "ref notNull class|CInteropActor meta|receiver_class";
    $props["active"]         = "bool default|1";
    $props["description"]    = "text";

    return $props;
  }

  /**
   * Load the sender
   *
   * @return mixed
   */
  function loadRefSender() {
    return $this->_ref_sender = $this->loadFwdRef("sender_id");
  }

  /**
   * Load the receiver
   *
   * @return mixed
   */
  function loadRefReceiver() {
    return $this->_ref_receiver = $this->loadFwdRef("receiver_id");
  }
}