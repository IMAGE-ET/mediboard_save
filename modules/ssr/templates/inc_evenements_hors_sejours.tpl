{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
Main.add(function() {
  var sejours_count = {{$sejours_count|json}};
  var link = $('tabs-replacement').down('a[href=#hors-sejours]');
	link.down('small').update('('+sejours_count+')');
	link.setClassName('wrong', sejours_count != 0);
})

Planification.purge = function(sejour_id) {
  var url = new Url;
  url.addParam('m', 'ssr');
  url.addParam('dosql', 'do_purge_sejour_ssr');
  url.addParam('sejour_id', sejour_id);
	url.requestUpdate("systemMsg", {
	  method: 'post',
		onComplete : Planification.onCompleteShowWeek
  } )
}

</script>

<table class="tbl">
  {{foreach from=$sejours key=sejour_id item=_sejour}}
			
  <tr id="hors-sejours-{{$sejour_id}}">
    <td colspan="2" class="text">
      {{assign var=patient value=$_sejour->_ref_patient}}
		  <big class="CPatient-view" 
			  onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}};')" 
			  onclick="this.up('tr').addUniqueClassName('selected'); Planification.refreshSejour('{{$sejour_id}}', false);" >
				{{$patient}}
			</big> 
		  <br />
			{{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
      {{$patient->_age}}
    </td>
    <td style="text-align: center;">
      {{mb_value object=$_sejour field=entree format=$conf.date}}
      <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
    </td>
    <td style="text-align: center;">
      {{mb_value object=$_sejour field=sortie format=$conf.date}}
      <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
    </td>
		<td>
			{{$evenements_counts.$sejour_id}}
		</td>
    <td class="button">
    	<button class="trash notext" onclick="Planification.purge('{{$sejour_id}}')">{{tr}}Purge{{/tr}}</button>
    </td>
  </tr>

	{{foreachelse}}
	<tr>
	  <td colspan="10" class="empty">{{tr}}CSejour.none{{/tr}}</td>
	</tr>
  {{/foreach}}
</table>