<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      <form name="provenance" action="?" method="get">
        <input type="hidden" name="m" value="stats" />
        <input type="hidden" name="tab" value="vw_patients_provenance" />
        Répartition des
        <select name="type" onchange="this.form.submit()">
          <option value="traitant" {{if $type == "traitant"}}selected="selected"{{/if}}>
            médecins traitants
          </option>
          <option value="adresse" {{if $type == "adresse"}}selected="selected"{{/if}}>
            médecins adressants
          </option>
          <option value="domicile" {{if $type == "domicile"}}selected="selected"{{/if}}>
            domiciles
          </option>
        </select>
        des patients hospitalisés en
        <select name="year" onchange="this.form.submit()">
          {{foreach from=$years item=_year}}
            <option value="{{$_year}}" {{if $_year == $year}}selected="selected"{{/if}}>
              {{$_year}}
            </option>
          {{/foreach}}
        </select>
      </form>
    </th>
  </tr>
  <tr>
    {{if $type != "domicile"}}
      <th>Correspondant</th>
    {{/if}}
    <th>Adresse</th>
    <th>Code Postal</th>
    <th>Nombre d'hospitalisations</th>
  </tr>
  {{foreach from=$listResult item=_result}}
    <tr>
      {{if $type != "domicile"}}
      {{if $_result.nom}}
        <td>{{$_result.nom}} {{$_result.prenom}}</td>
      {{else}}
        <td class="empty">Correspondant Inconnu</td>
      {{/if}}
      {{/if}}
      <td>
        {{if $_result.adresse}}
          {{$_result.adresse}}
        {{else}}
          <em>Inconnu</em>
        {{/if}}</td>
      <td>
        {{if $_result.cp}}
          {{$_result.cp}}
        {{else}}
          <em>Inconnu</em>
        {{/if}}
      </td>
      <td>{{$_result.total}}</td>
    </tr>
  {{/foreach}}
</table>