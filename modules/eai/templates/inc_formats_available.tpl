{{*
 * Formats available
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if !empty($formats_tabular|smarty:nodefaults)}}
<fieldset>
  <legend>{{tr}}CExchangeTabular{{/tr}}</legend>
  
  {{foreach from=$formats_tabular item=_format_tabular}}
    <button class="none" onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_tabular->_class_name}}')">
      <img src="modules/{{$_format_tabular->_ref_module->mod_name}}/images/icon.png" width="16"/> {{tr}}{{$_format_tabular->_class_name}}{{/tr}}
    </button>
  {{/foreach}}
  
  {{if !empty($messages_tabular|smarty:nodefaults)}}
    {{foreach from=$messages_tabular key=_message_tabular item=_messages_tabular_supported}}
      {{mb_include template="inc_messages_available" message=$_message_tabular messages_supported=$_messages_tabular_supported}}
    {{/foreach}}
  {{else}}
    <div class="small-warning">{{tr}}CMessageSupported.none{{/tr}}</div>
  {{/if}}
</fieldset>
{{/if}}

{{if !empty($formats_xml|smarty:nodefaults)}}
<fieldset> 
  <legend>{{tr}}CEchangeXML{{/tr}}</legend>
  
  {{foreach from=$formats_xml item=_format_xml}}
    <button onclick="InteropActor.viewMessagesSupported('{{$actor_guid}}', '{{$_format_xml->_class_name}}')">
      <img src="modules/{{$_format_xml->_ref_module->mod_name}}/images/icon.png" width="16"/>{{tr}}{{$_format_xml->_class_name}}{{/tr}}
    </button>
  {{/foreach}}
  
  {{if !empty($messages_xml|smarty:nodefaults)}}
    {{foreach from=$messages_xml key=_message_xml item=_messages_xml_supported}}
      {{mb_include template="inc_messages_available" message=$_message_xml messages_supported=$_messages_xml_supported}}
    {{/foreach}}
  {{else}}
    <div class="small-warning">{{tr}}CMessageSupported.none{{/tr}}</div>
  {{/if}}
</fieldset>
{{/if}}