<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("checkauth.php");

class CPathAccess {
  var $path = "";
  var $description = "";
  var $reason = array();

  function check() {
    global $mbpath;
    return is_writable($mbpath.$this->path);
  }
}

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

showHeader(); 

?>

<h2>V�rification des acc�s en �criture</h2>

<p>
  Le syst�me a besoin de pouvoir �crire un certain nombre de fichiers pour son 
  fonctionnement.
</p>

<p>
  La pr�sente page v�rifie que les permissions en �criture de PHP sur ces 
  diff�rents chemins.
</p>

<div class="big-warning">
  Il est tr�s vivement d�conseill� de s'affranchir des probl�mes de permissions en rendant 
  toute l'arborescence du syst�me accessible en �criture. Cette m�thode engendrerait
  potentiellement une grande faille de s�curit�.
  <br />
  Mediboard propose un script shell permettant d'�tablir ses permissions de fa�on 
  automatique. C'est le bon moment pour ex�cuter ce script si ce n'est pas d�j� fait !
  <pre>sh [racine/de/mediboard/]shell/setup.sh www-data</pre>
</div>

<table class="tbl">

<tr>
  <th>Chemin</th>
  <th>Description</th>
  <th>V�rification ?</th>
</tr>
  
<?php foreach($pathAccesses as $pathAccess) { ?>
<tr>
  <td><strong><?php echo $pathAccess->path; ?></strong></td>
  <td class="text"><?php echo nl2br($pathAccess->description); ?></td>
  <td>
    <?php if ($pathAccess->check()) { ?>
    <div class="message">Ok</div>
    <?php } else { ?>
    <div class="error">Erreur</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>
  
</table>

<?php showFooter(); ?>
