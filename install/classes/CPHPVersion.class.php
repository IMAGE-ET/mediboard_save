<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id$
 * @link       http://www.mediboard.org
 */

/**
 * PHP version prerequisite
 */
class CPHPVersion extends CPrerequisite {

  /**
   * Compare PHP version
   *
   * @param bool $strict Check also warnings
   *
   * @see parent::check
   *
   * @return bool
   */
  function check($strict = true) {
    return phpversion() >= $this->name;
  }

  /**
   * @see parent::getAll()
   */
  function getAll() {
    $versions = array();

    // Do not use $version which is a Mediboard global
    $php = new CPHPVersion;
    $php->name = "5.4";
    $php->mandatory = true;
    $php->description = "Version de PHP5 r�cente";
    $php->reasons[] = "Conception objet �volu�e";
    $php->reasons[] = "Optimisation m�moire";
    $php->reasons[] = "Performances";
    $versions[] = $php;

    return $versions;
  }
}
