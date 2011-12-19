{{mb_default var=offline value=0}}

<table class="tbl">
  {{if $offline}}
    <thead>
      <tr>
        <th class="title" colspan="7">
          {{$sejour->_view}}
          {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA}}
        </th>
      </tr>
    </thead>
  {{/if}}
  <tr>
    <th colspan="7" class="title">
			{{if !$readonly}}
				<div style="float: right">
          <input name="_show_obs_view" id="_show_obs_view" type="checkbox" {{if $_show_obs}}checked="checked"{{/if}}
            onchange="loadSuivi('{{$sejour->_id}}', '', '', this.checked ? 1 : 0, $('_show_trans_view').checked ? 1 : 0, !$('_show_const_view') ? null : $('_show_const_view').checked ? 1 : 0)"/>
          <label for="_show_obs_view" title="{{tr}}CObservationMedicale{{/tr}}">{{tr}}CObservationMedicale._show_obs{{/tr}}</label>
          
          <input name="_show_trans_view" id="_show_trans_view" type="checkbox" {{if $_show_trans}}checked="checked"{{/if}}
            onchange="loadSuivi('{{$sejour->_id}}', '', '', $('_show_obs_view').checked ? 1 : 0, this.checked ? 1 : 0, !$('_show_const_view') ? null : $('_show_const_view').checked ? 1 : 0)"/>
          <label for="_show_trans_view" title="{{tr}}CTransmissionMedicale{{/tr}}">{{tr}}CTransmissionMedicale._show_trans{{/tr}}</label>
          
          {{if $conf.soins.constantes_show}}
            <input name="_show_const_view" id="_show_const_view" type="checkbox" {{if $_show_const}}checked="checked"{{/if}}
              onchange="loadSuivi('{{$sejour->_id}}', '', '', $('_show_obs_view').checked ? 1 : 0, $('_show_trans_view').checked ? 1 : 0, this.checked ? 1 : 0)"/>
            <label for="_show_const_view" title="{{tr}}CConstantesMedicales{{/tr}}">{{tr}}CConstantesMedicales._show_const{{/tr}}</label>
          {{/if}}
          
			    <select style="width:150px" name="selCible" onchange="loadSuivi('{{$sejour->_id}}','',this.value)" >
			      <option value="">&mdash; Toutes les cibles</option>
			      {{foreach from=$cibles item=cibles_by_type}}
			        {{foreach from=$cibles_by_type item=_cible}}
			          <option {{if $_cible == $cible}} selected="selected" {{/if}} value="{{$_cible}}">{{$_cible|capitalize}}</option>
			        {{/foreach}}
			      {{/foreach}}
			    </select>
			    
			    {{if @$users}}
			    <select name="user_id" onchange="loadSuivi('{{$sejour->_id}}',this.value)">
			      <option value="">&mdash; Tous les utilisateurs</option>
			      {{foreach from=$users item=_user}}
			        <option value="{{$_user->_id}}" {{if $user_id == $_user->_id}} selected="selected"{{/if}}>{{$_user->_view}}</option>
			      {{/foreach}}
			    </select>
			    {{/if}}
		    </div>
	    {{/if}}
	    <span style="float: left;"> Suivi de soins</span>
    </th>
  </tr>
  <tr>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}User{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>{{mb_title class=CTransmissionMedicale field=object_class}}</th>
		<th>{{mb_title class=CTransmissionMedicale field=text}}</th>
    <th />
  </tr>  
  <tbody {{if !$readonly}} id="transmissions" {{/if}}>
  {{foreach from=$list_transmissions item=_suivi}}
	<tr class="{{if is_array($_suivi)}}{{$_suivi.0->_ref_object->_guid}}{{else}}{{$_suivi->_guid}}{{/if}}"
	    {{if ($_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment) && !$readonly}}
			  onmouseover="console.log('test');highlightTransmissions('{{$_suivi->_guid}}');" onmouseout="removeHighlightTransmissions();"
			{{/if}}>
 	  {{mb_include module=dPhospi template=inc_line_suivi show_patient=false nodebug=true}}
		</tr>
  {{foreachelse}}
  </tbody>
    <tr>
      <td colspan="7" class="empty">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>