{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigExport" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}-export{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_str var=export_segment}}
    
    {{mb_include module=system template=inc_config_str var=export_id_min}}
    
    {{mb_include module=system template=inc_config_str var=export_id_max}}
    
    {{mb_include module=system template=inc_config_str var=export_date_min}}
    
    {{mb_include module=system template=inc_config_str var=export_date_max}}
    
    {{mb_include module=system template=inc_config_str var=batch_count}}
        
    {{mb_include module=system template=inc_config_bool var=sej_no_numdos}}
    
    {{mb_include module=system template=inc_config_bool var=send_sej_pa}}
    
    {{mb_include module=system template=inc_config_bool var=send_mvt}}
    
    {{assign var=var    value="export_dest"}}
    {{assign var=field  value="$m[$var]"}}
    {{assign var=value  value=$conf.$m.$var}}
    {{assign var=locale value=config-$m-$var}}
    <tr>
      <th>
        <label for="{{$field}}" title="{{tr}}{{$locale}}-desc{{/tr}}">
          {{tr}}{{$locale}}{{/tr}}
        </label>  
      </th>
    
      <td>
        <select class="str" name="{{$field}}">
          {{foreach from=$receivers item=_receiver}}
            <option value="{{$_receiver->_id}}" {{if $value == $_receiver->_id}} selected="selected"{{/if}}>
              {{$_receiver}}
           </option>
          {{/foreach}}
        </select> 
      </td>
    </tr>
    
              
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_script module="smp" script="action"}}

<table class="tbl">
  <tr>
    <th class="title" colspan="100">{{tr}}smp-export-classes{{/tr}}</th>
  </tr>
  <tr>
    <td class="narrow">
      {{tr}}sip-export-class{{/tr}} '{{tr}}CSejour{{/tr}}'
    </td>
    <td>
      <button type="button" class="new" onclick="Action.doExport('start', 'sejour')">
        {{tr}}Start{{/tr}}      
      </button>
      <button type="button" class="change" onclick="Action.doExport('retry', 'sejour')">
        {{tr}}Retry{{/tr}}      
      </button>
      <button type="button" class="tick" onclick="Action.doExport('continue', 'sejour')">
        {{tr}}Continue{{/tr}}      
      </button>
    </td>
    <td id="export-sejour"></td>
  </tr>
</table>