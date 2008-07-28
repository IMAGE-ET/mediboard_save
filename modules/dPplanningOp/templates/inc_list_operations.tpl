{{mb_include_script module="dPcompteRendu" script="document"}}

<input id="currDateJSAccess" name="currDateJSAccess" type="hidden" value="{{$date}}" />
{{if !$board}}
<div style="font-weight:bold; height:20px; text-align:center;">
  {{$date|date_format:"%A %d %B %Y"}}
  <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  <script type="text/javascript">
    regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_idx_planning&date=");
  </script>
</div>
{{/if}}

{{if $boardItem}}
      <table class="tbl" style="font-size: 9px;">
{{else}}
      <table class="tbl">
{{/if}}
        <tr>
          <th class="title" colspan="5">
             Interventions
          </th>
        </tr>
        <tr>
          <th>{{mb_label class=CSejour field=patient_id}}</th>
          <th>
            [{{mb_label class=COperation field=libelle}}] 
            {{mb_label class=COperation field=codes_ccam}}
          </th>
          <th>{{mb_title class=COperation field=time_operation}}</th>
          <th>{{mb_title class=COperation field=temp_operation}}</th>
          {{if !$boardItem}}
            <th>Compte-rendu</th>
          {{/if}}
        </tr>
        
        {{if $urgences}}
        {{foreach from=$listUrgences item=curr_op}}
        <tr>
        
          <td class="text">
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}"
              class="tooltip-trigger"
              onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CPatient', object_id: {{$curr_op->_ref_sejour->_ref_patient->_id}} } })"
            >
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled{{/if}}">
            {{if $curr_op->annulee}}
              [ANNULEE]
            {{else}}
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->_datetime|date_format:"le %d/%m/%Y � %Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>

          <td id="{{$curr_op->_guid}}">
						<script type="text/javascript">
        			Document.register('{{$curr_op->_id}}','{{$curr_op->_class_name}}','{{$curr_op->chir_id}}', '{{$curr_op->_guid}}', 'collapse');
      			</script>
            
          </td>
        </tr>
        {{/foreach}}
        {{else}}
        {{foreach from=$listDay item=curr_plage}}
        <tr>
          <th colspan="6">{{$curr_plage->_ref_salle->nom}} : de {{$curr_plage->debut|date_format:"%Hh%M"}} � {{$curr_plage->fin|date_format:"%Hh%M"}}</th>
        </tr>
        {{foreach from=$curr_plage->_ref_operations item=curr_op}}
        <tr>
          <td class="text">
            <a href="{{$curr_op->_ref_sejour->_ref_patient->_dossier_cabinet_url}}"
              class="tooltip-trigger"
              onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CPatient', object_id: {{$curr_op->_ref_sejour->_ref_patient->_id}} } })"
            >
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              {{if $boardItem}}
              : {{$curr_code->libelleLong|truncate:50:"...":false}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;" {{if $curr_op->annulee}}class="cancelled{{/if}}">
            {{if $curr_op->annulee}}
              [ANNULEE]
            {{else}}
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{if $curr_op->time_operation != "00:00:00"}}
                Valid� pour 
                <br/ >{{$curr_op->time_operation|date_format:"%Hh%M"}}
              {{else}}
                Non valid�
              {{/if}}
              <br />
              {{if $curr_op->horaire_voulu}}
              souhait� pour 
              <br/>{{$curr_op->horaire_voulu|date_format:"%Hh%M"}}
              {{/if}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>

          {{if !$boardItem}}
          <td id="{{$curr_op->_guid}}">
						<script type="text/javascript">
        			Document.register('{{$curr_op->_id}}','{{$curr_op->_class_name}}','{{$curr_op->chir_id}}', '{{$curr_op->_guid}}', 'collapse');
      			</script>
            
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/if}}
      </table>