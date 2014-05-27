<?php

/**
 * dPccam
 *
 * Classe parente de l'acc�s � la base CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CCCAM
 */
class CCCAM {

  /** @var CMbObjectSpec */
  public $_spec;

  /** @var CMbObjectSpec */
  static $spec = null;

  /**
   * Get object spec
   *
   * @return CMbObjectSpec
   */
  static function getSpec() {
    if (self::$spec) {
      return self::$spec;
    }

    $spec = new CMbObjectSpec();
    $spec->dsn = "ccamV2";
    $spec->init();

    return self::$spec = $spec;
  }

  /**
   * Methode de pr�-serialisation
   *
   * @return array
   */
  function __sleep() {
    $fields = get_object_vars($this);
    unset($fields["_spec"]);
    return array_keys($fields);
  }

  /**
   * M�thode de "reveil" apr�s serialisation
   *
   * @return void
   */
  function __wakeup() {
    $this->_spec = self::getSpec();
  }
}