<?php

/**
 * EAI Object handler
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIObjectHandler
 * EAI Object handler
 */

class CEAIObjectHandler extends CMbObjectHandler {
  /** @var array */
  static $handled = array ();

  /** @var  string Sender GUID */
  public $_eai_sender_guid;

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
  
  /**
   * Trigger action on the right handler
   * 
   * @param string    $action   Action name
   * @param CMbObject $mbObject Object
   * 
   * @return void
   */
  function sendFormatAction($action, CMbObject $mbObject) {
    if (!$action) {
      return;
    }

    $cn_receiver_guid = CValue::sessionAbs("cn_receiver_guid");

    // Parcours des receivers actifs
    if (!$cn_receiver_guid) {
      // On est dans le cas d'un store d'un objet depuis MB
      if (!$mbObject->_eai_sender_guid) {
        $receiver = new CInteropReceiver();
        $receivers = $receiver->getObjects();
      }
      else {
        // On est dans le cas d'un enregisrement provenant d'une interface
        /** @var CInteropSender $sender */
        $sender = CMbObject::loadFromGuid($mbObject->_eai_sender_guid);

        $receivers = array();
        // Chargement des routes du eai_sender_guid
        if (CAppUI::conf("eai use_routers")) {
          // On supprime le _eai_sender_guid sur l'objet pour pouvoir envoyer les messages
          $mbObject->_eai_sender_guid = null;

          // R�cup�ration des receivers de ttes les routes actives
          /** @var CEAIRoute[] $routes */
          $where = array();
          $where["active"] = " = '1'";
          $routes = $sender->loadBackRefs("routes", null, null, null, null, null, null, $where);

          $receivers = array();
          foreach ($routes as $_route) {
            $receiver = $_route->loadRefReceiver();
            $receivers[get_class($receiver)][] = $receiver;
          }
        }
      }
    }
    // Sinon envoi destinataire s�lectionn�
    else {
      if ($cn_receiver_guid == "none") {
        return;
      }
      $receiver = CMbObject::loadFromGuid($cn_receiver_guid);
      if (!$receiver->_id) {
        return;
      }
      $receivers[$receiver->_class][] = $receiver;
    }

    foreach ($receivers as $_receivers) {
      if (!$_receivers) {
        continue;
      }
      foreach ($_receivers as $_receiver) {
        // Destinataire non actif on envoi pas
        if (!$_receiver->actif) {
          continue;
        }

        $handler = $_receiver->getFormatObjectHandler($this);

        if (!$handler) {
          continue;
        }

        $_receiver->loadConfigValues();
        $_receiver->loadRefsMessagesSupported();

        // Affectation du receiver � l'objet
        $mbObject->_receiver = $_receiver;

        $handlers = !is_array($handler) ? array($handler) : $handler;

        // On parcours les handlers
        foreach ($handlers as $_handler) {
          // R�cup�re le handler du format
          $format_object_handler = new $_handler;

          // Envoi l'action au handler du format
          try {
            $format_object_handler->$action($mbObject);
          }
          catch (Exception $e) {
            CAppUI::setMsg($e->getMessage(), UI_MSG_WARNING);
          }
        }
      }
    }
  }

  /**
   * Trigger before event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (isset($mbObject->_eai_sender_guid)) {
      $this->_eai_sender_guid = $mbObject->_eai_sender_guid;
    }

    return true;
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    $this->_eai_sender_guid = $mbObject->_eai_sender_guid;

    if (!$mbObject->_ref_last_log && $mbObject->_class != "CIdSante400") {
      return false;
    }
    
    // Cas d'une fusion
    if ($mbObject->_merging) {
      return false;
    }
    
    if ($mbObject->_forwardRefMerging) {
      return false;
    }

    return true;
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }

    return true;
  }

  /**
   * Trigger when merge failed
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onMergeFailure(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if (!$mbObject->_fusion) {
      return false;
    }

    return true;
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if (!$mbObject->_merging) {
      return false;
    }

    return true;
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }
}