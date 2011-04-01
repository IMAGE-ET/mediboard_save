{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
	$('technicien-{{$technicien_id}}').down('small').update('({{$sejours_count}})');

</script>
{{if !$technicien_id}} 
<!-- Filtre sur service-->
<tr>
  <td>
  	<form onsubmit="return Repartition.updateTechnicien('', this);" method="get">
	    <select name="service_id" onchange="this.form.onsubmit()">
	      <option value="">&mdash; {{tr}}CService.all{{/tr}}</option>
	      {{foreach from=$services item=_service}}
	      <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}} selected="selected" {{/if}}>
	        {{$_service}}
	      </option>
	      {{/foreach}}
	    </select>
	    <br/>
	    {{mb_include template=inc_show_cancelled_services}}
  	</form>
  </td>
</tr>

{{/if}}


{{foreach from=$sejours item=_sejour}}
{{mb_include template=inc_sejour_draggable remplacement=0 sejour=$_sejour}}
{{foreachelse}}
<tr>
  <td class="empty">{{tr}}CSejour.none{{/tr}}</td>
</tr>
{{/foreach}}


{{if count($replacements)}}
<tr>
  <th>{{tr}}CReplacement{{/tr}}s</th>
</tr>
{{/if}}
{{foreach from=$replacements item=_replacement}}
<tr>
	<td>
    {{assign var=conge value=$_replacement->_ref_conge}}
    {{assign var=replaced  value=$conge->_ref_user}}
		<span onmouseover="ObjectTooltip.createEx(this, '{{$conge->_guid}}')">
			{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$replaced}}
		</span>
	</td>
</tr>

{{mb_include template=inc_sejour_draggable remplacement=1 sejour=$_replacement->_ref_sejour}}
{{/foreach}}
