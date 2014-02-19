<?php

/**
 * H'XML
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHprimXML
 */
class CHprimXML {
  static $object_handlers = array(
    "CSipObjectHandler"     => "CSipHprimXMLObjectHandler",
    "CSmpObjectHandler"     => "CSmpHprimXMLObjectHandler",
    "CSaObjectHandler"      => "CSaHprimXMLObjectHandler",
    "CSaEventObjectHandler" => "CSaEventHprimXMLObjectHandler"
  );

  /**
   * Get object handlers
   *
   * @return array
   */
  static function getObjectHandlers() {
    return self::$object_handlers; 
  }

  /**
   * Get object tag
   *
   * @param string $group_id Group
   *
   * @return string|null
   */
  static function getObjectTag($group_id = null) {
    $context = array(get_called_class().":".__FUNCTION__, func_get_args());

    if (CFunctionCache::exist($context)) {
      return CFunctionCache::get($context);
    }

    $tag = self::getDynamicTag();

    // Permettre des id externes en fonction de l'�tablissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    return CFunctionCache::set($context, str_replace('$g', $group_id, $tag));
  }

  /**
   * Get object dynamic tag
   *
   * @return string
   */
  static function getDynamicTag() {
    return CAppUI::conf("hprimxml tag_default");
  }
}