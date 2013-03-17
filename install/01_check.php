<?php
/**
 * Installation prerequisite checker
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/header.php";

showHeader();

?>

<h2>V�rification des pr�requis</h2>

<h3>Version de PHP</h3>

<p>
  PHP est le langage d'ex�cution de script c�t� serveur de Mediboard. Il est 
  n�cessaire d'installer une version r�cente de PHP pour assurer le bon 
  fonctionnement du syst�me.
</p>

<p>
  N'h�sitez pas � vous rendre sur le site officiel de <a href="http://www.php.net/">http://www.php.net/</a>
  pour obtenir les derni�res versions de PHP.
</p>

<table class="tbl">

<tr>
  <th class="title" colspan="5">Version de PHP</th>
</tr>

<tr>
  <th>Num�ro de version</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilit�</th>
  <th>Installation ?</th>
</tr>
<?php 
  $version = new CPHPVersion;

  foreach ($version->getAll() as $prereq) { ?>
  <tr>
    <td><strong><?php echo $prereq->name; ?></strong></td>
    <td class="text"><?php echo nl2br($prereq->description); ?></td>
    <td>
      <?php if ($prereq->mandatory) { ?>
      Oui
      <?php } else { ?>
      Recommand�e
      <?php } ?>
    </td>
    <td class="text">
      <ul>
        <?php foreach ($prereq->reasons as $reason) { ?>
        <li><?php echo $reason; ?></li>
        <?php } ?>
      </ul>
    </td>
    <td>
      <?php if ($prereq->check()) { ?>
        <div class="info">Oui, Version <?php echo phpVersion(); ?></div>
      <?php } else { ?>
        <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">
          Non, Version <?php echo phpVersion(); ?>
        </div>
      <?php } ?>
    </td>
  </tr>
<?php } ?>
  
</table>

<h3>Extensions PECL</h3>
<p>
  PECL est une biblioth�que d'extensions binaires de PHP. 
  <br />
  La plupart des  extensions de base de PHP est fournie avec votre 
  distribution de PHP. Si toutefois certaines extensions sont manquantes,
  v�rifiez que :
</p>
<ul>
  <li>L'extension est install�e sur votre d�ploiement PHP</li>
  <li>L'extension est bien charg�e dans la configuration de PHP (php.ini)</li>
</ul>  
<p>
  N'h�sitez pas � vous rendre sur le site officiel de PHP <a href="http://www.php.net/">http://www.php.net/</a>
  et de PECL <a href="http://pecl.php.net/">http://pecl.php.net/</a>  pour 
  obtenir de plus amples informations. 
</p>

<table class="tbl" >

<tr>
  <th class="title" colspan="5">Extensions PECL</th>
</tr>

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilit�</th>
  <th>Installation ?</th>
</tr>

<?php 
  $extension = new CPHPExtension;
  
  foreach($extension->getAll() as $prereq) { ?>
  <tr>
    <td><strong><?php echo $prereq->name; ?></strong></td>
    <td class="text"><?php echo nl2br($prereq->description); ?></td>
    <td>
      <?php if ($prereq->mandatory) { ?>
      Oui
      <?php } else { ?>
      Recommand�e
      <?php } ?>
    </td>
    <td class="text">
      <ul>
        <?php foreach ($prereq->reasons as $reason) { ?>
        <li><?php echo $reason; ?></li>
        <?php } ?>
      </ul>
    </td>
    <td>
      <?php if ($prereq->check()) { ?>
      <div class="info">Extension charg�e</div>
      <?php } else { ?>
      <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Extension absente</div>
      <?php } ?>
    </td>
  </tr>
<?php } ?>

</table>

<h3>Packages PEAR</h3>

<p>
  PEAR est un framework de distributions de biblioth�ques �crites en PHP.
  <br />
  Si plusieurs ou tous les packages sont manquants, n'h�sitez pas � vous rendre 
  sur le site officiel <a href="http://pear.php.net/">http://pear.php.net/</a>
  pour les installer sur votre d�ploiement de PHP. 
</p>
  
<table class="tbl" >

<tr>
  <th class="title" colspan="6">Packages PEAR</th>
</tr>

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilit�</th>
  <th>Statut</th>
  <th>Installation ?</th>
</tr>

<?php 
  $package = new CPearPackage;
  
  foreach ($package->getAll() as $prereq) { ?>
  <tr>
    <td><strong><?php echo $prereq->name; ?></strong></td>
    <td class="text"><?php echo nl2br($prereq->description); ?></td>
    <td>
      <?php if ($prereq->mandatory) { ?>
      Oui
      <?php } else { ?>
      Recommand�
      <?php } ?>
    </td>
    <td class="text">
      <ul>
        <?php foreach ($prereq->reasons as $reason) { ?>
        <li><?php echo $reason; ?></li>
        <?php } ?>
      </ul>
    </td>
    <td><?php echo $prereq->status; ?></td>
    <td>
      <?php if ($prereq->check()) { ?>
        <div class="info">Package install�</div>
      <?php } else { ?>
        <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Package manquant</div>
      <?php } ?>
    </td>
  </tr>
<?php } ?>

</table>

<div class="big-info">
  Certains packages Pear ne sont pas publi�s dans un statut <strong>stable</strong>, 
  bien que suffisemment fonctionnels pour Mediboard. 
  <br />Pour pouvoir installer les packages en statut <strong>beta</strong>, il peut �tre
  n�ccessaire de configurer PEAR avec la commande :
  
  <pre>pear config-set preferred_state beta</pre>
</div>

<h3>Droits d'acc�s distants</h3>

<p>
  Certaines ressources ne devraient pas �tre accessibles autrement que depuis le serveur local.<br />
  Pour ce faire, il faut autoriser les fichiers <code>.htaccess</code> de Mediboard � red�finir certaines r�gles, en
  sp�cifiant <code>AllowOverride All</code> dans les fichiers de configuration Apache pour le r�p�rtoire web.
</p>
  
<table class="tbl" >

<tr>
  <th class="title" colspan="3">Droits d'acc�s distants</th>
</tr>

<tr>
  <th>URL</th>
  <th>Pr�-requis</th>
  <th>Validit�</th>
</tr>

<?php 

$restriction = new CUrlRestriction;

foreach ($restriction->getAll() as $_restriction) { ?>
  <tr>
    <td><strong><?php echo $_restriction->url; ?></strong></td>
    <td>Interdit (HTTP 403)</td>
    <td>
      <?php if ($_restriction->check()) { ?>
        <div class="info">OK</div>
      <?php } else { ?>
        <div class="warning">Erreur</div>
      <?php } ?>
    </td>
  </tr>
<?php } ?>

</table>

<?php showFooter(); ?>