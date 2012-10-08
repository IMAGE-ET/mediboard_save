<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
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
   * @see parent::check
   * 
   * @return bool
   */
  function check($strict = true) {
    return phpversion() >= $this->name;
  }
  
  function getAll() {
    $versions = array();
    
    // Do not use $version which is a Mediboard global
    $php = new CPHPVersion;
    $php->name = "5.2";
    $php->mandatory = true;
    $php->description = "Version de PHP5 r�cente";
    $php->reasons[] = "Int�gration du support XML natif : utilisation pour l'int�rop�rabilit� HPRIM XML'";
    $php->reasons[] = "Int�gration de PDO : acc�s universel et s�curis� aux base de donn�es";
    $php->reasons[] = "Conception objet plus �volu�e";
    $versions[] = $php;
    
    return $versions;
  }
}
