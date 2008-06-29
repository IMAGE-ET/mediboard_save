<form name="anesthTiming" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
<input type="hidden" name="del" value="0" />
      
<table class="form">
  <tr>
   <th class="category" colspan="2">Anesth�sie</th>
  </tr>
  
  <!-- Choix anesth�siste et type anesth�sie -->
  <tr>
    <td rowspan="2" style="vertical-align: middle;">
      {{if $can->edit || $modif_operation}}
      <select name="type_anesth" onchange="submitAnesth(this.form);">
        <option value="">&mdash; Type d'anesth�sie</option>
        {{foreach from=$listAnesthType item=curr_anesth}}
        <option value="{{$curr_anesth->type_anesth_id}}" {{if $selOp->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
          {{$curr_anesth->name}}
        </option>
       {{/foreach}}
      </select>
      {{elseif $selOp->type_anesth}}
        {{assign var="keyAnesth" value=$selOp->type_anesth}}
        {{assign var="typeAnesth" value=$listAnesthType.$keyAnesth}}
        {{$typeAnesth->name}}
      {{else}}-{{/if}}
      <br />par le Dr
      {{if $can->edit || $modif_operation}}
      <select name="anesth_id" onchange="submitAnesth(this.form);">
        <option value="">&mdash; Anesth�siste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
      {{elseif $selOp->_ref_anesth->user_id}}
        {{assign var="keyChir" value=$selOp->_ref_anesth->user_id}}
        {{assign var="typeChir" value=$listAnesths.$keyChir}}
        {{$typeChir->_view}}
      {{else}}-{{/if}}
    </td>
    {{include file=inc_field_timing.tpl object=$selOp field=induction_debut submit=submitAnesth}}
  </tr>
  <tr>
    {{include file=inc_field_timing.tpl object=$selOp field=induction_fin submit=submitAnesth}}
  </tr>
</table>
  
</form>
