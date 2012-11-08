<script type="text/javascript">
function printFicheAnesth(consult_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consult_id);
  url.popup(700, 500, "printFiche");
  return;
}

function editVisite(operation_id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "edit_visite_anesth"); 
  url.addParam("operation_id", operation_id);
  url.popup(800, 500, "editVisite");
  return;
}

Main.add(function(){
  Calendar.regField(getForm("selectPraticien").date, null, {noView: true});
 if ($('type_sejour')){
    Control.Tabs.create('type_sejour', true);
  }
});
</script>

<table class="main">
  <tr>
    <th>
      <form action="?" name="selectPraticien" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <label for="selPrat">Praticien</label>
        <select name="selPrat" onchange="this.form.submit()" style="max-width: 150px;">
          <option value="-1">&mdash; Choisir un praticien</option>
          {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $selPrat}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
          {{/foreach}}
        </select>
        - Interventions du {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_planning" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        <input type="hidden" name="sans_anesth" value="{{$sans_anesth}}"/>
        <input type="hidden" name="canceled" value="{{$canceled}}"/>
        <label>
          <input type="checkbox" name="_canceled" {{if $canceled}}checked="checked"{{/if}}
            onclick="$V(this.form.canceled, this.checked ? 1 : 0); this.form.submit()"/>
            Afficher les annul�es
        </label>
        <label>
          <input type="checkbox" name="_sans_anesth" {{if $sans_anesth}}checked="checked"{{/if}}
            onclick="$V(this.form.sans_anesth, this.checked ? 1 : 0); this.form.submit()"/>
            Inclure les interventions sans anesth�siste
        </label>
      </form>
    </th>
  </tr>
  <tr>
    <td>
      <ul id="type_sejour" class="control_tabs">
      {{foreach from=$listInterv key=_key_type item=_services}}
        {{assign var=count_ops_by_type value=$count_ops.$_key_type}}
        <li>
          <a href="#{{$_key_type}}_tab" {{if $count_ops_by_type == 0}}class="empty"{{/if}}>
            {{tr}}CSejour.type.{{$_key_type}}{{/tr}}
            <small>({{$count_ops_by_type}})</small>
          </a>
        </li>
      {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      {{foreach from=$listInterv key=_key_type item=_services}}
      {{assign var=count_ops_by_type value=$count_ops.$_key_type}}
      <div id="{{$_key_type}}_tab" style="display:none">
      <table class="tbl">
        <tr>
          <th>Chirurgien</th>
          <th colspan="2">Patient</th>
          <th>Intervention</th>
          <th>Heure</th>
          <th>Chambre</th>
          <th>Consultation</th>
          <th>Anesth�sie</th>
          <th>ASA</th>
          <th colspan="2">Visite</th>
        </tr>
        {{if $count_ops_by_type == 0}}
          <tr>
            <td colspan="8" class="empty">
              {{tr}}COperation.none{{/tr}}
            </td>
          </tr>
        {{else}}
          {{foreach from=$_services key=_key_service item=_list_intervs}}
            {{if $_list_intervs|@count}}
              <tr>
                {{if $_key_service == "non_place"}}
                <th colspan="11" class="section">Non plac�s</th>
                {{else}}
                <th colspan="11" class="section">Service {{$services.$_key_service}}</th>
                {{/if}}
              </tr>
              {{foreach from=$_list_intervs item=_operation}}
              <tr>
                <td class="text {{if $_operation->annulee}} cancelled{{/if}}" style="text-align: left;">
                  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
                </td>
                <td class="text {{if $_operation->annulee}} cancelled{{/if}}" style="text-align: left;">
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
                    {{$_operation->_ref_sejour->_ref_patient}}
                  </span>
                </td>
                <td style="text-align: center;">
                  {{assign var=constantes value=$_operation->_ref_sejour->_ref_patient->_ref_constantes_medicales}}
                  {{if $constantes->poids}} {{$constantes->poids}}kg{{else}}-{{/if}}
                  <br />
                  {{if $constantes->taille}} {{$constantes->taille}}cm{{else}}-{{/if}}
                </td>
                <td class="text {{if $_operation->annulee}} cancelled {{/if}}" style="text-align: left;">
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
                  {{if $_operation->libelle}}
                    {{$_operation->libelle}}
                  {{else}}
                    {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
                      {{$curr_code->code}}
                    {{/foreach}}
                  {{/if}}
                  </span>
                </td>
                <td class="button {{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center;">
                  {{$_operation->time_operation|date_format:$conf.time}}
                </td>
                <td class="button {{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center;">
                  {{$_operation->_ref_affectation->_ref_lit->_view}}
                </td>
                {{assign var=dossier_anesth value=$_operation->_ref_consult_anesth}}
                {{assign var=consult_anesth value=$dossier_anesth->_ref_consultation}}

                <td class="{{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center;">
                  {{if $dossier_anesth->_id}}
                    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_chir initials=border}}
                    <a class="action" href="?m=cabinet&amp;tab=edit_consultation&amp;selConsult={{$consult_anesth->_id}}">
                      <span onmouseover="ObjectTooltip.createEx(this, '{{$dossier_anesth->_guid}}')">
                      le {{mb_value object=$consult_anesth field="_date"}} 
                      </span>
                    </a>
                  {{else}}
                    <div class="empty">non effectu�e</div>
                    
                  {{/if}}
                </td>

                <td class="{{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center;">
                  {{$_operation->_ref_type_anesth}}
                </td>

                <td class="{{if $_operation->annulee}}cancelled{{/if}}" style="text-align: center;";>
                  {{if $dossier_anesth->ASA}}
                    <strong>{{$dossier_anesth->ASA[0]}}</strong>
                  {{else}}
                    -
                  {{/if}}
                </td>

                <td class="{{if $_operation->annulee}}cancelled{{/if}} {{if !$_operation->date_visite_anesth}}empty{{/if}}">
                  {{if $_operation->date_visite_anesth}}
                    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_anesth_visite initials=border}}
                    le {{$_operation->date_visite_anesth|date_format:$conf.date}}
                  {{else}}
                    non effectu�e
                  {{/if}}
                </td>
                <td {{if $_operation->annulee}}class="cancelled"{{/if}}">
                  <button type="button" class="edit notext" onclick="editVisite({{$_operation->_id}});">{{tr}}Edit{{/tr}}</button>
                  {{if $_operation->_ref_consult_anesth->_id}}
                    <button type="button" class="print notext" onclick="printFicheAnesth('{{$_operation->_ref_consult_anesth->_ref_consultation->_id}}');">{{tr}}Print{{/tr}}</button>
                  {{/if}}
                </td>
              </tr>
              {{/foreach}}
            {{/if}}
          {{/foreach}}
        {{/if}}
      </table>
      </div>
      {{/foreach}}
    </td>
  </tr>
</table>