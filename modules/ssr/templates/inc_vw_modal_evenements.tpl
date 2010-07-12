<table class="tbl">
	<tr>
		<th colspan="3">Evenements</th>
	</tr>
	{{foreach from=$evenements item=_evenements_by_sejour}}
	  {{foreach from=$_evenements_by_sejour item=_evenement name="evts"}}
		{{if $smarty.foreach.evts.first}}
		<tr>
		  <th colspan="3">{{$_evenement->_ref_sejour->_ref_patient->_view}}</th>
		</tr>	
		{{/if}}
		<tr>
			<td>
				{{$_evenement->_ref_prescription_line_element->_ref_element_prescription->_view}}
			</td>
			<td>
			  {{mb_value object=$_evenement field="debut"}} - {{mb_value object=$_evenement field="duree"}} min
			</td>
			<td>
				<input type="checkbox" checked="checked" onclick="tab_selected.toggle('{{$_evenement->_id}}');">
			</td>
		</tr>
		{{/foreach}}
	{{/foreach}}
	<tr>
		<td colspan="3" class="button">
			<button type="button" class="submit" onclick="submitValidation(oFormSelectedEvents); modalWindow.close();">{{tr}}Validate{{/tr}}</button>
			<button type="button" class="cancel" onclick="modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
		</td>
	</tr>
</table>