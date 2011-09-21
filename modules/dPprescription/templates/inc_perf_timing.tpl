{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

{{if $prescription_line_mix->perop}}
// Submit du debut ou de la fin de la perf
submitTiming = function(){
  var oForm = document.forms['editPerf{{$prescription_line_mix->_id}}'];
  submitFormAjax(oForm, "systemMsg", { onComplete: function() {
    window.opener.Prescription.updatePerop('{{$sejour_id}}');
		window.close();
  } } );
}
{{/if}}
	
</script>

{{assign var=prescription_line_mix_id value=$prescription_line_mix->_id}}
<form name="editPerf{{$prescription_line_mix->_id}}" method="post" action="?">
  <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_mix_id" value="{{$prescription_line_mix_id}}" />
	<table class="form">
	  <tr>
	    <th class="title text">
	    {{foreach from=$prescription_line_mix->_ref_lines item=_perf_line name="foreach_perf"}}
	     {{$_perf_line->_view}}<br />
	    {{/foreach}}
	    {{$prescription_line_mix->_view}}
	    </th>
	  </tr>
	  <tr>
	    <td>
			  {{if $prescription_line_mix->date_pose}}
			    {{mb_label object=$prescription_line_mix field=date_pose}}
			    {{mb_field object=$prescription_line_mix field=date_pose form="editPerf$prescription_line_mix_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$prescription_line_mix field=time_pose form="editPerf$prescription_line_mix_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_pose.value = ''; this.form.time_pose.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_pose" value="" />
			    <input type="hidden" name="time_pose" value="" />
			    <button type="button" class="submit" onclick="this.form.date_pose.value = 'current'; this.form.time_pose.value = 'current'; submitTiming();">{{tr}}CPrescriptionLineMix-date_pose{{/tr}}</button>
			  {{/if}}
			</td>
	  </tr>
	  <tr>
	    <td>
			  {{if $prescription_line_mix->date_retrait}}
			    {{mb_label object=$prescription_line_mix field=date_retrait}}
			    {{mb_field object=$prescription_line_mix field=date_retrait form="editPerf$prescription_line_mix_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$prescription_line_mix field=time_retrait form="editPerf$prescription_line_mix_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_retrait.value = ''; this.form.time_retrait.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_retrait" value="" />
			    <input type="hidden" name="time_retrait" value="" />
			    <button type="button" class="submit" onclick="this.form.date_retrait.value = 'current'; this.form.time_retrait.value = 'current'; submitTiming();">{{tr}}CPrescriptionLineMix-date_retrait{{/tr}}</button>
			  {{/if}}
			</td>
	  </tr>
	</table>
</form>

{{if !$prescription_line_mix->perop}}
<table class="tbl">
	<th class="title">Variations du d�bit de la perfusion</th>
	{{foreach from=$prescription_line_mix->_ref_variations item=_variation}}
	{{assign var=variation_id value=$_variation->_id}}
	<tr>
		<td>
			<form name="editVariation-{{$variation_id}}" action="?" method="post" 
			      onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshPerfTiming(); refreshDossierSoin(); } } );">
			  <input type="hidden" name="m" value="dPprescription" />
				<input type="hidden" name="dosql" value="do_prescription_line_mix_variation_aed" />
				<input type="hidden" name="prescription_line_mix_variation_id" value="{{$_variation->_id}}" />
				<input type="hidden" name="del" value="0" />
			  <table class="layout">
			  	<tr>
			  		<td>
			  			<button type="button" class="cancel notext" onclick="$V(this.form.del, '1'); this.form.onsubmit();"></button>
			  		</td>
			  		<td>{{mb_field object=$_variation field=dateTime form=editVariation-$variation_id register=true}}</td>
	          <td>{{mb_field object=$_variation field=debit increment=1 form="editVariation-$variation_id"}}</td>
						<td><button type="submit" class="submit notext"></button></td>
			  	</tr>
			  </table>
			</form>
		</td>
  </tr>
	{{foreachelse}}
	<tr>
		<td>Aucune variation</td>
	</tr>
	{{/foreach}}
</table>
{{/if}}