<form name="addOpFrm" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
  
  
  {{if !$consult_anesth->operation_id}}
  <!-- Choix du s�jour -->
  <select name="sejour_id" onchange="submitOpConsult()">
    <option value="">Pas de s�jour</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <option value="{{$curr_sejour->_id}}"{{if $consult_anesth->sejour_id==$curr_sejour->_id}} selected="selected"{{/if}}>
      S�jour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
    </option>
    {{/foreach}}
  </select>
  <br />

  {{else}}
  <!-- Choix de l'intervention -->
  {{mb_field object=$consult_anesth field="sejour_id" hidden=1}}
  {{/if}}
  <select name="operation_id" onchange="submitOpConsult()">
    <option value="">Pas d'Intervention</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <optgroup label="S�jour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}">
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      <option value="{{$curr_op->operation_id}}"{{if $consult_anesth->operation_id==$curr_op->_id}} selected="selected"{{/if}}>
        Le {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}} &mdash; Dr. {{$curr_op->_ref_chir->_view}}
      </option>
      {{/foreach}}
    </optgroup>
    {{/foreach}}
  </select>
  <br />

  {{assign var=sejour value=$consult_anesth->_ref_sejour}}
  {{if $sejour->_id}}
	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$sejour->_class_name}}', object_id: '{{$sejour->_id}}' } })">
  <strong>S�jour :</strong>
	  {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s) {{/if}}
	  {{mb_value object=$sejour field=type}}
	</span>
  <br />
  {{/if}}
  
  {{assign var=operation value=$consult_anesth->_ref_operation}}
  {{if $operation->_id}}
	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$operation->_class_name}}', object_id: '{{$operation->_id}}', view_tarif: true } })">
	  <strong>Intervention :</strong>
	  le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
	  par le <strong>Dr. {{$operation->_ref_chir->_view}}</strong>
    {{if $operation->libelle}}
    <em>[{{$operation->libelle}}]</em>
    {{/if}}
	</span>
  {{/if}}
</form>