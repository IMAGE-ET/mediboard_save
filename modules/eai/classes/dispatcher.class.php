<?php

/**
 * Dispatcher EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIDispatcher
 * Dispatcher EAI
 */

class CEAIDispatcher {  
  static function dispatch(CInteropSender $actor, $data) {
    $understand = false;
    foreach (CExchangeDataFormat::getAll() as $_data_format) {
      $data_format = new $_data_format;
      // Test si le message est bien form� et si l'exp�diteur traite le type
      $understand = $data_format->understand($actor, $data);     
      if ($understand) {
        break;
      }
    }
    if (!$understand) {
      CAppUI::stepAjax("CEAIDispatcher-no-understand", UI_MSG_WARNING);
      return false;
    }
    
    // Traitement par le handler du format
    try {
      $data_format->handle($actor, $data);    
    } catch(Exception $e) {
      CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
    }
  }
}

?>