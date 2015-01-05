{{mb_default var=offline value=0}}
{{mb_default var=alert   value=0}}

{{foreach from=$sejour->_ref_operations item=_operation name=operation}}
<tr>
  <td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
  </td>
  <td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_anesth}}
  </td>
  <td>{{$_operation->_datetime|date_format:$conf.date}}
  {{if $_operation->_datetime|date_format:$conf.time != "00h00"}}
    {{$_operation->_datetime|date_format:$conf.time}}
  {{/if}}
  {{if $_operation->annulee}}
  <th class="category cancelled">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}');">{{tr}}COperation-annulee{{/tr}}</strong>
  </th>
  {{else}}
  <td class="text">
    {{if $alert}}
      <span style="float: right">
        {{mb_include module=planningOp template=inc_reload_infos_interv operation=$_operation just_alert=1}}
      </span>
    {{/if}}
    {{mb_include module=planningOp template=inc_vw_operation}}
  </td>
  {{/if}}
  
  {{if @$modules.brancardage->_can->read}}
    <td>
      {{mb_script module=brancardage script=creation_brancardage ajax="true"}}
      <input id="modif" type="hidden" name="modif"/>
      {{assign var=bloc_brancard value=$_operation->_ref_salle->bloc_id}}
      <div id="patient_pret-{{$_operation->sejour_id}}">
        {{mb_include module=brancardage template=inc_exist_brancard colonne="patient_pret" see_sejour=true destination="CBlocOperatoire"
        destination_guid="CBlocOperatoire-$bloc_brancard" callback="refreshListIntervs" date_brancard=$_operation->_datetime|date_format:"%Y-%m-%d"}}
      </div>
    </td>  
  {{/if}}
  
  <td class="narrow button">
    <button class="{{if $_operation->_ref_consult_anesth->_id}}print{{else}}warning{{/if}}" type="button"
    onclick="
    {{if $offline}}
      var fiche = $('fiche_anesth_{{$_operation->_id}}');
      if (fiche) {
    Modal.open(fiche);
      }
    {{else}}
      printFicheAnesth('{{$_operation->_ref_consult_anesth->_id}}', '{{$_operation->_id}}');
    {{/if}}">
      Fiche d'anesth�sie
    </button>
    <br />
    <button class="print" style="width: 100%; min-width: 10em;" type="button" onclick="printFicheBloc('{{$_operation->_id}}');">
      Feuille de bloc
    </button>
    {{mb_include module=forms template=inc_widget_ex_class_register object=$_operation event_name=liaison}}
  </td>
</tr>

{{if $_operation->_back && array_key_exists("check_lists", $_operation->_back)}}
<tr>
  <td colspan="10">
    {{mb_include module=salleOp template=inc_vw_check_lists object=$_operation}}
  </td>
</tr>
{{/if}}
{{foreachelse}}
<tr>
  <td colspan="4" class="empty">{{tr}}COperation.none{{/tr}}</td>
</tr>
{{/foreach}}