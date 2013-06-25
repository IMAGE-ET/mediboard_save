<!-- Plages -->
{{foreach from=$praticien->_ref_plages item=_plage}}
<hr />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      {{mb_include module=system template=inc_object_notes object=$_plage}}
      <a onclick="EditPlanning.order('{{$_plage->_id}}');" href="#" title="Agencer les interventions">
        {{$_plage->_ref_salle->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$_plage->debut|date_format:$conf.time}} � {{$_plage->fin|date_format:$conf.time}}
      </a>
    </th>
  </tr>
</table>

<table class="tbl">
  {{if $_plage->_ref_operations|@count}}
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
  {{/if}}
  
  {{if $_plage->_unordered_operations|@count}}
  <tr>
    <th colspan="10">Non plac�es</th>
  </tr>
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

<!-- D�plac�es -->
{{if $praticien->_ref_deplacees|@count}}
<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      D�plac�es
    </th>
  </tr>
</table>

<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$praticien->_ref_deplacees}}
</table>
{{/if}}

<!-- Urgences -->
{{if $praticien->_ref_urgences|@count}}
<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Hors plage
    </th>
  </tr>        
</table>

<table class="tbl">
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$praticien->_ref_urgences}}
</table>
{{/if}}