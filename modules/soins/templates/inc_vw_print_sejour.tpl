{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
<tbody style="page-break-inside: avoid; font-size: 1.2em;">
<tr>
  <th colspan="2" style="text-align: left;">
    <!-- Identit� du patient -->
    <table class="main layout">
      <tr>
        <td style="width: 80px;">
          {{if ($service_id && $service_id != "NP") || $show_affectation}}
            {{assign var=affectation value=$sejour->_ref_curr_affectation}}
            {{if $affectation->_id && $affectation->lit_id}}
              {{mb_value object=$affectation->_ref_lit field=nom}}
            {{/if}}
            {{if $sejour->isolement}}<br /><small>(isolement)</small>{{/if}}
          {{/if}}
        </td>

        <td>
          {{assign var=statut value="present"}}
          {{if !$sejour->entree_reelle || ($sejour->_ref_prev_affectation->_id && $sejour->_ref_prev_affectation->effectue == 0)}}
            {{assign var=statut value="attente"}}
          {{/if}}
          {{if $sejour->sortie_reelle || $sejour->_ref_curr_affectation->effectue == 1}}
            {{assign var=statut value="sorti"}}
          {{/if}}

          <strong class="{{if $statut == "attente"}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}"
            {{if $statut == "sorti"}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
            {{$patient}}
          </strong>
        </td>

        <td style="text-align: right;">
          {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
          {{$patient->_age}} <span style="font-weight: normal;">({{mb_value object=$patient field=naissance}})</span>
        </td>
      </tr>
    </table>
  </th>
</tr>

<tr>
  <td class="text" style="vertical-align: top;">
    <!-- Praticien -->
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}

    <!-- Transmissions -->
    {{if $sejour->_ref_transmissions|@count}}
      <hr />
      {{foreach from=$sejour->_ref_transmissions item=_transmission}}
        <div onmouseover="ObjectTooltip.createEx(this, '{{$_transmission->_guid}}')" style="display: inline;">
          <strong>{{$_transmission->type|substr:0:1|upper}}</strong>: {{$_transmission->text|nl2br}}
        </div>
      {{/foreach}}
    {{/if}}
  </td>

  <td class="text" style="vertical-align: top;">
    <!-- Modif d'hospi -->
    {{if $sejour->_ref_prescription_sejour->_jour_op|@count}}

      {{foreach from=$sejour->_ref_prescription_sejour->_jour_op item=_info_jour_op}}
        <div>
          (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
        </div>
      {{/foreach}}
    {{/if}}

    {{if $sejour->libelle}}
      <strong>{{mb_label object=$sejour field=libelle}}:</strong>
      {{mb_value object=$sejour field=libelle}}
    {{/if}}

    {{if $sejour->_ref_tasks}}
      <hr />
      {{foreach from=$sejour->_ref_tasks item=_task}}
        {{$_task->description}}
        {{if $_task->prescription_line_element_id}}
          {{$_task->_ref_prescription_line_element->_view}}
        {{/if}}
        {{if $_task->resultat}}
          : {{$_task->resultat}}
        {{/if}}
        <br />
      {{/foreach}}
    {{/if}}

    {{if $sejour->_ref_tasks_not_created|@count}}
      {{foreach from=$sejour->_ref_tasks_not_created item=_task_not_created}}
        {{$_task_not_created}}
        <br />
      {{/foreach}}
    {{/if}}

    <!-- Allergies -->
    <strong>{{tr}}CAntecedent.type.alle{{/tr}}:</strong>
      {{foreach from=$dossier_medical->_ref_allergies item=_allergie name=alle}}
        {{$_allergie}}
        {{if !$smarty.foreach.alle.last}}&bull;{{/if}}
      {{foreachelse}}
        <em>Non renseign�</em>
      {{/foreach}}

    <!-- Ant�c�dents -->
    {{if $dossier_medical->_count_antecedents && ($dossier_medical->_count_antecedents > $dossier_medical->_count_allergies)}}
      <hr />
      {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
      {{foreach from=$antecedents key=name item=cat}}
        {{if $name != "alle" && $cat|@count}}
          <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}:</strong>
          {{foreach from=$cat item=ant name=ants}}
            {{if $ant->date}}
              {{mb_value object=$ant field=date}}:
            {{/if}}
            {{$ant->rques}}
            {{if !$smarty.foreach.ants.last}}&bull;{{/if}}
          {{/foreach}}
          <br />
        {{/if}}
      {{/foreach}}
    {{/if}}
  </td>
</tr>
</tbody>