{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <col style="width: 50%" />
    <tr>
      <th class="category" colspan="2">
        Int�gration de l'IDE
      </th>
    </tr>

    <tr>
      <td colspan="2">
        <div class="small-info">
          Les IDE/�diteurs suivants sont officiellement support�s:
          <ul>
            <li>Notepad++</li>
            <li>PhpStorm</li>
            <li>Sublime Text</li>
          </ul>
        </div>

        Afin de permettre l'ouverture de l'IDE depuis Mediboard, il faut suivre les �tapes suivantes:

        <ol>
          <li>Ajouter le chemin vers l'�x�cutable de <code>PHP</code> dans le PATH si ce n'est pas d�j� le cas.</li>
          <li>Enregistrer le chemin vers Mediboard dans la variable d'environnement <code>MEDIBOARD_PATH</code> (<code>{{$path}}</code>)</li>
          <li>Sp�cifier ci-dessous le chemin vers l'�xecutable de l'IDE (ou le nom de l'�x�cutable s'il est dans le PATH)</li>
          <li>
            <strong>Sous Windows</strong>:
            <ol>
              <li>
                Executer le fichier
                <code><a href="dev/register_protocol/register_ide.reg" target="_blank" download>register_ide.reg</a></code>
              </li>
              <li>Red�marrer</li>
            </ol>
          </li>
          <li>
            <strong>Sous Linux</strong>:
            <div class="small-warning">
              En cours de d�veloppement.
            </div>
            {{*
            <ol>
              <li>
                Ex�cuter les lignes suivantes:
                <pre>
gconftool-2 -t string -s /desktop/gnome/url-handlers/ide/command 'php $MEDIBOARD_PATH/dev/ide.php "%s"'
gconftool-2 -s /desktop/gnome/url-handlers/ide/needs_terminal false -t bool
gconftool-2 -s /desktop/gnome/url-handlers/ide/enabled true -t bool
                </pre>
              </li>
            </ol>
*}}
          </li>
          <li>
            <strong>Sous MacOS</strong>:
            <!-- https://support.shotgunsoftware.com/entries/127152 -->
            <div class="small-warning">
              En cours de d�veloppement.
            </div>
          </li>
        </ol>
      </td>
    </tr>

    {{mb_include module=system template=inc_config_str var=ide_path size=70}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
