<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSmpObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CSejour", "CAffectation");

  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }

  function onAfterStore(CMbObject &$mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    // Si pas de tag s�jour
    if (!CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas de NDA sur le s�jour
    if ((isset($mbObject->_no_num_dos) && ($mbObject->_no_num_dos == 1)) &&
        CAppUI::conf('sip server')) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  function onBeforeMerge(CMbObject &$mbObject) {
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onBeforeMerge", $mbObject);
  }
  
  function onAfterMerge(CMbObject &$mbObject) {
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterMerge", $mbObject);
  }
  
  function onAfterDelete(CMbObject &$mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}
?>