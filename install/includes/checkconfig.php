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

require_once "header.php";

if (!@include_once $mbpath."includes/config.php") {
  showHeader();
?>
  
  <div class="small-error">
    Erreur : Le fichier de configuration n'a pas �t� valid�, merci de revenir � l'�tape 
    pr�c�dente.
  </div>
  
<?php
  showFooter();
}
?>