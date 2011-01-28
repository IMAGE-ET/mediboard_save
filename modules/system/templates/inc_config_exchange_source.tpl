{{* $Id: vw_idx_dest_hprim.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="sourcename" value=$source->name}}

{{if $source->_incompatible}} 
<div class="small-error">
  {{tr}}CExchangeSource-_incompatible{{/tr}} <strong>({{tr}}config-instance_role-{{$conf.instance_role}}{{/tr}})</strong>
</div>
{{/if}}

{{if !$sourcename}} 
  <div class="small-info">
    {{tr}}CExchangeSource-no-name{{/tr}}
  </div>
{{else}}
  <div id="exchange_source-{{$sourcename}}">
    <script type="text/javascript">
      refreshExchangeSource = function(exchange_source_name, type){
        var url = new Url("system", "ajax_refresh_exchange_source");
        url.addParam("exchange_source_name", exchange_source_name);
        url.addParam("type", type);
        url.requestUpdate('exchange_source-'+exchange_source_name);
      }
    </script>
    
    {{if $source->_allowed_instances}} 
    <script type="text/javascript">
	    Main.add(function () {
	      Control.Tabs.create('tabs-exchange-source-{{$sourcename}}', true);
	    });
    </script>
    
    <ul id="tabs-exchange-source-{{$sourcename}}" class="control_tabs">
      {{foreach from=$source->_allowed_instances item=_source_allowed}}
      <li><a href="#{{$_source_allowed->_class_name}}-{{$sourcename}}">{{tr}}{{$_source_allowed->_class_name}}{{/tr}}</a></li>
     {{/foreach}}
    </ul>
      
    <hr class="control_tabs" />
    {{/if}} 
    
    <div id="CSourceFTP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceFTP)}}block{{else}}none{{/if}};">
      {{if !"ftp"|module_active}}
        {{mb_include module=system template=module_missing mod=ftp}}  
      {{else}}
        {{if $source->_id && !($source instanceof CSourceFTP)}}
          <div class="small-info">
            {{tr}}CExchangeSource-already-exist{{/tr}}
          </div>
        {{else}}
        <table class="main">
          {{assign var="_source_ftp" value=$source}}
          {{if $source->_class_name == "CExchangeSource"}}
            {{assign var="_source_ftp" value=$source->_allowed_instances.CSourceFTP}}
          {{/if}}
          {{if !$source->_id}}
          <tr>
            <td class="halfPane">
              <a class="button new" onclick="$('config-source-ftp-{{$sourcename}}').show()">
                Cr�er une source FTP
              </a>
           </td>
          </tr>
          {{/if}}
          <tr>
            <td id="config-source-ftp-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
              {{if $_source_ftp instanceof CSourceFTP}}
                {{mb_include module=ftp template=inc_config_source_ftp source=$_source_ftp}}  
              {{/if}}     
            </td>
          </tr>
        </table> 
        {{/if}}
      {{/if}}
    </div>
    
    <div id="CSourceSOAP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceSOAP)}}block{{else}}none{{/if}};">
      {{if !"webservices"|module_active}}
        {{mb_include module=system template=module_missing mod=webservices}}  
      {{else}}
        {{if $source->_id && !($source instanceof CSourceSOAP)}}
          <div class="small-info">
            {{tr}}CExchangeSource-already-exist{{/tr}}
          </div>
        {{else}}
        <table class="main source">
          {{assign var="_source_soap" value=$source}}
          {{if $source->_class_name == "CExchangeSource"}}
            {{assign var="_source_soap" value=$source->_allowed_instances.CSourceSOAP}}
          {{/if}}
          {{if !$source->_id}}
          <tr>
            <td class="halfPane">
              <a class="button new" onclick="$('config-source-soap-{{$sourcename}}').show()">
                Cr�er une source SOAP
              </a> 
           </td>
          </tr>
          {{/if}}
          <tr>
            <td id="config-source-soap-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
              {{if $_source_soap instanceof CSourceSOAP}}
                {{mb_include module=webservices template=inc_config_source_soap source=$_source_soap}}
              {{/if}}            
            </td>
          </tr>
        </table>
        {{/if}}
      {{/if}}
    </div>
    
    <div id="CSourceSMTP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceSMTP)}}block{{else}}none{{/if}};">
      {{if $source->_id && !($source instanceof CSourceSMTP)}}
        <div class="small-info">
          {{tr}}CExchangeSource-already-exist{{/tr}}
        </div>
      {{else}}
      <table class="main">
        {{assign var="_source_smtp" value=$source}}
        {{if $source->_class_name == "CExchangeSource"}}
          {{assign var="_source_smtp" value=$source->_allowed_instances.CSourceSMTP}}
        {{/if}}
        {{if !$source->_id}}
        <tr>
          <td class="halfPane">
            <a class="button new" onclick="$('config-source-smtp-{{$sourcename}}').show()">
              Cr�er une source SMTP
            </a>
         </td>
        </tr>
        {{/if}}
        <tr>
          <td id="config-source-smtp-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
            {{if $_source_smtp instanceof CSourceSMTP}}
              {{mb_include module=system template=inc_config_source_smtp source=$_source_smtp}}  
            {{/if}}     
          </td>
        </tr>
      </table> 
      {{/if}}
    </div>
  </div>
{{/if}}