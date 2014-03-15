<?php
/**
 * Installation database feed
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkconfig.php";
require_once "includes/checkauth.php";

$dbConfigs = $dPconfig["db"];
unset($dbConfigs["ccam"]);

showHeader(); 

?>

<h2>Test et construction initiale de la base de donn�es</h2>

<h3>Construction de la base principale</h3>

<p>
  Cette op�ration va cr�er les structures initiales des tables de la base de 
  donn�es principale. La connexion pour la configuration 'std' doit �tre 
  op�rationnelle pour continuer.
</p>

<form action="06_feed.php" name="feedBase" method="post">  
<input type="hidden" name="do" value="true" />
<table class="form">
  <tr>
    <th class="category">Construction de la base</th>
  </tr>
  <tr>
    <td class="button">
      <button class="modify" type="submit">Construire la base</button>
    </td>
  </tr>
</table>

</form>

<?php 
if (@$_POST["do"]) {
  $db = CMbDb::getStd();
  $db->queryDump("includes/mediboard.sql");
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Construction des tables pour les 'core' modules</td>
  <td>
    <?php if (!count($dbConnection->_errors)) { ?>
    <div class="info">Construction r�ussie</div>
    <?php } else { ?>
    <div class="error">
      Erreurs lors de la construction
      <br />
      <?php echo nl2br(implode("\n", $dbConnection->_errors)); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

<?php
if ($db->getOne("SELECT * FROM users")) {
?>

<div class="small-warning">
  Attention, la base de donn�es principale poss�de d�j� une structure. La 
  reconstruire endommagerait probablement les donn�es. 
  <br />
  Si vous d�sirez re-cr�er une structure il est n�cessaire de vider initialement 
  la base avec un gestionnaire adapt� (comme <a href="http://www.phpmyadmin.net/" target="_blank">PHPMyAdmin</a>).
</div>

<?php } ?>

<?php showFooter(); ?>