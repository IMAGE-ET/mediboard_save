{{mb_default var=selected_guid value=""}}
{{mb_default var=show_semaine_grossesse value=0}}
{{if $object instanceof CSejour}}

{{if $object->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
<tr {{if $object->_guid == $selected_guid}} class="selected" {{/if}}
  {{if isset($collision_sejour|smarty:nodefaults) && $object->_id == $collision_sejour}}
    style="border: 2px solid red;"
  {{elseif $object->_is_proche}}
    style="border: 2px solid blue;"
  {{/if}}>
  <td class="text">
    {{if $object->_canEdit}}
    <a class="actionPat" title="Modifier le s�jour" href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$object->_id}}">
      <img src="images/icons/planning.png" alt="Planifier"/>
    </a>
    {{/if}}
    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$object _show_numdoss_modal=1}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
    {{$object->_shortview}}
    {{if $object->_nb_files_docs}}
      - ({{$object->_nb_files_docs}} Doc.)
    {{/if}}
    </span>

  </td>
  <td style="text-align: left;" {{if $object->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_praticien}}
  </td>
</tr>

{{foreach from=$object->_ref_consultations item=_consult}}
<tr {{if $object->_guid == $selected_guid}} class="selected" {{/if}}>
  <td class="text" style="text-indent: 1em;">
    {{if $_consult->_canEdit}}
    <a class="actionPat" title="Modifier la consultation" href="?m=cabinet&tab=edit_planning&consultation_id={{$_consult->_id}}">
      <img src="images/icons/planning.png" alt="modifier" />
    </a>
    <a class="iconed-text {{$_consult->_type}}"
      href="?m=cabinet&tab=edit_consultation&selConsult={{$_consult->_id}}&chirSel={{$_consult->_ref_plageconsult->chir_id}}">
    {{else}}
    <a href="#nothing" class="iconed-text {{$_consult->_type}}">
    {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
      Consultation le {{$_consult->_datetime|date_format:$conf.datetime}}
      {{if $_consult->_nb_files_docs}}
        - ({{$_consult->_nb_files_docs}} Doc.)
      {{/if}}
      </span>
    </a>
  </td>
  <td style="text-align: left;" {{if $_consult->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
  </td>
</tr>
{{/foreach}}

{{foreach from=$object->_ref_operations item=curr_op}}
<tr {{if $object->_guid == $selected_guid}} class="selected" {{/if}}>
  <td class="text" style="text-indent: 1em;">
    {{mb_ternary var=tab_editor test=$curr_op->plageop_id value=vw_edit_planning other=vw_edit_urgence}}
    {{assign var=link_editor value="?m=planningOp&tab=$tab_editor&operation_id=`$curr_op->_id`"}}
    {{if $curr_op->_canEdit}}
    <a class="actionPat" title="Modifier l'intervention" href="{{$link_editor}}">
      <img src="images/icons/planning.png" alt="modifier"/>
    </a>
    <a href="{{$link_editor}}" class="iconed-text interv">
    {{else}}
    <a href="#nothing" class="iconed-text interv">
    {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
      Intervention le {{$curr_op->_datetime|date_format:$conf.date}}
      {{if $curr_op->_nb_files_docs}}
        - ({{$curr_op->_nb_files_docs}} Doc.)
      {{/if}}
      </span>
    </a>
  </td>
  <td style="text-align: left;" {{if $curr_op->annulee}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_op->_ref_chir}}
  </td>
</tr>
{{if $curr_op->_ref_consult_anesth->_id}}
  {{assign var=consult_anesth value=$curr_op->_ref_consult_anesth}}
  {{assign var=consult value=$consult_anesth->_ref_consultation}}
  <tr>
    <td class="text" style="text-indent: 2em;">
      {{if $consult->_canRead}}
        <a class="actionPat" title="Modifier la consultation" href="?m=cabinet&tab=edit_planning&consultation_id={{$consult->_id}}">
          <img src="images/icons/planning.png" alt="modifier" />
        </a>
      {{/if}}
      {{if $consult->_canEdit}}
      <a class="iconed-text anesth"
         href="?m=cabinet&tab=edit_consultation&selConsult={{$consult->_id}}&chirSel={{$consult->_ref_plageconsult->chir_id}}&dossier_anesth_id={{$consult_anesth->_id}}">
        {{else}}
        <a href="#nothing" class="iconed-text anesth">
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
      Le {{$consult->_datetime|date_format:$conf.datetime}} - {{$consult->_etat}}
    </span>
        </a>
    </td>
    <td style="text-align: left;" {{if $consult->annule}}class="cancelled"{{/if}}>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult->_ref_chir}}
    </td>
  </tr>
{{/if}}
{{/foreach}}
{{elseif $conf.dPpatients.CPatient.multi_group == "limited" && !$object->annule}}
<tr>
  <td>
    {{$object->_shortview}}
  </td>
  <td style="background-color:#afa">
    {{$object->_ref_group->text|upper}}
  </td>
</tr>
{{/if}}

{{elseif $object instanceof CConsultation}}

{{if $object->_ref_chir->_ref_function->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
<tr {{if $object->_guid == $selected_guid}} class="selected" {{/if}}>
  <td class="text">
    {{if $object->_canRead}}
    <a class="actionPat" title="Modifier la consultation" href="?m=cabinet&tab=edit_planning&consultation_id={{$object->_id}}">
      <img src="images/icons/planning.png" alt="modifier" />
    </a>
    {{/if}}
    {{if $object->_canEdit}}
    <a class="iconed-text {{$object->_type}}" 
      href="?m=cabinet&tab=edit_consultation&selConsult={{$object->_id}}&chirSel={{$object->_ref_plageconsult->chir_id}}">
    {{else}}
    <a href="#nothing" class="iconed-text {{$object->_type}}">
    {{/if}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
      Le {{$object->_datetime|date_format:$conf.datetime}} - {{$object->_etat}}
      {{if "maternite"|module_active && $show_semaine_grossesse}}
        ({{$object->_semaine_grossesse}}�me semaine)
      {{/if}}
    </span>
    </a>
  </td>
  <td style="text-align: left;" {{if $object->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_chir}}
  </td>
</tr>
{{elseif $conf.dPpatients.CPatient.multi_group == "limited" && !$object->annule}}
<tr>
  <td>
    <span class="iconed-text">Le {{$object->_datetime|date_format:$conf.datetime}}</span>
  </td>
  <td style="background-color:#afa">
    {{$object->_ref_chir->_ref_function->_ref_group->text|upper}}
  </td>
</tr>
{{/if}}

{{/if}}