{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CPlageOp" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    <tr>
      {{assign var="var" value="hours_start"}}
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td class="greedyPane">
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      {{assign var="var" value="hours_stop"}}
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=locked}}
    

    {{mb_include module=system template=inc_config_enum var=systeme_materiel values="standard|expert"}}
    
    <tr>
      {{assign var="var" value="hour_midi_fullprint"}}
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
        {{foreach from=$hours item=_hour}}
          <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>

    {{mb_include module=system template=inc_config_bool var=view_empty_plage_op}}

    <tr>
      <th colspan="2" class="title">Affichages</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=view_prepost_suivi}}
    {{mb_include module=system template=inc_config_bool var=chambre_operation}}
    {{mb_include module=system template=inc_config_enum var=time_autorefresh values="120|300|600"}}

    {{mb_include module=system template=inc_config_bool var=view_tools}}
    {{mb_include module=system template=inc_config_bool var=view_required_tools}}
    {{mb_include module=system template=inc_config_bool var=view_anesth_type}}
    {{mb_include module=system template=inc_config_bool var=view_rques}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>