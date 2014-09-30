{{mb_default var=show_age_sexe_mvt value=0}}
{{mb_default var=show_hour_anesth_mvt value=0}}
{{mb_default var=affectation value=0}}
{{assign var=patient value=$sejour->_ref_patient}}

<tr {{if $sejour->recuse == -1}}class="opacity-70"{{/if}}>
  {{if $show_duree_preop && $type_mouvement != "sorties"}}
    <td>
      {{mb_value object=$sejour->_ref_curr_operation field=_heure_us}}
    </td>
  {{/if}}
  <td class="text {{if $sejour->confirme}}arretee{{/if}}">
    {{if $canPlanningOp->read}}
    <div style="float: right">
      {{if $isImedsInstalled}}
        {{mb_include module=Imeds template=inc_sejour_labo link="#1" float="none"}}
      {{/if}}
        
      <a class="action" style="display: inline" title="Modifier le s�jour" href="?m=planningOp&tab=vw_edit_sejour&sejour_id={{$sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
    </div>
    {{/if}}
    
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')"
      {{if !$sejour->entree_reelle}} class="patient-not-arrived"{{/if}}>
      {{$patient}}
    </strong>
    <br />
    {{mb_include module=hospi template=inc_vw_liaisons_prestation liaisons=$sejour->_liaisons_for_prestation}}
  </td>

  {{if $show_age_sexe_mvt}}
    <td>
      {{$patient->sexe|strtoupper}}
    </td>
    <td>
      {{mb_value object=$patient field=_age}}
    </td>
  {{/if}}

  <td class="text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </td>
  <td class="text">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">{{$sejour->_motif_complet}}</strong>
  </td>

  {{if $show_hour_anesth_mvt}}
    {{assign var=op value=$sejour->_ref_curr_operation}}
    <td>
      {{if $op->_id}}
        {{$op->_datetime_best|date_format:$conf.time}}
      {{/if}}
    </td>
    <td>
      {{if $op->_id}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$op->_ref_anesth}}
      {{/if}}
    </td>
  {{/if}}

  {{if "dmi"|module_active}}
    <td class="button">
      {{foreach from=$sejour->_ref_operations item=_interv}}
        {{mb_include module=dmi template=inc_dmi_alert interv=$_interv}}
      {{/foreach}}
    </td>
  {{/if}}
  <td class="text {{if $sejour->sortie_reelle}}effectue{{/if}}">
    {{if $affectation}}
      {{$affectation->_ref_lit}}
    {{else}}
      -
    {{/if}}
  </td>
  <td class="narrow">
    {{$sejour->entree|date_format:$conf.datetime}}
    <div style="position: relative;">
    <div class="sejour-bar" title="arriv�e il y a {{$sejour->_entree_relative}}j et d�part pr�vu dans {{$sejour->_sortie_relative}}j ">
      <div style="width: {{if $sejour->_duree}}{{math equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}{{else}}100{{/if}}%;"></div>
    </div>
    </div>
  </td>
  <td class="narrow {{if $sejour->confirme}}ok{{else}}warning{{/if}}">
    <div class="only-printable">
      {{if $type == 'presents'}}
        {{if $affectation}}
          {{$affectation->sortie|date_format:$conf.datetime}} / {{mb_value object=$sejour field="mode_sortie"}}
        {{else}}
          {{$sejour->sortie|date_format:$conf.datetime}} / {{mb_value object=$sejour field="mode_sortie"}}
        {{/if}}
      {{else}}
        {{if $affectation}}
          {{$affectation->sortie|date_format:$conf.time}} / {{mb_value object=$sejour field="mode_sortie"}}
        {{else}}
          {{$sejour->sortie|date_format:$conf.time}} / {{mb_value object=$sejour field="mode_sortie"}}
        {{/if}}
      {{/if}}
    </div>
    <div class="not-printable" style="text-align: center">
      {{if $affectation}}
        {{assign var=aff_guid value=$affectation->_guid}}
      {{else}}
        {{assign var=aff_guid value=$sejour->_guid}}
      {{/if}}
      <form name="editSortiePrevue-{{$type}}-{{$aff_guid}}" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, refreshList.curry(null, null, '{{$type}}', '{{$type_mouvement}}'))">
        <input type="hidden" name="m" value="planningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        {{if $sejour->confirme}}
          {{mb_value object=$sejour field=sortie}}
          / {{mb_value object=$sejour field="mode_sortie"}}
        {{else}}
          {{mb_value object=$sejour field=sortie_prevue}} / {{mb_value object=$sejour field="mode_sortie"}}<br/>
          <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
          {{mb_field object=$sejour field=sortie_prevue hidden=true form="editSortiePrevue-`$type`-`$aff_guid`" onchange="this.form.onsubmit()"}}
        {{/if}}
        {{if $sejour->sortie_reelle}}
          / <strong>Effectu�e</strong>
        {{else}}
          <button class="edit" type="button"
                  onclick='Admissions.validerSortie("{{$sejour->_id}}", true, refreshList.curry("{{$order_col}}", "{{$order_way}}", "{{$type}}", "{{$type_mouvement}}"));'>
            Modifier
          </button>
        {{/if}}
      </form>
    </div>
  </td>
  {{if $type == "ambu"}}
    {{if $show_retour_mvt}}
      <td></td>
    {{/if}}
    {{if $show_collation_mvt}}
      <td></td>
    {{/if}}
    {{if $show_sortie_mvt}}
      <td></td>
    {{/if}}
  {{/if}}
</tr>