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

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;

showHeader();

?>

<script>
  /**
   * @param {NodeList,Array} list
   * @returns {array}
   */
  $A = function(list) {
    return Array.prototype.slice.call(list);
  };

  /**
   * @param {String} selector
   * @returns {NodeList}
   */
  $$ = function(selector) {
    console.log(selector);
    return $A(document.querySelectorAll(selector));
  };

  /**
   *
   * @param {HTMLElement} element
   */
  hideElement = function(element) {
    element.style.display = "none";
  };

  /**
   *
   * @param {HTMLElement} element
   */
  showElement = function(element) {
    element.style.display = "";
  };

  /**
   * @param {HTMLElement} element
   * @param {Boolean}     visible
   */
  setVisibleElement = function(element, visible) {
    visible ? showElement(element) : hideElement(element);
  };

  /**
   * @param {NodeList,Array} list
   * @param {Function}       callback
   */
  each = function(list, callback) {
    list = $A(list);
    for (var i = 0, l = list.length; i < l; i++) {
      callback(list[i]);
    }
    return list;
  };

  toggleMemoryParams = function(value) {
    each($$('.shared-memory-params'), hideElement);
    each($$('.params-'+value), showElement);
  };

  window.onload = function(){
    toggleMemoryParams($$('#shared_memory')[0].value);
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
            <option value="prod"   <?php if ($dPconfig['instance_role'] == 'prod'  ) echo 'selected="1"'; ?> >Production</option>
            <option value="qualif" <?php if ($dPconfig['instance_role'] == 'qualif') echo 'selected="1"'; ?> >Qualification</option>
          </select>
        </td>
      </tr>

      <tr>
        <th>
          <label for="session_handler" title="Choisir quel mode de gestion de sessions utiliser (celui-ci doit �tre install�)">
            Gestionnaire de sessions
          </label>
        </th>
        <td>
          <select name="session_handler">
            <option value="files"    <?php if ($dPconfig['session_handler'] == 'files'   ) { echo 'selected="selected"'; } ?> >Fichiers</option>
            <option value="memcache" <?php if ($dPconfig['session_handler'] == 'memcache') { echo 'selected="selected"'; } ?> >Memcache (d�conseill�)</option>
            <option value="mysql"    <?php if ($dPconfig['session_handler'] == 'mysql'   ) { echo 'selected="selected"'; } ?> >MySQL (Utile pour les environnements r�pliqu�s)</option>
            <option value="zebra"    <?php if ($dPconfig['session_handler'] == 'zebra'   ) { echo 'selected="selected"'; } ?> >Zebra (Utile pour les environnements r�pliqu�s)</option>
          </select>
        </td>
        <td class="text">
          <div class="small-warning">
            Le changement de ce param�tre <strong>mettra fin � toutes les session des utilisateurs actuellement connect�s</strong>.<br />
            Si vous choisissez le mode Memcache, veuillez vous assurer que le serveur est correctement configur�.
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <label for="http_redirections" title="Active les redirections http d�finies dans Mediboard">
            Redirections http actives
          </label>
        </th>
        <td colspan="2">
          <input type="radio" name="http_redirections" value="0" id="http_redirections_0" <?php if ($dPconfig['http_redirections'] == "0") echo 'checked="1"'; ?> />
          <label for="http_redirections_0">Non</label>

          <input type="radio" name="http_redirections" value="1" id="http_redirections_1" <?php if ($dPconfig['http_redirections'] == "1") echo 'checked="1"'; ?> />
          <label for="http_redirections_1">Oui</label>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Base de donn�es principale</legend>

    <table class="form">
      <col style="width: 25%" />

      <tr>
        <th><label for="db[std][dbtype]" title="Type de base de donn�es. Seul mysql est possible pour le moment">Type de base de donn�es :</label></th>
        <td colspan="2"><input type="text" readonly="readonly" size="40" name="db[std][dbtype]" value="<?php echo @$dPconfig["db"]["std"]["dbtype"]; ?>" /></td>
      </tr>

      <tr>
        <th><label for="db[std][dbhost]">Nom de l'h�te</label></th>
        <td colspan="2"><input type="text" size="40" name="db[std][dbhost]" value="<?php echo @$dPconfig["db"]["std"]["dbhost"]; ?>" /></td>
      </tr>

      <tr>
        <th><label for="db[std][dbname]">Nom de la base</label></th>
        <td colspan="2"><input type="text" size="40" name="db[std][dbname]" value="<?php echo @$dPconfig["db"]["std"]["dbname"]; ?>" /></td>
      </tr>

      <tr>
        <th><label for="db[std][dbuser]">Nom de l'utilisateur</label></th>
        <td colspan="2"><input type="text" size="40" name="db[std][dbuser]" value="<?php echo @$dPconfig["db"]["std"]["dbuser"]; ?>" /></td>
      </tr>

      <tr>
        <th><label for="db[std][dbpass]">Mot de passe</label></th>
        <td colspan="2"><input type="password" size="40" name="db[std][dbpass]" value="<?php echo @$dPconfig["db"]["std"]["dbpass"]; ?>" /></td>
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
            M�moire partag�e
          </label>
        </th>
        <td>
          <select id="shared_memory" name="shared_memory" onchange="toggleMemoryParams(this.value)">
            <option value="none"      <?php if ($dPconfig['shared_memory'] == 'none'      ) { echo 'selected="selected"'; } ?> >Disque</option>
            <option value="apc"       <?php if ($dPconfig['shared_memory'] == 'apc'       ) { echo 'selected="selected"'; } ?> >APC</option>
            <option value="redis"     <?php if ($dPconfig['shared_memory'] == 'redis'     ) { echo 'selected="selected"'; } ?> >Redis (Exp�rimental)</option>
            <option value="memcached" <?php if ($dPconfig['shared_memory'] == 'memcached' ) { echo 'selected="selected"'; } ?> >Memcached (Exp�rimental)</option>
          </select>
        </td>
        <td>
          <div class="small-info">
            <?php require_once "includes/empty_shared_memory.php"; ?>
          </div>
        </td>
      </tr>

      <tr class="shared-memory-params params-redis">
        <td colspan="3">
          <div class="small-info">
            Sp�cifiez l'adresse IP+port du serveur Redis. Par exemple <em>127.0.0.1:6379</em>
          </div>
        </td>
      </tr>

      <tr class="shared-memory-params params-memcached">
        <td colspan="3">
          <div class="small-info">
            Sp�cifiez les adresses IP+port des serveurs du cluster Memcached, s�par�s par des virgules. Par exemple <em>192.168.1.39:11211, 192.168.1.40:11211</em>
          </div>
        </td>
      </tr>

      <tr class="shared-memory-params params-memcached params-redis">
        <th>
          <label for="shared_memory_params" title="Param�tres du gestionnaire de m�moire partag�e">
            Param�tres
          </label>
        </th>
        <td colspan="2">
          <input type="text" size="90" id="shared_memory_params" name="shared_memory_params" value="<?php echo $dPconfig["shared_memory_params"]; ?>" />
        </td>
      </tr>
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
              passe li�s � des source de connexion SMTP, POP ou FTP.
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
          <input type="radio" name="offline" value="0" id="offline_0" <?php if ($dPconfig['offline'] == "0") echo 'checked="1"'; ?> />
          <label for="offline_0">Non</label>
          <input type="radio" name="offline" value="1" id="offline_1" <?php if ($dPconfig['offline'] == "1") echo 'checked="1"'; ?> />
          <label for="offline_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th><label for="offline_non_admin">Mode maintenance accessible aux admins</label></th>
        <td colspan="2">
          <input type="radio" name="offline_non_admin" value="0" id="offline_non_admin_0" <?php if ($dPconfig['offline_non_admin'] == "0") echo 'checked="1"'; ?> />
          <label for="offline_non_admin_0">Non</label>
          <input type="radio" name="offline_non_admin" value="1" id="offline_non_admin_1" <?php if ($dPconfig['offline_non_admin'] == "1") echo 'checked="1"'; ?> />
          <label for="offline_non_admin_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th><label for="migration[active]" title="Affiche une page avec les nouvelles adresse de Mediboard aux utilisateurs">Mode migration</label></th>
        <td colspan="2">
          <input type="radio" name="migration[active]" value="0" id="migration[active]_0" <?php if ($dPconfig['migration']['active'] == "0") echo 'checked="1"'; ?> />
          <label for="migration[active]_0">Non</label>
          <input type="radio" name="migration[active]" value="1" id="migration[active]_1" <?php if ($dPconfig['migration']['active'] == "1") echo 'checked="1"'; ?> />
          <label for="migration[active]_1">Oui</label>
        </td>
      </tr>

      <tr>
        <th><label for="offline_time_start" title="Heure � partir de laquelle Mediboard sera hors ligne">Heure d�but mode maintenance</label></th>
        <td colspan="2">
          <input type="time" name="offline_time_start" id="offline_time_start" <?php if ($dPconfig['offline_time_start']) echo "value='".$dPconfig['offline_time_start']."'"; ?> />
        </td>
      </tr>

      <tr>
        <th><label for="offline_time_end" title="Heure jusqu� laquelle Mediboard sera hors ligne">Heure fin mode maintenance</label></th>
        <td colspan="2">
          <input type="time" name="offline_time_end" id="offline_time_end" <?php if ($dPconfig['offline_time_end']) echo "value='".$dPconfig['offline_time_end']."'"; ?> />
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