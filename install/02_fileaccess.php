<?php
/**
 * Installation file access checker
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";

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
  <pre>sh [racine/de/mediboard/]shell/setup.sh</pre>
  Si cette ex�cution pose probl�me, essayez de l'ex�cuter avec le param�tre suivant :
  <pre>sh [racine/de/mediboard/]shell/setup.sh -g [groupe apache]</pre>
  Exemples de valeurs pour [groupe apache] :
  <ul>
    <li>Sur Ubuntu : www-data</li>
    <li>Sur Mac Os X : _www</li>
  </ul>
</div>

<table class="tbl">

<tr>
  <th>Chemin</th>
  <th>Description</th>
  <th>V�rification ?</th>
</tr>
  
<?php 
$pathAccess = new CPathAccess();
foreach ($pathAccess->getAll() as $pathAccess) { ?>
<tr>
  <td><strong><?php echo $pathAccess->path; ?></strong></td>
  <td class="text"><?php echo nl2br($pathAccess->description); ?></td>
  <td>
    <?php if ($pathAccess->check()) { ?>
    <div class="info">Ok</div>
    <?php } else { ?>
    <div class="error">Erreur</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>
  
</table>

<?php showFooter(); ?>