{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceSoap" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_soap_aed" />
        <input type="hidden" name="source_soap_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
        <input type="hidden" name="name" value="{{$source->name}}" />  
        
        <table class="form">
          <tr>
            <th class="category" colspan="100">
              {{tr}}config-source-soap{{/tr}}
            </th>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="name"}}</th>
            <td><input type="text" readonly="readonly" name="name" value="{{$source->name}}" /></td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="role"}}</th>
            <td>{{mb_field object=$source field="role"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="host"}}</th>
            <td>{{mb_field object=$source field="host"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="user"}}</th>
            <td>{{mb_field object=$source field="user"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="password"}}</th>
            <td>{{mb_field object=$source field="password"}}</td>
          </tr>
					<tr>
            <th>{{mb_label object=$source field="type_echange"}}</th>
            <td>{{mb_field object=$source field="type_echange"}}</td>
          </tr>
					<tr>
            <th>{{mb_label object=$source field="evenement_name"}}</th>
            <td>{{mb_field object=$source field="evenement_name"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="single_parameter"}}</th>
            <td>{{mb_field object=$source field="single_parameter"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="wsdl_mode"}}</th>
            <td>{{mb_field object=$source field="wsdl_mode"}}</td>
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
        SOAP = {
          connexion: function (exchange_source_name) {
            var url = new Url("webservices", "ajax_connexion_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-connexion-" + exchange_source_name);
          },
          
          getFunctions: function (exchange_source_name) {
            var url = new Url("webservices", "ajax_getFunctions_soap");
            url.addParam("exchange_source_name", exchange_source_name);
            url.requestUpdate("utilities-source-soap-getFunctions-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-soap{{/tr}}
          </th>
        </tr>
        
        <!-- Test connexion SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.connexion('{{$source->name}}');">
              {{tr}}utilities-source-soap-connexion{{/tr}}
            </button>
          </td>
			  </tr>
				<tr>
          <td id="utilities-source-soap-connexion-{{$source->name}}" class="text"></td>
        </tr>
        
        <!-- Liste des functions SOAP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SOAP.getFunctions('{{$source->name}}');">
              {{tr}}utilities-source-soap-getFunctions{{/tr}}
            </button> 
          </td>
				</tr>
        <tr>
          <td id="utilities-source-soap-getFunctions-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>