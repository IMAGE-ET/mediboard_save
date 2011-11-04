<script type="text/javascript">
function checkPlage() {
  var form = getForm('editFrm');
  
  if (!checkForm(form)) {
    return false;
  }
    
  if (form.chir_id.value == "" && form.spec_id.value == "") {
    alert("Merci de choisir un chirurgien ou une sp�cialit�");
    form.chir_id.focus();
    return false;
  }
  
  return true;
}
Main.add(function(){
  var oForm = getForm('editFrm');
  Calendar.regField(oForm.date);
  Calendar.regField(oForm.temps_inter_op);
  var options = {
    exactMinutes: false, 
    minInterval: {{"CPlageOp"|static:minutes_interval}},
    minHours: {{"CPlageOp"|static:hours_start}},
    maxHours: {{"CPlageOp"|static:hours_stop}}
  };
  Calendar.regField(oForm.debut, null, options);
  Calendar.regField(oForm.fin, null, options);
});
</script>

{{mb_script module=dPbloc script=edit_planning}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkPlage()" class="{{$plagesel->_spec}}">
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" />

  <table class="form" id="modif_planning">
    <tr>
      {{if $plagesel->plageop_id}}
      <th class="title modify" colspan="6">
        
        {{mb_include module=system template=inc_object_idsante400 object=$plagesel}}
        {{mb_include module=system template=inc_object_history object=$plagesel}}
        {{tr}}CPlageOp-title-modify{{/tr}}
      {{else}}
      <th class="title" colspan="6">
        {{tr}}CPlageOp-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
    <tr>
      <td colspan="6" class="text">
        <div class="small-info">
          <strong>La gestion des plages a �volu� :</strong>
          <ul>
            <li>La r�p�tition est maintenant indiqu�e en nombre de semaines et plus en nombre de plages</li>
            <li>
              la supression d'une plage est int�gr�e au formulaire principal : il faut utiliser le nombre
              de semaines et le type de r�p�titions pour indiquer les supressions � effectuer, puis cliquer
              directement sur le bouton supprimer
            </li>
          </ul>
        </div>
      </td>
    </tr>
    <tr>
     <th>{{mb_label object=$plagesel field="chir_id"}}</th>
     <td>
      <select name="chir_id" class="{{$plagesel->_props.chir_id}}" style="width: 15em;">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{if $chirs|@count}}
        <optgroup label="Chirurgiens">
        </optgroup>
        {{foreach from=$chirs item=_chir}}
          <option class="mediuser" style="border-color: #{{$_chir->_ref_function->color}};" value="{{$_chir->user_id}}" 
          {{if $plagesel->chir_id == $_chir->user_id}}selected="selected"{{/if}}>
            {{$_chir->_view}}
          </option>
        {{/foreach}}
        {{/if}}
        {{if $anesths|@count}}
        <optgroup label="Anesth�sistes"></optgroup>
        {{foreach from=$anesths item=_anesth}}
          <option class="mediuser" style="border-color: #{{$_anesth->_ref_function->color}};" value="{{$_anesth->user_id}}" 
          {{if $plagesel->chir_id == $_anesth->user_id}}selected="selected"{{/if}}>
            {{$_anesth->_view}}
          </option>
        {{/foreach}}
        {{/if}}
      </select>
    </td>
    <th>{{mb_label object=$plagesel field="salle_id"}}</th>
    <td>
      <select name="salle_id" class="{{$plagesel->_props.salle_id}}" style="width: 15em;">
        <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
        {{if $plagesel->_id}}
          {{foreach from=$listBlocs item=curr_bloc}}
            <optgroup label="{{$curr_bloc->_view}}">
            {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
              <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                {{$curr_salle}}
              </option>
            {{foreachelse}}
              <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
            {{/foreach}}
            </optgroup>
          {{/foreach}}
        {{else}}
          {{foreach from=$bloc->_ref_salles item=curr_salle}}
            <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
              {{$curr_salle}}
            </option>
          {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
          {{/foreach}}
        {{/if}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$plagesel field="spec_id"}}</th>
    <td>
      <select name="spec_id" class="{{$plagesel->_props.spec_id}}" style="width: 15em;">
        <option value="">&mdash; Choisir une sp�cialit�</option>
        {{foreach from=$specs item=spec}}
          <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
          {{if $spec->function_id == $plagesel->spec_id}}selected="selected"{{/if}}>
            {{$spec->text}}
          </option>
        {{/foreach}}
      </select>
    </td>
    <th>{{mb_label object=$plagesel field="date"}}</th>
    <td>
      {{if $plagesel->plageop_id}}
      <input type="hidden" name="date" value="{{$plagesel->date}}" />
      {{else}}
      <input type="hidden" name="date" value="{{$date}}" />
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$plagesel field="anesth_id"}}</th>
    <td>
      <select name="anesth_id" style="width: 15em;">
        <option value="">&mdash; Choisir un anesth�siste</option>
        {{foreach from=$anesths item=_anesth}}
        <option class="mediuser" style="border-color: #{{$_anesth->_ref_function->color}};" value="{{$_anesth->user_id}}" {{if $plagesel->anesth_id == $_anesth->user_id}} selected="selected" {{/if}} >
          {{$_anesth->_view}}
        </option>
        {{/foreach}}
    </select>
    </td>
    <th>{{mb_label object=$plagesel field="debut"}}</th>
    <td>{{mb_field object=$plagesel field="debut"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$plagesel field="unique_chir"}}</th>
    <td>{{mb_field object=$plagesel field="unique_chir"}}</td>
    <th>{{mb_label object=$plagesel field="fin"}}</th>
    <td>{{mb_field object=$plagesel field="fin" }}</td>
  </tr>
  <tr>
    <th>
      <label for="_repeat" title="Nombre de semaines de r�p�tition">Nombre de semaines</label>
    </th>
    <td>
      <input type="text" class="notNull num min|1" name="_repeat" size="1" value="1" />
    </td>
    <th>{{mb_label object=$plagesel field="temps_inter_op"}}</th>
    <td>{{mb_field object=$plagesel field="temps_inter_op"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$plagesel field="_type_repeat"}}</th>
    <td>{{mb_field object=$plagesel field="_type_repeat" style="width: 15em;" typeEnum="select"}}</td>
    <th>{{mb_label object=$plagesel field="delay_repl"}}</th>
    <td>{{mb_field object=$plagesel field="delay_repl" size=1 increment=true form="editFrm" min=0}} jours</td>
  </tr>
  <tr>
    <th>{{mb_label object=$plagesel field="max_intervention"}}</th>
    <td>{{mb_field object=$plagesel field="max_intervention" size=1 increment=true form="editFrm" min=0}}</td>
    <th>{{mb_label object=$plagesel field="spec_repl_id"}}</th>
    <td>
      <select name="spec_repl_id" class="{{$plagesel->_props.spec_repl_id}}" style="width: 15em;">
        <option value="">&mdash; Sp�cialit� de remplacement</option>
        {{foreach from=$specs item=spec}}
          <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
          {{if $spec->function_id == $plagesel->spec_repl_id}}selected="selected"{{/if}}>
            {{$spec->text}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="4" class="text">
      <div class="small-info">
        Pour modifier plusieurs plages (nombre de semaines > 1),
        veuillez ne pas changer les champs d�but et fin en m�me temps
      </div>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4">
    {{if $plagesel->plageop_id}}
      <button type="submit" class="modify">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la plage op�ratoire',objName:'{{$plagesel->_view|smarty:nodefaults|JSAttribute}}'})">
        Supprimer
      </button>
    {{else}}
      <button type="submit" class="new">Ajouter</button>
    {{/if}}
    </td>
  </tr>
</table>
</form>