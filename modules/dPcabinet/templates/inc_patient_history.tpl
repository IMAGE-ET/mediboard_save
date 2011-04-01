<!-- S�jour et interventions -->
{{if !$app->user_prefs.simpleCabinet}}
<div>
	<span onmouseover="ObjectTooltip.createDOM(this, 'sejours');">
	  {{$patient->_ref_sejours|@count}} {{tr}}CSejour{{/tr}}(s)   
	</span>
</div>
  
<table class="tbl" id="sejours" style="display: none;">
  {{foreach from=$patient->_ref_sejours item=_sejour}}
  <tr>
    <td>
      {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
		</td>
		<td>
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
		</td>
	</tr>

  {{foreach from=$_sejour->_ref_operations item=_op}}
  <tr>
  	<td style="padding-left: 1em;">
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_op->_id}}"
        onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
        le {{$_op->_datetime|date_format:$conf.date}}
      </a>
		</td>
		<td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
		</td>
	</tr>
  {{foreachelse}}
  <tr>
  	<td colspan="2" class="empty" style="padding-left: 1em;">
       {{tr}}COperation.none{{/tr}}
  	</td>
	</tr>
  {{/foreach}}

  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CSejour.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}
  
<!-- Consultations -->
<div>
	<span  onmouseover="ObjectTooltip.createDOM(this, 'consultations');">
		{{$patient->_ref_consultations|@count}} {{tr}}CConsultation{{/tr}}(s)
	</span>
</div>
  
<table class="tbl" id="consultations" style="display: none;">
  
  {{foreach from=$patient->_ref_consultations item=_consult}}
  <tr>
    <td>
      <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}"
        onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
        le {{$_consult->_datetime|date_format:$conf.date}}
      </a>
    </td>
    <td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
    </td>

    <td>
    </td>
  </tr>
  {{foreachelse}}
  
  <tr>
    <td colspan="2" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
  </tr>
  {{/foreach}}
	
</table>
