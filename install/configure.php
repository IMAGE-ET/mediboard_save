<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("checkauth.php");
require_once($mbpath."classes/mbconfig.class.php");

if(isset($_POST["username"])){
 unset($_POST["username"]); 
}
if(isset($_POST["password"])){
 unset($_POST["password"]); 
}

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;

?>

<?php showHeader(); ?>

<h2>Cr�ation du fichier de configuration</h2>

<form name="configure" action="configure.php" method="post">

<table class="form">

  <tr>
    <th class="category" colspan="2">Configuration g�n�rale</th>
  </tr>

  <tr>
    <th><label for="root_dir" title="Repertoire racine. Pas de slash final. Utiliser les slashs aussi pour MS Windows">R�pertoire racine :</label></th>
    <td><input type="text" size="40" name="root_dir" value="<?php echo $dPconfig["root_dir"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="company_name" title="Nom de la soci�t�">Nom de la soci�t� :</label></th>
    <td><input type="text" size="40" name="company_name" value="<?php echo $dPconfig['company_name'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="page_title" title="Titre de la page web dans la barre de navigation">Titre de la page web :</label></th>
    <td><input type="text" size="40" name="page_title" value="<?php echo $dPconfig['page_title'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="base_url" title="Url Racine pour le syst�me">Url racine :</label></th>
    <td><input type="text" size="40" name="base_url" value="<?php echo $dPconfig['base_url'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="site_domain" title="Nom de domaine de premier du syst�me">Nom de domaine :</label></th>
    <td><input type="text" size="40" name="site_domain" value="<?php echo $dPconfig['site_domain'] ?>" /></td>
  </tr>

</table>

<table class="form">

  <tr>
    <th class="category" colspan="4">Configurations des bases de donn�es</th>
  </tr>

  <tr>
    <th colspan="2"><label for="dbtype" title="Type de base de donn�es. Seul mysql est possible pour le moment">Type de base de donn�es :</label></th>
    <td colspan="2" class="readonly"><input type="text" readonly="readonly" size="20" name="dbtype" value="<?php echo @$dPconfig["dbtype"]; ?>" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Base de donn�es principale</th>
    <th class="category" colspan="2">Base de donn�es des GHM</th>
  </tr>

  <tr>
    <th><label for="baseMediboard" title="Merci de choisir une configuration de base de donn�es">Nom de la configuration :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="baseMediboard" value="<?php echo @$dPconfig["baseMediboard"]; ?>" /></td>
    <th><label for="baseGHS" title="Merci de choisir une configuration de base de donn�es">Nom de la configuration :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="baseGHS" value="<?php echo @$dPconfig["baseGHS"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbhost]" title="Nom de l'h�te">Nom de l'h�te :</label></th>
    <td><input type="text" size="40" name="db[std][dbhost]" value="<?php echo @$dPconfig["db"]["std"]["dbhost"]; ?>" /></td>
    <th><label for="db[GHS1010][dbhost]" title="Nom de l'h�te">Nom de l'h�te :</label></th>
    <td><input type="text" size="40" name="db[GHS1010][dbhost]" value="<?php echo @$dPconfig["db"]["GHS1010"]["dbhost"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[std][dbname]" value="<?php echo @$dPconfig["db"]["std"]["dbname"]; ?>" /></td>
    <th><label for="db[GHS1010][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[GHS1010][dbname]" value="<?php echo @$dPconfig["db"]["GHS1010"]["dbname"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[std][dbuser]" value="<?php echo @$dPconfig["db"]["std"]["dbuser"]; ?>" /></td>
    <th><label for="db[GHS1010][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[GHS1010][dbuser]" value="<?php echo @$dPconfig["db"]["GHS1010"]["dbuser"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[std][dbpass]" value="<?php echo @$dPconfig["db"]["std"]["dbpass"]; ?>" /></td>
    <th><label for="db[GHS1010][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[GHS1010][dbpass]" value="<?php echo @$dPconfig["db"]["GHS1010"]["dbpass"]; ?>" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Base de donn�es CCAM</th>
    <th class="category" colspan="2">Base de donn�es CIM</th>
  </tr>

  <tr>
    <th><label for="baseCCAM" title="Merci de choisir une configuration de base de donn�es">Nom de la configuration :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="baseCCAM" value="<?php echo @$dPconfig["baseCCAM"]; ?>" /></td>
    <th><label for="baseCIM10" title="Merci de choisir une configuration de base de donn�es">Nom de la configuration :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="baseCIM10" value="<?php echo @$dPconfig["baseCIM10"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[ccamV2][dbhost]" title="Nom de l'h�te">Nom de l'h�te :</label></th>
    <td><input type="text" size="40" name="db[ccamV2][dbhost]" value="<?php echo @$dPconfig["db"]["ccamV2"]["dbhost"]; ?>" /></td>
    <th><label for="db[cim10][dbhost]" title="Nom de l'h�te">Nom de l'h�te :</label></th>
    <td><input type="text" size="40" name="db[cim10][dbhost]" value="<?php echo @$dPconfig["db"]["cim10"]["dbhost"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[ccamV2][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[ccamV2][dbname]" value="<?php echo @$dPconfig["db"]["ccamV2"]["dbname"]; ?>" /></td>
    <th><label for="db[cim10][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[cim10][dbname]" value="<?php echo @$dPconfig["db"]["cim10"]["dbname"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[ccamV2][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[ccamV2][dbuser]" value="<?php echo @$dPconfig["db"]["ccamV2"]["dbuser"]; ?>" /></td>
    <th><label for="db[cim10][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[cim10][dbuser]" value="<?php echo @$dPconfig["db"]["cim10"]["dbuser"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[ccamV2][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[ccamV2][dbpass]" value="<?php echo @$dPconfig["db"]["ccamV2"]["dbpass"]; ?>" /></td>
    <th><label for="db[cim10][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[cim10][dbpass]" value="<?php echo @$dPconfig["db"]["cim10"]["dbpass"]; ?>" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Base de don�es INSEE</th>
  </tr>

  <tr>
    <th><label for="baseINSEE" title="Merci de choisir une configuration de base de donn�es">Nom de la configuration :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="baseINSEE" value="<?php echo @$dPconfig["baseINSEE"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[INSEE][dbhost]" title="Nom de l'h�te">Nom de l'h�te :</label></th>
    <td><input type="text" size="40" name="db[INSEE][dbhost]" value="<?php echo @$dPconfig["db"]["INSEE"]["dbhost"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[INSEE][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[INSEE][dbname]" value="<?php echo @$dPconfig["db"]["INSEE"]["dbname"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[INSEE][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[INSEE][dbuser]" value="<?php echo @$dPconfig["db"]["INSEE"]["dbuser"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[INSEE][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[INSEE][dbpass]" value="<?php echo @$dPconfig["db"]["INSEE"]["dbpass"]; ?>" /></td>
  </tr>

