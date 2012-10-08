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
 * File access check helper
 * Responsibilities:
 *  - path and description of path
 *  - checking
 */
class CPathAccess extends CCheckable {
  var $path = "";
  var $description = "";

  /**
   * Actually check path is writable
   * 
   * @return bool
   */
  function check($strict = true) {
    global $mbpath;
    return is_writable($mbpath.$this->path);
  }
  
  function getAll() {
    $pathAccesses = array();
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "tmp/";
    $pathAccess->description = "R�pertoire des fichiers temporaires";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "files/";
    $pathAccess->description = "R�pertoire de tous les fichiers attach�s";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "lib/";
    $pathAccess->description = "R�pertoire d'installation des biblioth�ques tierces";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "includes/";
    $pathAccess->description = "R�pertoire du fichier de configuration du syst�me";
    
    $pathAccesses[] = $pathAccess;
    
    $pathAccess = new CPathAccess;
    $pathAccess->path = "modules/hprimxml/xsd";
    $pathAccess->description = "R�pertoire des schemas HPRIM";
    
    $pathAccesses[] = $pathAccess;
    
    return $pathAccesses;
  }
}
