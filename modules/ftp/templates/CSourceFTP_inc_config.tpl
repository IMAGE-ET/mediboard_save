{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ftp    script=action_ftp      ajax=true}}

{{mb_default var=light value=""}}

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceFTP-{{$source->name}}" action="?m={{$m}}" method="post"
          onsubmit="return onSubmitFormAjax(this, { onComplete : (function() {
          if (this.up('.modal')) {
            Control.Modal.close();
          } else {
            ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
          }}).bind(this)})">

        <input type="hidden" name="m" value="ftp" />
        <input type="hidden" name="dosql" value="do_source_ftp_aed" />
        <input type="hidden" name="source_ftp_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$source->name}}" />

        <fieldset>
          <legend>{{tr}}CSourceFTP{{/tr}}</legend>

          <table class="form">
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <th style="width: 120px">{{mb_label object=$source field="user"}}</th>
              <td>{{mb_field object=$source field="user" size="50"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="password"}}</th>
              {{assign var=placeholder value="Pas de mot de passe"}}
              {{if $source->password}}
                {{assign var=placeholder value="Mot de passe enregistr�"}}
              {{/if}}
              <td>{{mb_field object=$source field="password" placeholder=$placeholder size="30"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="port"}}</th>
              <td>{{mb_field object=$source field="port"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="timeout"}}</th>
              <td>{{mb_field object=$source field="timeout" register=true increment=true form="editSourceFTP-`$source->name`" size=3 step=1 min=0}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="ssl"}}</th>
              <td>{{mb_field object=$source field="ssl"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="pasv"}}</th>
              <td>{{mb_field object=$source field="pasv"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="mode"}}</th>
              <td>{{mb_field object=$source field="mode" typeEnum="radio"}}</td>
            </tr>
          </table>
        </fieldset>

        <fieldset>
          <legend>{{tr}}CSourceFTP-manage_files{{/tr}}</legend>

          <table class="main form">
            <tr>
              <th style="width: 120px">{{mb_label object=$source field="counter"}}</th>
              <td>{{mb_field object=$source field="counter"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="fileprefix"}}</th>
              <td>{{mb_field object=$source field="fileprefix"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="fileextension"}}</th>
              <td>{{mb_field object=$source field="fileextension"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="filenbroll"}}</th>
              <td>{{mb_field object=$source field="filenbroll" typeEnum="radio"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="fileextension_write_end"}}</th>
              <td>{{mb_field object=$source field="fileextension_write_end"}}</td>
            </tr>
          </table>
        </fieldset>

        <table class="main form">
          <tr>
            <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

                <button class="trash" type="button" onclick="confirmDeletion(this.form,
                  { ajax: 1, typeName: '', objName: '{{$source->_view}}'},
                  { onComplete: (function() {
                  if (this.up('.modal')) {
                    Control.Modal.close();
                  } else {
                    ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
                  }}).bind(this.form)})">

                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>

        {{if !$light}}
        <fieldset>
          <legend>{{tr}}utilities-source-ftp{{/tr}}</legend>

          <table class="main form">
            <tr>
              <td class="button">
                <!-- Test connexion FTP -->
                <button type="button" class="search" onclick="FTP.connexion('{{$source->name}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-ftp-connexion{{/tr}}
                </button>

                <!-- Liste des fichiers -->
                <button type="button" class="list" onclick="FTP.getFiles('{{$source->name}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-ftp-getFiles{{/tr}}
                </button>

                <button type="button" class="lookup" onclick="ExchangeSource.manageFiles('{{$source->_guid}}');"
                        {{if !$source->_id}}disabled{{/if}}>
                  {{tr}}utilities-source-ftp-manageFiles{{/tr}}
                </button>
              </td>
            </tr>
          </table>
        </fieldset>
        {{/if}}
      </form>
    </td>
  </tr>
</table>