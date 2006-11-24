<form name="addOpFrm" action="?m={{$m}}" method="post">
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <select name="operation_id" onchange="submitOpConsult()">
        <option value="">Pas d'Intervention</option>
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        <optgroup label="S�jour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}">
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <option value="{{$curr_op->operation_id}}"{{if $consult_anesth->operation_id==$curr_op->operation_id}} selected="selected"{{/if}}>Le {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}} &mdash; Dr. {{$curr_op->_ref_chir->_view}}</option>
        {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
</form>
{{if $consult_anesth->operation_id}}
  Intervention le <strong>{{$consult_anesth->_ref_operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
  par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong> (cot� {{tr}}COperation.cote.{{$consult_anesth->_ref_operation->cote}}{{/tr}})<br />
  <ul>
    {{if $consult_anesth->_ref_operation->libelle}}
    <li><em>[{{$consult_anesth->_ref_operation->libelle}}]</em></li>
    {{/if}}
    {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
    <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}})</li>
    {{/foreach}}
  </ul>
{{/if}}
<br />
<form name="EditPosFrm" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
<label for="position" title="Veuillez choisir la position du patient">Position</label>

<select name="position" onchange="submitFormAjax(this.form, 'systemMsg')">
<option value="">&mdash; Veuillez Choisir</option>
{{foreach from=$consult_anesth->_enumsTrans.position item=curr_pos key=key_pos}}
<option value="{{$key_pos}}" {{if $key_pos == $consult_anesth->position}} selected="selected" {{/if}} >
  {{$curr_pos}}
</option>
{{/foreach}}
</select>

{{if $consult_anesth->operation_id}}
 - Type d'admission : <strong>{{tr}}CSejour.type.{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}</strong>{{if $consult_anesth->_ref_operation->_ref_sejour->type!="ambu" && $consult_anesth->_ref_operation->_ref_sejour->type!="exte"}} {{$consult_anesth->_ref_operation->_ref_sejour->_duree_prevue}} jour(s){{/if}}
{{/if}}
</form>