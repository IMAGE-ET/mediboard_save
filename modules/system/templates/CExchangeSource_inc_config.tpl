{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $source->_id && ($source->_class != $class)}}
  <div class="small-info">
    {{tr}}CExchangeSource-already-exist{{/tr}}
  </div>
{{else}}
<table class="main">
  {{assign var="_source" value=$source}}
  {{if $source->_class == "CExchangeSource"}}
    {{assign var="_source" value=$source->_allowed_instances.$class}}
  {{/if}}
  {{if !$source->_id}}
  <tr>
    <td class="halfPane">
      <a class="button new" onclick="$('config-source-{{$class}}-{{$sourcename}}').show(); Control.Modal.position();">
        {{tr}}{{$class}}-title-create{{/tr}}
      </a>
   </td>
  </tr>
  {{/if}}
  <tr>
    <td id="config-source-{{$class}}-{{$sourcename}}" {{if !$source->_id}}style="display:none"{{/if}}>
      {{if $_source->_class == $class}}
        {{mb_include module=$mod template="`$class`_inc_config" source=$_source}}  
      {{/if}}     
    </td>
  </tr>
</table> 
{{/if}}