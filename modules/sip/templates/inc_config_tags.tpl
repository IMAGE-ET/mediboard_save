{{* $Id: inc_config_tags.tpl 8090 2010-02-17 16:02:09Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 8090 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=mod value=dPpatients}}
{{assign var=class value=CPatient}}
{{assign var="var" value="tag_ipp"}}
<tr>
  <th>
    <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}-desc{{/tr}}">
      {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$conf.$mod.$class.$var}}" />
    {{if $conf.$mod.$class.$var != $pat}}
    <div class="small-warning">
      Le tag IPP pour l'utilisation de ce module dans cet �tablissement devrait �tre : '{{$pat}}' <br />
      <button type="submit" class="change" onclick="this.form.elements['{{$mod}}[{{$class}}][{{$var}}]'].value = '{{$pat}}'">
        {{tr}}Restore{{/tr}} le bon tag
      </button>
    </div>
    {{else}}  
    <div class="small-success">
      Le tag IPP est compatible avec l'utilisation de ce module dans cet �tablissement.
    </div>
    {{/if}}
  </td>
</tr>

{{assign var=mod value=dPplanningOp}}
{{assign var=class value=CSejour}}
{{assign var="var" value="tag_dossier"}}
<tr>
  <th>
    <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}-desc{{/tr}}">
      {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$conf.$mod.$class.$var}}" />
    {{if $conf.$mod.$class.$var != $sej}}
    <div class="small-warning">
      Le tag 'Num�ro de dossier' pour l'utilisation de ce module dans cet �tablissement devrait �tre : '{{$sej}}'
      <br />
      <button type="submit" class="change" onclick="this.form.elements['{{$mod}}[{{$class}}][{{$var}}]'].value = '{{$sej}}'">
        {{tr}}Restore{{/tr}} le bon tag
      </button>
    </div>
    {{else}}  
    <div class="small-success">
      Le tag 'Num�ro de dossier' est compatible avec l'utilisation de ce module dans cet �tablissement.
    </div>
    {{/if}}
  </td>
</tr>