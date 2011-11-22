{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceMLLP-{{$source->name}}" action="?m={{$m}}" method="post" 
        onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="hl7" />
        <input type="hidden" name="dosql" value="do_source_mll_aed" />
        <input type="hidden" name="source_soap_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$source->name}}" />  
        
        <table class="form">
          <tr>
            <th class="category" colspan="100">
              {{tr}}config-source-mllp{{/tr}}
            </th>
          </tr>
          {{mb_include module=system template=CExchangeSource_inc}}
          <tr>
            <th>{{mb_label object=$source field="port"}}</th>
            <td>{{mb_field object=$source field="port"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:1, typeName:'',objName:'{{$source->_view|smarty:nodefaults|JSAttribute}}', onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}  
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="greedyPane">
      <script type="text/javascript">
        MLLP = {
          connexion: function (exchange_source_name) {
            var url = new Url("hl7", "ajax_connexion_mllp");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-mllp-connexion-" + exchange_source_name);
          },
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-mllp{{/tr}}
          </th>
        </tr>
        
        <!-- Test connexion MLLP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="MLLP.connexion('{{$source->name}}');">
              {{tr}}utilities-source-mllp-connexion{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td id="utilities-source-mllp-connexion-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>