
{{mb_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
Main.add(function () {
  InseeFields.initCPVille("etabExterne", "cp", "ville","tel");
  
  var row = $("{{$etab_externe->_guid}}-row");
  
  if (row) {
    row.addUniqueClassName("selected");
  }
});
</script>

<button class="new" onclick="editCEtabExterne('0')">
  {{tr}}CEtabExterne-title-create{{/tr}}
</button>

<form name="etabExterne" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_etabExterne_aed" />
  <input type="hidden" name="etab_id" value="{{$etab_externe->_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      {{if $etab_externe->_id}}
      <th class="title text modify" colspan="2">
        {{mb_include module=system template=inc_object_idsante400 object=$etab_externe}}
        {{mb_include module=system template=inc_object_history object=$etab_externe}}
        Modification de l'�tablissement '{{$etab_externe->nom}}'
      {{else}}
      <th class="title" colspan="2">
        Cr�ation d'un �tablissement externe
      {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="nom"}}</th>
      <td>{{mb_field object=$etab_externe field="nom" tabindex="1" size=40}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="raison_sociale"}}</th>
      <td>{{mb_field object=$etab_externe field="raison_sociale" tabindex="2" size=40}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="adresse"}}</th>
      <td>{{mb_field object=$etab_externe field="adresse" tabindex="3"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$etab_externe field="cp"}}</th>
      <td>{{mb_field object=$etab_externe field="cp" tabindex="4"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$etab_externe field="ville"}}</th>
      <td>{{mb_field object=$etab_externe field="ville" tabindex="5"}}</td>
    </tr>
    
    
    <tr>
      <th>{{mb_label object=$etab_externe field="tel"}}</th>
      <td>{{mb_field object=$etab_externe field="tel" tabindex="6"}}</td>
    </tr>
    <tr>
       <th>{{mb_label object=$etab_externe field="fax"}}</th>
       <td>{{mb_field object=$etab_externe field="fax" tabindex="7"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="finess"}}</th>
      <td>{{mb_field object=$etab_externe field="finess" tabindex="8"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="siret"}}</th>
      <td>{{mb_field object=$etab_externe field="siret"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$etab_externe field="ape"}}</th>
      <td>{{mb_field object=$etab_externe field="ape"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
      {{if $etab_externe->_id}}
        <button class="modify" type="submit" name="modify">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'�tablissement',objName:'{{$etab_externe->nom|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button class="new" type="submit" name="create">
          {{tr}}Create{{/tr}}
        </button>
      {{/if}}
      </td>
    </tr>
  </table>
</form>