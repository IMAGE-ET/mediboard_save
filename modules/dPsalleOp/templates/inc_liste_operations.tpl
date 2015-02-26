{{mb_default var="redirect_tab" value="0"}}
{{mb_default var="ajax_salle" value="0"}}

<!-- Ent�tes -->
<tr>
  {{if $urgence && $salle}}
    <th>Praticien</th>
  {{else}}
    <th>Heure</th>
  {{/if}}
  <th>Patient</th>
  <th>Interv</th>
  <th>Cot�</th>
  {{if !$vueReduite}}
    <th>Dur�e</th>
  {{/if}}
</tr>

{{assign var=systeme_materiel value=$conf.dPbloc.CPlageOp.systeme_materiel}}
{{assign var=save_op value=$operations|@reset}}

{{foreach from=$operations item=_operation name=ops}}
  {{if $conf.dPsalleOp.COperation.modif_salle}}
    {{assign var="rowspan" value=2}}
  {{else}}
    {{assign var="rowspan" value=1}}
  {{/if}}

  {{if !$_operation->annulee}}
    {{if $smarty.foreach.ops.index > 0}}
      {{assign var=preop_curr value=$_operation->presence_preop}}
      {{assign var=timeop_curr value=$_operation->time_operation}}
      {{assign var=postop_save value=$save_op->presence_postop}}
      {{assign var=timeop_save value=$save_op->time_operation}}
      {{assign var=tempop_save value=$save_op->temp_operation}}

      {{assign var=intervalle_a value="CMbDT::addTime"|static_call:$postop_save:$tempop_save}}
      {{assign var=intervalle_a value="CMbDT::addTime"|static_call:$intervalle_a:$timeop_save}}

      {{assign var=intervalle_b value="CMbDT::subTime"|static_call:$preop_curr:$timeop_curr}}

      {{if $intervalle_a < $intervalle_b}}
        <tr>
          <th colspan="5" class="section">
            {{assign var=time_pause value="CMbDT::timeRelative"|static_call:$intervalle_a:$intervalle_b:"%02dh%02d"}}
            [PAUSE] ({{$time_pause}})
          </th>
        </tr>
      {{/if}}
      {{assign var=save_op value=$_operation}}
    {{/if}}
    <tbody class="hoverable">
      <tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
        {{if $_operation->_deplacee && $_operation->salle_id != $salle->_id}}
          <td class="text" rowspan="{{$rowspan}}" style="background-color:#ccf">
        {{elseif $_operation->entree_salle && $_operation->sortie_salle}}
          <td class="text" rowspan="{{$rowspan}}" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
        {{elseif $_operation->entree_salle}}
          <td class="text" rowspan="{{$rowspan}}" style="background-color:#cfc">
        {{elseif $_operation->sortie_salle}}
          <td class="text" rowspan="{{$rowspan}}" style="background-color:#fcc">
        {{elseif $_operation->entree_bloc}}
          <td class="text" rowspan="{{$rowspan}}" style="background-color:#ffa">
        {{else}}
          <td class="text" rowspan="{{$rowspan}}">
        {{/if}}
          {{if $salle}}
            <a {{if $redirect_tab}}
                href="?m=salleOp&tab=vw_operations&operation_id={{$_operation->_id}}&salle={{$salle->_id}}"
              {{else}}
                href="#1" onclick="loadOperation('{{$_operation->_id}}', this.up('tr'))"
              {{/if}}
              title="Coder l'intervention">
          {{/if}}
            {{if ($urgence && $salle) || $_operation->_ref_plageop->spec_id}}
              {{$_operation->_ref_chir}}
            {{if $vueReduite && $_operation->_ref_anesth->_id}}
              {{$_operation->_ref_anesth}} -
            {{/if}}
            {{/if}}
            {{if $_operation->time_operation != "00:00:00" && !$_operation->entree_salle}}
              {{$_operation->time_operation|date_format:$conf.time}}
              <br />
              {{$_operation->_fin_prevue|date_format:$conf.time}}
            {{elseif $_operation->entree_salle}}
              {{$_operation->entree_salle|date_format:$conf.time}}
              {{if $_operation->sortie_salle}}
                <br />
                {{$_operation->sortie_salle|date_format:$conf.time}}
              {{/if}}
            {{else}}
              NP
            {{/if}}
            {{if $vueReduite && $urgence && $salle}}
              ({{$_operation->temp_operation|date_format:$conf.time}})

              {{if $conf.dPbloc.CPlageOp.view_prepost_suivi}}
                {{if $_operation->presence_preop}}
                  <div>
                    Pr�-op : {{$_operation->presence_preop|date_format:$conf.time}}
                  </div>
                {{/if}}
                {{if $_operation->presence_postop}}
                  <div>
                    Post-op : {{$_operation->presence_postop|date_format:$conf.time}}
                  </div>
                {{/if}}
              {{/if}}
            {{/if}}
          {{if $salle}}
            </a>
          {{/if}}

          {{if $_operation->_ref_chirs|@count > 1}}
            <span class="noteDiv" onmouseover="ObjectTooltip.createDOM(this, 'chirs_{{$_operation->_guid}}');">
              <button class="user notext">Chirurgiens multiples</button>
              <span class="countertip" style="margin-top:2px;">
                {{$_operation->_ref_chirs|@count}}
              </span>
            </span>
            <div style="display:none;" id="chirs_{{$_operation->_guid}}">
              <table class="main form">
                <tr>
                  <th colspan="2" class="title">
                    Intervention
                    {{if !$_operation->plageop_id}}[HP]{{/if}}
                    le {{$_operation->_datetime|date_format:$conf.date}}
                    {{if $_operation->_ref_patient}}de {{$_operation->_ref_patient}}{{/if}}</th>
                </tr>
                {{foreach from=$_operation->_ref_chirs key=key_chir item=_chir}}
                  <tr>
                    <th>{{mb_label object=$_operation field=$key_chir}}</th>
                    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_chir}}</td>
                  </tr>
                {{/foreach}}
                {{if $_operation->_ref_anesth->_id}}
                  <tr>
                    <td colspan="2"><hr style="width: 50%;"/></td>
                  </tr>
                  <tr>
                    <th>{{mb_label object=$_operation field=anesth_id}}</th>
                    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_anesth}}</td>
                  </tr>
                {{/if}}
              </table>
            </div>
          {{/if}}

          {{if $vueReduite && $systeme_materiel == "expert"}}
            <button class="print notext not-printable" onclick="printFicheBloc({{$_operation->_id}})">{{tr}}Print{{/tr}}</button>
            {{if $urgence && $salle && $_operation->_ref_besoins|@count}}
              <img src="style/mediboard/images/icons/equipement.png" onmouseover="ObjectTooltip.createDOM(this, 'besoins_{{$_operation->_id}}')"/>
              <div id="besoins_{{$_operation->_id}}" style="display: none;">
                {{tr}}CBesoinRessource.all{{/tr}} :
                <ul>
                  {{foreach from=$_operation->_ref_besoins item=_besoin}}
                   <li>
                     {{$_besoin->_ref_type_ressource}}
                   </li>
                  {{/foreach}}
                </ul>
              </div>
            {{/if}}
          {{/if}}
        </td>

        {{if $_operation->_deplacee && $_operation->salle_id != $salle->_id}}
          <td class="text" colspan="5">
            <div class="warning">
              <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
                    onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
                {{$_operation->_ref_patient}}
                {{if $vueReduite}}
                  ({{$_operation->_ref_patient->_age}})
                {{/if}}
              </span>
              <br />
              Intervention d�plac�e vers {{$_operation->_ref_salle}}
            </div>
          </td>
        {{else}}
          <td class="text">
            {{if $salle}}
              <a {{if $redirect_tab}}
                  href="?m=salleOp&tab=vw_operations&operation_id={{$_operation->_id}}&salle={{$salle->_id}}"
                {{else}}
                  href="#1" onclick="loadOperation('{{$_operation->_id}}', this.up('tr'))"
                {{/if}}>
            {{/if}}
            <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
              {{$_operation->_ref_patient}}
              {{if $vueReduite}}
                ({{$_operation->_ref_patient->_age}})
              {{/if}}
            </span>
            {{if $_operation->_ref_affectation && $_operation->_ref_affectation->_ref_lit && $_operation->_ref_affectation->_ref_lit->_id && $conf.dPbloc.CPlageOp.chambre_operation == 1}}
              <div style="font-size: 0.9em; white-space: nowrap; border: none;">
                {{$_operation->_ref_affectation->_ref_lit->_ref_chambre->_ref_service}} &rarr; {{$_operation->_ref_affectation->_ref_lit}}
              </div>
            {{/if}}
            {{if $salle}}
              </a>
            {{/if}}
          </td>

          <td class="text">
            {{mb_ternary var=direction test=$urgence value=vw_edit_urgence other=vw_edit_planning}}
            {{if $salle}}
              <a {{if $redirect_tab}}
                  href="?m=salleOp&tab=vw_operations&operation_id={{$_operation->_id}}&salle={{$salle->_id}}"
                {{else}}
                  href="#1" onclick="loadOperation('{{$_operation->_id}}', this.up('tr'))"
                {{/if}}
                {{if $_operation->_count_actes == "0"}}style="border-color: #F99" class="mediuser"{{/if}}>
            {{/if}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')" >
            {{if $_operation->libelle}}
              {{$_operation->libelle}}
            {{else}}
              {{foreach from=$_operation->_ext_codes_ccam_princ item=_code}}
                {{$_code->code}}
              {{/foreach}}
            {{/if}}
            {{if $_operation->_ref_sejour->type == "comp" && $vueReduite}}
              ({{$_operation->_ref_sejour->type|truncate:1:""|capitalize}} {{$_operation->_ref_sejour->_duree}}j)
            {{else}}
               ({{$_operation->_ref_sejour->type|truncate:1:""|capitalize}})
            {{/if}}
            </span>
            <br/>
            {{if $conf.dPbloc.CPlageOp.view_tools && $_operation->_ref_besoins|@count && $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
              <strong>Mat.</strong> :
              {{foreach from=$_operation->_ref_besoins item=_matos name=matos}}
                {{$_matos->_ref_type_ressource}}
                {{if !$smarty.foreach.matos.last}}, {{/if}}
              {{/foreach}}<br/>
            {{/if}}
            {{if $conf.dPbloc.CPlageOp.view_required_tools && $_operation->materiel}}
              <strong>Mat. � P.</strong> : {{$_operation->materiel}}<br/>
            {{/if}}
            {{if $conf.dPbloc.CPlageOp.view_required_tools && $_operation->exam_per_op}}
              <strong>Exam per-op</strong> : {{$_operation->exam_per_op}}<br/>
            {{/if}}
            {{if $conf.dPbloc.CPlageOp.view_anesth_type && $_operation->type_anesth}}
              <strong>T Anesth.</strong> : {{mb_value object=$_operation field=type_anesth}}<br/>
            {{/if}}
            {{if $conf.dPbloc.CPlageOp.view_rques && $_operation->rques}}
              <strong>Rques</strong> : {{mb_value object=$_operation field=rques}}<br/>
            {{/if}}

            {{if $salle}}
              </a>
            {{/if}}
          </td>
          <td>
            {{if $conf.dPplanningOp.COperation.verif_cote && ($_operation->cote == "droit" || $_operation->cote == "gauche") && !$vueReduite}}
              <form name="editCoteOp{{$_operation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_planning_aed" />
                {{mb_key object=$_operation}}
                {{mb_field emptyLabel="COperation-cote_bloc" object=$_operation field="cote_bloc" onchange="return onSubmitFormAjax(this.form);"}}
              </form>
            {{else}}
            {{mb_value object=$_operation field="cote"}}
          {{/if}}
          </td>
          {{if !$vueReduite}}
            <td {{if $_operation->_presence_salle}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
              {{if $_operation->_presence_salle}}
                {{$_operation->_presence_salle|date_format:$conf.time}}
              {{else}}
                {{$_operation->temp_operation|date_format:$conf.time}}
              {{/if}}
            </td>
          {{/if}}
        {{/if}}
      </tr>

      {{if $conf.dPsalleOp.COperation.modif_salle && !($_operation->_deplacee && $_operation->salle_id != $salle->_id) && @$allow_moves|default:1}}
        <tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
          <td colspan="5" class="not-printable">
            <form name="changeSalle{{$_operation->_id}}" action="?m={{$m}}" method="post" {{if $ajax_salle}}onsubmit="return onSubmitFormAjax(this, {onComplete: window.updateSuiviSalle || window.refreshListOp});" {{/if}}>
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
            <select name="salle_id" onchange="this.form.{{if $ajax_salle}}on{{/if}}submit();">
              {{if $urgence && !$_operation->salle_id}}
                <option value="">&mdash; Choisir une salle</option>
              {{/if}}
              {{foreach from=$listBlocs item=curr_bloc}}
              <optgroup label="{{$curr_bloc->nom}}">
                {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $_operation->salle_id}}selected="selected"{{/if}}>
                  {{$curr_salle->nom}}
                </option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
            </form>
          </td>
        </tr>
      {{/if}}
    </tbody>
  {{/if}}
{{/foreach}}
