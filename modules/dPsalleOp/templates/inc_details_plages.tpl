<!-- Plages -->
{{foreach from=$salle->_ref_plages item=_plage}}
<hr />

<form name="anesth{{$_plage->_id}}" action="?" method="post">

<input type="hidden" name="m" value="dPbloc" />
<input type="hidden" name="otherm" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_plagesop_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_repeat" value="1" />
<input type="hidden" name="plageop_id" value="{{$_plage->_id}}" />
<input type="hidden" name="chir_id" value="{{$_plage->chir_id}}" />
<input type="hidden" name="spec_id" value="{{$_plage->spec_id}}" />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$_plage->_id}}" title="Administrer la plage">
        Chir : Dr {{$_plage->_ref_chir->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$_plage->debut|date_format:$dPconfig.time}} � {{$_plage->fin|date_format:$dPconfig.time}}
      </a>
    </th>
  </tr>
  
  <tr>
    {{if $vueReduite}}
    <th class="category" colspan="2">
      {{if $_plage->anesth_id}}
        Anesth : Dr {{$_plage->_ref_anesth->_view}}
      {{else}}
        -
      {{/if}}
    </th>
    {{else}}
    <th><label for="anesth_id" title="Anesth�siste associ� � la plage d'op�ration">Anesth�siste</label></th>
    <td>
      <select name="anesth_id" onchange="submit()">
        <option value="">&mdash; Choisir un anesth�siste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $_plage->anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
    {{/if}}
  </tr>
  
</table>

</form>

 <table class="tbl">
  {{if $_plage->_ref_operations}}
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
  {{/if}}

  {{if $_plage->_unordered_operations}}
  <tr>
    <th colspan="10">Non plac�es</th>
  </tr>
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

<!-- D�plac�es -->
{{if $salle->_ref_deplacees|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      D�plac�es
    </th>
  </tr>
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_deplacees}}
</table>
{{/if}}

<!-- Urgences -->
{{if $salle->_ref_urgences|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>
<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_urgences}}
</table>
{{/if}}