</table>

<table class="form">

  <tr>
    <th class="category" colspan="2">Param�tres d'IHM</th>
  </tr>

  <tr>
    <th><label for="currency_symbol" title="Symbole mon�taire. Entit�s HTML accept�es">Symbole mon�taire :</label></th>
    <td><input type="text" size="40" name="currency_symbol" value="<?php echo $dPconfig['currency_symbol'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="hide_confidential" title="Brouiller les donn�es confidentielles. Utiles pour le monde de d�monstration">Brouiller les donn�es confidentielles ?</label></th>
    <td><input type="hide_confidential" size="40" name="hide_confidential" value="<?php echo $dPconfig['hide_confidential'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="locale_warn" title="Alerter les absence de traduction. En ajoutant une marque autour">Alerter les absence de traduction ? </label></th>
    <td><input type="text" size="40" name="locale_warn" value="<?php echo $dPconfig['locale_warn'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="debug" title="Version de d�bogage. Affiche toutes les alertes PHP et la console Smarty">Version de d�bogage ?</label></th>
    <td><input type="text" size="40" name="debug" value="<?php echo $dPconfig['debug'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="shared_memory" title="Choisir quelle extension doit tenter de g�rer la m�moire partag�e (celle-ci doit �tre install�e)">M�moire partag�e ?</label></th>
    <td>
      <select name="shared_memory" size="1">
        <option value="none"         <?php if ($dPconfig['shared_memory'] == 'none'        ) { echo 'selected="selected"'; } ?> >Aucune</option>
        <option value="eaccelerator" <?php if ($dPconfig['shared_memory'] == 'eaccelerator') { echo 'selected="selected"'; } ?> >eAccelertaror</option>
      </select>
    </td>
  </tr>

</table>

<table class="form">

  <tr>
    <th class="category" colspan="2">Param�tres d'indexation de fichiers</th>
  </tr>

  <tr>
    <th><label for="ft[default]" title="Application par d�faut">Application par d�faut :</label></th>
    <td><input type="text" size="40" name="ft[default]" value="<?php echo $dPconfig['ft']['default'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[application/msword]" title="Application par d�faut">Application pour les fichiers MIME <strong>application/msword</strong> :</label></th>
    <td><input type="text" size="40" name="ft[application/msword]" value="<?php echo $dPconfig['ft']['application/msword'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[text/html]" title="Application par d�faut">Application pour les fichiers MIME <strong>text/html</strong> :</label></th>
    <td><input type="text" size="40" name="ft[text/html]" value="<?php echo $dPconfig['ft']['text/html'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[application/pdf]" title="Application par d�faut">Application pour les fichiers MIME <strong>application/pdf</strong> :</label></th>
    <td><input type="text" size="40" name="ft[application/pdf]" value="<?php echo $dPconfig['ft']['application/pdf'] ?>" /></td>
  </tr>

</table>

<table class="form">

  <tr>
    <th class="category" colspan="2">Param�tres de compatibilit�</th>
  </tr>

  <tr>
    <th><label for="interop[mode_compat]" title="Mode de compatibilit�">Mode de compatibilit� :</label></th>
    <td>
      <select name="interop[mode_compat]">
        <option value="default" <?php if($dPconfig['interop']['mode_compat'] == 'default'){echo 'selected="selected"';} ?> >Par d�faut</option>
        <option value="medicap" <?php if($dPconfig['interop']['mode_compat'] == 'medicap'){echo 'selected="selected"';} ?> >Medicap</option>
        <option value="tonkin"  <?php if($dPconfig['interop']['mode_compat'] == 'tonkin' ){echo 'selected="selected"';} ?> >Tonkin</option>
      </select>
    </td>
  </tr>

  <tr>
    <th><label for="interop[base_url]" title="Adresse externe">Adresse de l'appli externe</label></th>
    <td><input type="text" size="40" name="interop[base_url]" value="<?php echo $dPconfig['interop']['base_url'] ?>" /></td>
  </tr>

</table>

<table class="form">

  <tr>
    <th class="category">Validation obligatoire</th>
  </tr>

  <tr>
    <td class="button"><button class="submit" type="submit">Valider la configuration</button></td>
  </tr>
</table>

</form>

<?php showFooter(); ?>
