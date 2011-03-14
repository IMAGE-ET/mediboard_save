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
        {{mb_include module=system template=CExchangeSource_inc_config mod=ftp class="CSourceFTP"}}  
      {{/if}}
    </div>
    
    <div id="CSourceSOAP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceSOAP)}}block{{else}}none{{/if}};">
      {{if !"webservices"|module_active}}
        {{mb_include module=system template=module_missing mod=webservices}}  
      {{else}}
        {{mb_include module=system template=CExchangeSource_inc_config mod=webservices class="CSourceSOAP"}}  
      {{/if}}
    </div>
    
    <div id="CSourceSMTP-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceSMTP)}}block{{else}}none{{/if}};">
      {{mb_include module=system template=CExchangeSource_inc_config mod=system class="CSourceSMTP"}}  
    </div>
    
    <div id="CSourceFileSystem-{{$sourcename}}" style="display:{{if !$source->_allowed_instances && ($source instanceof CSourceFileSystem)}}block{{else}}none{{/if}};">
      {{if $source instanceof CSourceFileSystem && !$source->active}}
      <div class="small-info">
        {{tr}}CExchangeSource-not-active{{/tr}}
      </div>
      {{/if}}
      {{mb_include module=system template=CExchangeSource_inc_config mod=system class="CSourceFileSystem"}}  
    </div>
  </div>
{{/if}}