<?php
/**
 * Installation main configure form
 *
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id$
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";
require_once $mbpath."includes/compat.php";
require_once $mbpath."classes/CMbConfig.class.php";
require_once $mbpath."classes/CMbArray.class.php";

if (isset($_POST["username"])) {
  unset($_POST["username"]);
}

if (isset($_POST["password"])) {
  unset($_POST["password"]);
}

$mutex_drivers = array(
  "CMbRedisMutex" => array(
    "title"  => "Redis",
    "params" => "Serveurs Redis, s�par�s par des virgules",
  ),
  "CMbAPCMutex"   => array(
    "title"  => "APC",
    "params" => null,
  ),
  "CMbFileMutex"  => array(
    "title" => "Fichier",
    "params" => null,//"Dossier contenant les verrous (par d�faut ./tmp)"
  ),
);

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;

showHeader();

?>

<script>
  toggleMemoryParams = function(value) {
    each($$('.shared-memory-params'), hideElement);
    each($$('.params-'+value), showElement);
  };

  toggleSessionMutex = function(value) {
    each($$('.session-mutex'), hideElement);
    each($$('.session-mutex-'+value), showElement);
  };

  window.onload = function(){
    toggleMemoryParams($$('#shared_memory_distributed')[0].value);
    toggleSessionMutex($$('#session_handler')[0].value);
  };
</script>

<h2>Cr�ation du fichier de configuration</h2>

<form name="configure" action="04_configure.php" method="post">
  <fieldset>
    <legend>Configuration g�n�rale</legend>

    <table class="form">
      <col style="width: 25%" />
      <tr>
        <th>
          <label for="root_dir" title="Repertoire racine. Pas de slash final. Utiliser les slashs aussi pour MS Windows">
            R�pertoire racine
          </label>
        </th>
        <td colspan="2"><input type="text" size="40" name="root_dir" value="<?php echo $dPconfig["root_dir"]; ?>" /></td>
      </tr>

      <tr>
        <th>
          <label for="base_url" title="Url Racine pour le syst�me">
            Url racine
          </label>
        </th>
        <td colspan="2"><input type="text" size="40" name="base_url" value="<?php echo $dPconfig['base_url'] ?>" /></td>
      </tr>

      <tr>
        <th><label for="instance_role">R�le de l'instance</label></th>
        <td colspan="2">
          <select name="instance_role">
            <option value="prod"   <?php if ($dPconfig['instance_role'] == 'prod'  ) { echo 'selected'; } ?> >Production</option>
            <option value="qualif" <?php if ($dPconfig['instance_role'] == 'qualif') { echo 'selected'; } ?> >Qualification</option>
          </select>
        </td>
      </tr>

      <tr>
        <th>
          <label for="http_redirections" title="Active les redirections http d�finies dans Mediboard">
            Redirections http actives
          </label>
        </th>
        <td colspan="2">
          <label>
            <input type="radio" name="http_redirections" value="0" id="http_redirections_0"
              <?php if ($dPconfig['http_redirections'] == "0") { echo 'checked'; } ?> />
            Non
          </label>

          <label>
            <input type="radio" name="http_redirections" value="1" id="http_redirections_1"
              <?php if ($dPconfig['http_redirections'] == "1") { echo 'checked'; } ?> />
            Oui
          </label>
        </td>
      </tr>

      <tr>
        <th><label for="error_logs_in_db">Logs d'erreur en base de donn�es</label></th>
        <td colspan="2">
          <label>
            <input type="radio" name="error_logs_in_db" value="0" id="error_logs_in_db_0"
              <?php if ($dPconfig['error_logs_in_db'] == "0") { echo 'checked'; } ?> />
            Non
          </label>
          <label>
            <input type="radio" name="error_logs_in_db" value="1" id="error_logs_in_db_1"
              <?php if ($dPconfig['error_logs_in_db'] == "1") { echo 'checked'; } ?> />
            Oui
          </label>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Base de donn�es principale</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th>
          <label for="db[std][dbtype]" title="Type de base de donn�es. Seul mysql est possible pour le moment">
            Type de base de donn�es :
          </label>
        </th>
        <td colspan="2">
          <input type="text" readonly="readonly" size="40" name="db[std][dbtype]"
                 value="<?php echo @$dPconfig["db"]["std"]["dbtype"]; ?>" />
        </td>
      </tr>

      <tr>
        <th><label for="db[std][dbhost]">Nom de l'h�te</label></th>
        <td colspan="2">
          <input type="text" size="40" name="db[std][dbhost]" value="<?php echo @$dPconfig["db"]["std"]["dbhost"]; ?>" />
        </td>
      </tr>

      <tr>
        <th><label for="db[std][dbname]">Nom de la base</label></th>
        <td colspan="2">
          <input type="text" size="40" name="db[std][dbname]" value="<?php echo @$dPconfig["db"]["std"]["dbname"]; ?>" />
        </td>
      </tr>

      <tr>
        <th><label for="db[std][dbuser]">Nom de l'utilisateur</label></th>
        <td colspan="2">
          <input type="text" size="40" name="db[std][dbuser]" value="<?php echo @$dPconfig["db"]["std"]["dbuser"]; ?>" />
        </td>
      </tr>

      <tr>
        <th><label for="db[std][dbpass]">Mot de passe</label></th>
        <td colspan="2">
          <input type="password" size="40" name="db[std][dbpass]" value="<?php echo @$dPconfig["db"]["std"]["dbpass"]; ?>" />
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>M�moire partag�e</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th>
          <label for="shared_memory" title="Choisir quelle extension doit g�rer la m�moire partag�e (celle-ci doit �tre install�e)">
            M�moire partag�e locale
          </label>
        </th>
        <td>
          <select id="shared_memory" name="shared_memory">
            <option value="none" <?php if ($dPconfig['shared_memory'] == 'none') { echo 'selected'; } ?> >Disque</option>

            <option value="apc"
              <?php if ($dPconfig['shared_memory'] == 'apc' ) {
                echo 'selected';
              }
              if (!extension_loaded('apc')) {
                echo 'disabled';
              } ?> >APC</option>

            <option value="apcu"
              <?php if ($dPconfig['shared_memory'] == 'apcu') {
                echo 'selected';
              }
              if (!extension_loaded('apcu')) {
                echo 'disabled';
              } ?> >APCu (PHP 5.5+)</option>
          </select>
        </td>
        <td>
          <div class="small-info">
            <?php require_once "includes/empty_shared_memory.php"; ?>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <label for="shared_memory_distributed" title="Choisir quelle extension doit g�rer la m�moire partag�e distribu�e">
            M�moire partag�e distribu�e
          </label>
        </th>
        <td>
          <select id="shared_memory_distributed" name="shared_memory_distributed" onchange="toggleMemoryParams(this.value)">
            <option value=""      <?php if ($dPconfig['shared_memory_distributed'] == ''     ) { echo 'selected'; } ?> >
              Aucune (utilise la m�moire partag�e locale)
            </option>
            <option value="redis" <?php if ($dPconfig['shared_memory_distributed'] == 'redis') { echo 'selected'; } ?> >
              Redis
            </option>
          </select>
        </td>
        <td>
        </td>
      </tr>

      <tr class="shared-memory-params params-redis">
        <td colspan="3">
          <div class="small-info">
            Sp�cifiez les adresses IP+port des serveurs du cluster Redis, s�par�s par des virgules.
            Par exemple <em>192.168.1.39:6379, 192.168.1.40:6379</em>
          </div>
        </td>
      </tr>

      <tr class="shared-memory-params params-redis">
        <th>
          <label for="shared_memory_params" title="Param�tres du gestionnaire de m�moire partag�e">
            Param�tres
          </label>
        </th>
        <td colspan="2">
          <input type="text" size="90" id="shared_memory_params" name="shared_memory_params"
                 value="<?php echo $dPconfig["shared_memory_params"]; ?>" />
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Sessions</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th>
          <label for="session_handler" title="Choisir quel mode de gestion de sessions utiliser (celui-ci doit �tre install�)">
            Gestionnaire de sessions
          </label>
        </th>
        <td>
          <select id="session_handler" name="session_handler" onchange="toggleSessionMutex(this.value)">
            <option value="files" <?php if ($dPconfig['session_handler'] == 'files') { echo 'selected'; } ?>>
              Fichiers
            </option>
            <option value="mysql"  <?php if ($dPconfig['session_handler'] == 'mysql' || $dPconfig['session_handler'] == 'zebra') { echo 'selected'; } ?>>
              MySQL (Utile pour les environnements r�pliqu�s)
            </option>
          </select>
        </td>
        <td class="text">
          <div class="small-warning">
            Le changement de ce param�tre <strong>mettra fin � toutes les session des utilisateurs actuellement connect�s</strong>.
            <br />
            Si vous choisissez le mode Memcache, veuillez vous assurer que le serveur est correctement configur�.
          </div>
        </td>
      </tr>

      <tr class="session-mutex session-mutex-mysql">
        <th>
          <label for="session_handler_mutex_type" title="Mode de mutex pour les sessions, seulement pour MySQL">
            Mode mutex des sessions de mode MySQL
          </label>
        </th>
        <td colspan="2">
          <select name="session_handler_mutex_type">
            <option value=""       <?php if ($dPconfig['session_handler_mutex_type'] == ''      ) { echo 'selected'; } ?> >Par d�faut (GET_LOCK MySQL)</option>
            <option value="files"  <?php if ($dPconfig['session_handler_mutex_type'] == 'files' ) { echo 'selected'; } ?> >Fichiers</option>
            <option value="system" <?php if ($dPconfig['session_handler_mutex_type'] == 'system') { echo 'selected'; } ?> >Syst�me (Mutex syst�me: Redis, etc)</option>
          </select>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Drivers de mutex</legend>

    <table class="form">
      <col style="width: 15%" />

      <?php foreach ($mutex_drivers as $_driver_class => $_values) { ?>
        <tr>
          <th class="narrow">
            <?php echo $_values["title"]; ?>
          </th>
          <td class="narrow">
            <label>
              <input type="radio" name="mutex_drivers[<?php echo $_driver_class; ?>]" value="1"
                <?php if (!empty($dPconfig['mutex_drivers'][$_driver_class])) { echo 'checked'; } ?> />
              Oui
            </label>

            <label>
              <input type="radio" name="mutex_drivers[<?php echo $_driver_class; ?>]" value="0"
                <?php if (empty($dPconfig['mutex_drivers'][$_driver_class])) { echo 'checked'; } ?> />
              Non
            </label>
          </td>
          <th>
            <label for="mutex_drivers_params[<?php echo $_driver_class; ?>]">
              <?php echo $_values["params"]; ?>
            </label>
          </th>
          <td>
            <?php if ($_values["params"]) { ?>
              <input type="text" name="mutex_drivers_params[<?php echo $_driver_class; ?>]" size="70"
                     id="mutex_drivers_params[<?php echo $_driver_class; ?>]"
                     value="<?php echo $dPconfig["mutex_drivers_params"][$_driver_class]; ?>" />
            <?php } ?>
          </td>
        </tr>
      <?php } ?>

    </table>
  </fieldset>

  <fieldset>
    <legend>S�curit�</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th>
          <label for="master_key_filepath" title="Emplacement de la clef Mediboard">
            R�pertoire clef (optionel)
          </label>
        </th>
        <td colspan="2">
          <input type="text" size="40" name="master_key_filepath" value="<?php echo $dPconfig['master_key_filepath'] ?>" />
        </td>
      </tr>
      <tr>
        <td colspan="3">
          <div class="small-warning">
            <p>
              Ce param�tre n'est pas obligatoire, il est utilis� pour le chiffrage des mots de
              passe li�s � des sources de connexion SMTP, POP ou FTP.
            </p>

            <p>
              Ce r�pertoire doit �tre <strong>pr�alablement cr��</strong>, apache ne disposant g�n�ralement pas des droits
              suffisants pour le faire.
            </p>

            <p>
              Veillez � ce que l'utilisateur apache ait les droits d'�criture dans ce dossier.
            </p>

            <p style="font-weight: bold; padding: 1em;">
              Ce fichier NE DOIT PAS se situer dans un r�pertoire accessible depuis le Web !<br />
              Veillez � le sauvegarder.
            </p>

            <p>
              Un script de mise en place vous est fourni :<br />
            <pre>php &lt;r�pertoire mediboard&gt;/cli/genMasterKey.php -d $r�pertoire clef$ -g $groupe apache$</pre>
            <p>
          </div>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Mode maintenance</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th><label for="offline">Mode maintenance</label></th>
        <td colspan="2">
          <input type="radio" name="offline" value="0" id="offline_0" <?php if ($dPconfig['offline'] == "0") { echo 'checked'; } ?> />
          <label for="offline_0">Non</label>
          <input type="radio" name="offline" value="1" id="offline_1" <?php if ($dPconfig['offline'] == "1") { echo 'checked'; } ?> />
          <label for="offline_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th><label for="offline_non_admin">Mode maintenance accessible aux admins</label></th>
        <td colspan="2">
          <input type="radio" name="offline_non_admin" value="0" id="offline_non_admin_0" <?php if ($dPconfig['offline_non_admin'] == "0") { echo 'checked'; } ?> />
          <label for="offline_non_admin_0">Non</label>
          <input type="radio" name="offline_non_admin" value="1" id="offline_non_admin_1" <?php if ($dPconfig['offline_non_admin'] == "1") { echo 'checked'; } ?> />
          <label for="offline_non_admin_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th>
          <label for="migration[active]" title="Affiche une page avec les nouvelles adresses de Mediboard aux utilisateurs">
            Mode migration
          </label>
        </th>
        <td colspan="2">
          <input type="radio" name="migration[active]" value="0" id="migration[active]_0"
            <?php if ($dPconfig['migration']['active'] == "0") { echo 'checked'; } ?> />
          <label for="migration[active]_0">Non</label>
          <input type="radio" name="migration[active]" value="1" id="migration[active]_1"
            <?php if ($dPconfig['migration']['active'] == "1") { echo 'checked'; } ?> />
          <label for="migration[active]_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th>
          <label for="offline_time_start" title="Heure � partir de laquelle Mediboard sera hors ligne">
            Heure d�but mode maintenance
          </label>
        </th>
        <td colspan="2">
          <input type="time" name="offline_time_start" id="offline_time_start"
                 value="<?php echo $dPconfig['offline_time_start']; ?>" />
        </td>
      </tr>

      <tr>
        <th>
          <label for="offline_time_end" title="Heure jusqu� laquelle Mediboard sera hors ligne">
            Heure fin mode maintenance
          </label>
        </th>
        <td colspan="2">
          <input type="time" name="offline_time_end" id="offline_time_end"
                 value="<?php echo $dPconfig['offline_time_end']; ?>" />
        </td>
      </tr>
    </table>
  </fieldset>

  <table class="form">
    <tr>
      <td class="button"><button class="submit" type="submit">Valider la configuration</button></td>
    </tr>
  </table>

</form>

<?php showFooter(); ?>