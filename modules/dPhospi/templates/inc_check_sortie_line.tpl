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
        
      <a class="action" style="display: inline" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
    </div>
    {{/if}}
    
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')"
      {{if !$sejour->entree_reelle}} class="patient-not-arrived"{{/if}}>
      {{$patient}}
    </strong>
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
          {{$affectation->sortie|date_format:$conf.datetime}}
        {{else}}
          {{$sejour->sortie|date_format:$conf.datetime}}
        {{/if}}
      {{else}}
        {{if $affectation}}
          {{$affectation->sortie|date_format:$conf.time}}
        {{else}}
          {{$sejour->sortie|date_format:$conf.time}}
        {{/if}}
      {{/if}}
    </div>
    <div class="not-printable">
      {{if $affectation}}
        {{assign var=aff_guid value=$affectation->_guid}}
      {{else}}
        {{assign var=aff_guid value=$sejour->_guid}}
      {{/if}}
      <form name="editSortiePrevue-{{$type}}-{{$aff_guid}}" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, { onComplete: function() { refreshList(null, null, '{{$type}}', '{{$type_mouvement}}'); } })">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="confirme" value="{{$sejour->confirme}}" />
        {{mb_key object=$sejour}}
        {{mb_field object=$sejour field=entree_prevue hidden=true}}
        {{if $sejour->confirme || $sejour->sortie_reelle}}
          {{mb_value object=$sejour field=sortie}}
          / {{mb_value object=$sejour field="mode_sortie"}}
          {{if $sejour->sortie_reelle}}
            / <strong>Effectu�e</strong>
          {{else}}
            <button type="button" onclick="$V(this.form.confirme, 0); this.form.onsubmit()" class="cancel">
              Annuler
            </button>
          {{/if}}
        {{else}}
          <button class="add" type="button" onclick="addDays(this, 1)">1J</button>
          {{mb_field object=$sejour field=sortie_prevue register=true form="editSortiePrevue-`$type`-`$aff_guid`" onchange="this.form.onsubmit()"}}
          {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
            {{mb_field object=$sejour field=mode_sortie onchange="\$V(this.form._modifier_sortie, 0); if (\$V(this) == 'deces') { showDateDeces('`$sejour->_id`'); } else { this.form.onsubmit(); }" hidden=true}}
            <select name="mode_sortie_id" class="{{$sejour->_props.mode_sortie_id}}" style="width: 15em" onchange="updateModeSortie(this)">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{foreach from=$list_mode_sortie item=_mode}}
                <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
                  {{$_mode}}
                </option>
              {{/foreach}}
            </select>
          {{else}}
            {{mb_field object=$sejour field="mode_sortie" onchange="if (\$V(this) == 'deces') { showDateDeces('`$sejour->_id`'); } else { this.form.onsubmit(); }"}}
          {{/if}}
          <button type="button" onclick="$V(this.form.confirme, 1); this.form.onsubmit()" class="tick">
            Autoriser
          </button>
          <br />
          <div id="listEtabExterne-editFrm{{$sejour->_guid}}" {{if $sejour->mode_sortie != "transfert"}} style="display: none;" {{/if}}>
            {{mb_field object=$sejour field="etablissement_sortie_id" form="editSortiePrevue-`$type`-`$aff_guid`" 
              autocomplete="true,1,50,true,true" onchange="this.form.onsubmit()"}}
          </div>
          <div id="dateDeces{{$sejour->_id}}" {{if $sejour->mode_sortie != "deces"}}style="display: none"{{/if}}>
            {{mb_label object=$patient field=deces}} : <input type="hidden" name="_date_deces" value="{{$patient->deces}}" onchange="this.form.onsubmit()"
            class="date progressive {{if $sejour->mode_sortie == "deces"}}notNull{{/if}}" />
          </div>
          <script type="text/javascript">
            Main.add(function() {
              Calendar.regProgressiveField(getForm('editSortiePrevue-{{$type}}-{{$aff_guid}}')._date_deces);
            });
          </script>
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