{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main layout">
  <tr>
    <td class="halfPane"><fieldSet>
        <legend>Infos patient</legend>
        <div class="text" id="infoPat">
          <div class="empty">Aucun patient s�lectionn�</div>
        </div>
      </fieldSet>
    </td>
    <td>
      {{if $consult->_id}}
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}{{$object_consult->_class}}{{/tr}}</legend>
          <div id="documents">
            <script type="text/javascript">
              Document.register('{{$object_consult->_id}}','{{$object_consult->_class}}','{{$consult->_praticien_id}}','documents');
            </script>
          </div>
        </fieldset>
        <fieldset>
          <legend>{{tr}}CFile{{/tr}} - {{tr}}{{$consult->_class}}{{/tr}}</legend>
          <div id="files">
            <script type="text/javascript">
              File.register('{{$consult->_id}}','{{$consult->_class}}', 'files');
            </script>
          </div>
        </fieldset>
        <fieldset>
          <legend>{{tr}}CDevisCodage{{/tr}}</legend>
          {{mb_script module=ccam script=DevisCodage ajax=1}}
          <script>
            Main.add(function() {
              DevisCodage.list('{{$consult->_class}}', '{{$consult->_id}}');
            });
          </script>
          <div id="view-devis"></div>
        </fieldset>
      {{/if}}
    </td>
  </tr>
</table>

