{{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
<table class="tbl">
<tr>
  <th class="title">
    {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
  </th>
</tr>
</table>


<table class="tbl" id="sejour{{$curr_sejour->sejour_id}}">

{{foreach from=$sejourNonAffectes item=curr_sejour}}
  <tr>
    <td class="patient">
      <strong>{{$curr_sejour->_ref_patient->_view}}</strong>
      {{if $curr_sejour->type != "ambu" && $curr_sejour->type != "exte"}}
      ({{$curr_sejour->_duree_prevue}}j)
      {{else}}
      ({{$curr_sejour->type|truncate:1:""|capitalize}})
      {{/if}}
      {{if $curr_sejour->_couvert_cmu}}
      <div style="float: right;"><strong>CMU</strong></div>
      {{/if}}
    </td>
    <td class="date"><em>Age</em> : {{$curr_sejour->_ref_patient->_age}} ans</td>
    <td class="date" style="background:#{{$curr_sejour->_ref_praticien->_ref_function->color}}"><em>Dr. {{$curr_sejour->_ref_praticien->_view}}</em></td>
    <td class="text">
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
      {{if $curr_operation->libelle}}
      <em>[{{$curr_operation->libelle}}]</em>
      <br />
      {{/if}}
      {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
      <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
      {{/foreach}}
      {{/foreach}}
    </td>
  </tr>      
  <tr>
    <td class="date">
      <em>Entr�e</em> : {{$curr_sejour->entree_prevue|date_format:"%A %d %B %H:%M"}}<br />
      <em>Sortie</em> : {{$curr_sejour->sortie_prevue|date_format:"%A %d %B %H:%M"}}
    </td>
    <td class="date" colspan="2" style="background-color: #ff5">
      {{if $curr_sejour->rques != ""}}
      <em>S�jour</em>: {{$curr_sejour->rques|nl2br}}<br />
      {{/if}}
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
      {{if $curr_operation->rques != ""}}
      <em>Intervention</em>: {{$curr_operation->rques|nl2br}}<br />
      {{/if}}
      {{/foreach}}
      {{if $curr_sejour->_ref_patient->rques}}
      <em>Patient</em>: {{$curr_sejour->_ref_patient->rques|nl2br}}<br />
      {{/if}}
    </td>
    <td class="date">
      <form name="EditSejour{{$curr_sejour->sejour_id}}" action="?m=dPhospi" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="otherm" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
        <em>Pathologie:</em>
        <select name="pathologie">
          <option value="">&mdash; Choisir &mdash;</option>
          {{foreach from=$pathos->_enumsTrans.categorie|smarty:nodefaults item=curr_patho}}
          <option {{if $curr_patho == $curr_sejour->pathologie}}selected="selected"{{/if}}>
          {{$curr_patho}}
          </option>
          {{/foreach}}
        </select>
        <br />
        <input type="radio" name="septique" value="0" {{if $curr_sejour->septique == 0}} checked="checked" {{/if}} />
        <label for="septique_0" title="Op�ration propre">Propre</label>
        <input type="radio" name="septique" value="1" {{if $curr_sejour->septique == 1}} checked="checked" {{/if}} />
        <label for="septique_1" title="S�jour septique">Septique</label>
        <button type="button" onclick="submitFormAjax(this.form, 'systemMsg')" class="submit">Valider</button>
      </form>
    </td>  
  </tr>
{{/foreach}}

</table>
<br />

{{/foreach}}