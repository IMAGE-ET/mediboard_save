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
      <li><a href="#{{$_key_type}}_tab">{{tr}}CSejour.type.{{$_key_type}}{{/tr}}</a></li>
      {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      {{foreach from=$listInterv key=_key_type item=_services}}
      <div id="{{$_key_type}}_tab" style="display:none">
      <table class="tbl">
        <tr>
          <th>Chirurgien</th>
          <th>Patient</th>
          <th>Intervention</th>
          <th>Heure</th>
          <th>Chambre</th>
          <th>Consultation</th>
          <th colspan="2">Visite</th>
        </tr>
        {{foreach from=$_services key=_key_service item=_list_intervs}}
        {{if $_list_intervs|@count}}
        <tr>
          {{if $_key_service == "non_place"}}
          <th colspan="8">Non plac�s</th>
          {{else}}
          <th colspan="8">Service {{$services.$_key_service->_view}}</th>
          {{/if}}
        </tr>
        {{foreach from=$_list_intervs item=_operation}}
        <tr>
          <td class="text {{if $_operation->annulee}}cancelled{{/if}}">
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
          </td>
          <td class="text {{if $_operation->annulee}}cancelled{{/if}}">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
              {{$_operation->_ref_sejour->_ref_patient->_view}}
            </span>
          </td>
          <td {{if $_operation->annulee}}class="cancelled"{{/if}}">
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
          <td class="button {{if $_operation->annulee}}cancelled{{/if}}"">
            {{$_operation->time_operation|date_format:$conf.time}}
          </td>
          <td class="button {{if $_operation->annulee}}cancelled{{/if}}"">
            {{$_operation->_ref_affectation->_ref_lit->_view}}
          </td>
          <td class="text {{if $_operation->annulee}}cancelled{{/if}}"">
            {{if $_operation->_ref_consult_anesth->_id}}
            <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$_operation->_ref_consult_anesth->_ref_consultation->_id}}">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_consult_anesth->_guid}}')">
              Le {{mb_value object=$_operation->_ref_consult_anesth->_ref_consultation field="_date"}} par le Dr {{$_operation->_ref_consult_anesth->_ref_consultation->_ref_chir->_view}}
              </span>
            </a>
            {{else}}
              -
            {{/if}}
          </td>
          <td class="text {{if $_operation->annulee}}cancelled{{/if}} {{if !$_operation->date_visite_anesth}}empty{{/if}}">
            {{if $_operation->date_visite_anesth}}
              Le {{$_operation->date_visite_anesth|date_format:$conf.date}} par le Dr {{$_operation->_ref_anesth_visite->_view}}
            {{else}}
              Visite non effectu�e
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
      </table>
      </div>
      {{/foreach}}
    </td>
  </tr>
</table>