<form name="select_sejour_collectif">
  <table class="form tbl">
    <tr>
      <th class="title" colspan="9">
        <span style="text-align: left">({{$sejours|@count}})</span>
        Séjours SSR du {{$date|date_format:$conf.longdate}}
        <br/>contenant l'élément de prescription {{$element}}
      </th>
    </tr>

    <tr>
      <th>
        {{if $sejours|@count}}
          <input name="check_all_sejours" type="checkbox" onchange="Seance.selectSejours($V(this));"/>
        {{/if}}
      </th>
      <th style="width:  8em;">{{mb_label class=CAffectation field=lit_id}}</th>
      <th>{{mb_label class=CSejour field=patient_id}}</th>
      <th>{{mb_label class=CAffectation field=entree}}</th>
      <th>{{mb_label class=CAffectation field=sortie}}</th>
      <th>{{mb_label class=CSejour field=service_id}}</th>
      <th>{{mb_label class=CSejour field=libelle}}</th>
      <th>{{mb_label class=CSejour field=praticien_id}}</th>
      <th>{{mb_title class=CBilanSSR field=_kine_referent_id}} /{{mb_title class=CBilanSSR field=_kine_journee_id}}</th>
    </tr>

    {{foreach from=$sejours item=_sejour}}
      <tr>
        <td style="text-align: center;">
          {{assign var=sejour_guid value=$_sejour->_guid}}
          <input type="checkbox" name="_sejour_view_{{$_sejour->_id}}" class="sejour_collectif"
                 onchange="Seance.jsonSejours['{{$_sejour->_guid}}'].checked = (this.checked ? 1 : 0);Seance.checkCountSejours();"
            {{if isset($_sejours_guids.$sejour_guid|smarty:nodefaults) && $_sejours_guids.$sejour_guid.checked == 1}}
              checked="checked"
            {{/if}}
            />
          <script>
            var jsonLine = {checked : 0};
            Seance.jsonSejours["{{$_sejour->_guid}}"] = jsonLine;
            Seance.checkCountSejours();
          </script>
        </td>
        <td class="text">
          {{assign var=affectation value=$_sejour->_ref_curr_affectation}}
          {{if $affectation->_id}}
            {{$affectation->_ref_lit}}
          {{/if}}
        </td>
        <td class="text">
          {{mb_include template=inc_view_patient patient=$_sejour->_ref_patient}}
        </td>

        {{assign var=distance_class value=ssr-far}}
        {{if $_sejour->_entree_relative == "-1"}}
          {{assign var=distance_class value=ssr-close}}
        {{elseif $_sejour->_entree_relative == "0"}}
          {{assign var=distance_class value=ssr-today}}
        {{/if}}
        <td class="{{$distance_class}}">
          {{mb_value object=$_sejour field=entree format=$conf.date}}
          <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
        </td>

        {{assign var=distance_class value=ssr-far}}
        {{if $_sejour->_sortie_relative == "1"}}
          {{assign var=distance_class value=ssr-close}}
        {{elseif $_sejour->_sortie_relative == "0"}}
          {{assign var=distance_class value=ssr-today}}
        {{/if}}
        <td class="{{$distance_class}}">
          {{mb_value object=$_sejour field=sortie format=$conf.date}}
          <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
        </td>

        <td style="text-align: center;">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
       {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
      </span>

          {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
          <div class="opacity-60">
            {{if $bilan->hospit_de_jour}}
              <img style="float: right;" title="{{mb_value object=$bilan field=_demi_journees}}" src="modules/ssr/images/dj-{{$bilan->_demi_journees}}.png" />
            {{/if}}
            {{mb_value object=$_sejour field=service_id}}
          </div>
        </td>

        <td class="text">
          {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
          {{mb_value object=$_sejour field=libelle}}
          {{assign var=libelle value=$_sejour->libelle|upper|smarty:nodefaults}}
        </td>

        <td class="text">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
          {{assign var=prat_demandeur value=$bilan->_ref_prat_demandeur}}
          {{if $prat_demandeur->_id}}
            <br />{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$prat_demandeur}}
          {{/if}}
        </td>

        <td class="text">
          {{assign var=kine_referent value=$bilan->_ref_kine_referent}}
          {{if $kine_referent->_id}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_referent}}
            {{assign var=kine_journee value=$bilan->_ref_kine_journee}}
            {{if $kine_journee->_id != $kine_referent->_id}}
              <br/>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_journee}}
            {{/if}}
          {{/if}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="9" class="empty">
          {{tr}}CSejour.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="9" class="button">
        {{if $sejours|@count}}
          <button type="button" class="tick" onclick="Seance.addSejour();">Ajouter ces patients à la séance collective</button>
        {{/if}}
        <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>