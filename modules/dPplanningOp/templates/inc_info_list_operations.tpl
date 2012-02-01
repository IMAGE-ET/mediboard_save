{{foreach from=$sejour->_ref_operations item=_operation name=operation}}
<tr>
  <td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
  </td>
  <td>{{$_operation->_datetime|date_format:$conf.date}} 
            
	{{if @$modules.brancardage->_can->read}}
	  {{mb_script module=brancardage script=creation_brancardage ajax=true }}
		<input id="modif" type="hidden" name="modif"/>
	  <div id="patientpret" >
	  	<input type="hidden" name="param_brancard"  id="param_brancard"
         data-salle-id  ="{{$_operation->salle_id}}"
         data-sejour-id ="{{$_operation->sejour_id}}"
				 data-operation-id="{{$_operation->_id}}"
         data-charge    ="" />
	    <button type="button" class="brancard" onclick="CreationBrancard.patientPret('{{$_operation->sejour_id}}','{{$_operation->salle_id}}','{{$_operation->_id}}');" >
	       Patient pret
	    </button>
	  </div>
	{{/if}}
	
  {{if $_operation->annulee}}
  <th class="category cancelled">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}');">{{tr}}COperation-annulee{{/tr}}</strong>
  </th>
  {{else}}
  <td class="text">
    {{mb_include module=dPplanningOp template=inc_vw_operation}}
  </td>
	{{/if}}
  <td class="narrow button">
    <button class="{{if $_operation->_ref_consult_anesth->_ref_consultation->_id}}print{{else}}warning{{/if}}" type="button"
    onclick="
    {{if $offline}}
      var fiche = $('fiche_anesth_{{$_operation->_id}}');
      if (fiche) {
        modal(fiche);
      }
    {{else}}
      printFicheAnesth('{{$_operation->_ref_consult_anesth->_ref_consultation->_id}}', '{{$_operation->_id}}');
    {{/if}}">
      Fiche d'anesth�sie
    </button>
    <br />
    <button class="print" style="width: 100%; min-width: 10em;" type="button" onclick="printFicheBloc('{{$_operation->_id}}');">
      Feuille de bloc
    </button>
    {{mb_include module=forms template=inc_widget_ex_class_register object=$_operation event=liaison}}
  </td>
</tr>

{{if $_operation->_back && array_key_exists("check_lists", $_operation->_back)}}
<tr>
  <td colspan="10">
    {{mb_include module=dPsalleOp template=inc_vw_check_lists operation=$_operation}}
  </td>
</tr>
{{/if}}
{{foreachelse}}
<tr>
  <td colspan="4" class="empty">{{tr}}COperation.none{{/tr}}</td>
</tr>
{{/foreach}}