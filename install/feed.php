<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("checkconfig.php");
require_once("checkauth.php");

require_once("dbconnection.php");
$dbConfigs = $dPconfig["db"];
unset($dbConfigs["ccam"]);

?>

<?php showHeader(); ?>

<h2>Test et construction initiale de la base de donn�es</h2>

<h3>Construction de la base principale</h3>

<p>
  Cette op�ration va cr�er les structures initiales des tables de la base de 
  donn�es principale. La connexion pour la configuration 'std' doit �tre 
  op�rationnelle pour continuer.
</p>

<form action="feed.php" name="feedBase" method="post">  

<table class="form">
  <tr>
    <th class="category">Construction de la base</th>
  </tr>
  <tr>
    <td class="button">
      <input type="submit" name="do" value="Construire de la base" />
    </td>
  </tr>
</table>

</form>

<?php 
if (@$_POST["do"]) {
  $dbConnection = new CMbDb(
    $dbConfig["dbhost"], 
    $dbConfig["dbuser"], 
    $dbConfig["dbpass"], 
    $dbConfig["dbname"]);
  if ($dbConnection->connect()) {
    $dbConnection->queryDump("mediboard.sql");
  }
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Cr�ations des bases et des utilisateurs</td>
  <td>
    <?php if (!count($dbConnection->_errors)) { ?>
    <div class="message">Cr�ations r�ussies</div>
    <?php } else { ?>
    <div class="error">
      Erreurs lors des cr�ations
      <br />
      <?php echo nl2br(join($dbConnection->_errors, "\n")); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

<?php 
$dbConfig = $dbConfigs["std"];
$db = new CMbDb(
  $dbConfig["dbhost"], 
  $dbConfig["dbuser"], 
  $dbConfig["dbpass"], 
  $dbConfig["dbname"]);
$db->connect();
if ($db->getOne("SELECT * FROM `users`")) {
?>

<div class="big-warning">
  Attention, la base de donn�es principale actuellement d�j� une structure. La 
  reconstruire endommagerait probablement les donn�es. 
  <br />
  Si vous d�sirez re-cr�er une structure il est n�cessaire de vider initialement 
  la base avec un gestionnaire adapt�.
</div>

<?php } ?>


<?php showFooter(); ?>