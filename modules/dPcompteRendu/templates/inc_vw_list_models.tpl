<table class="form" id="{{$class}}" style="display: none;">
  <tr>
    <th class="category">Mod�les du praticien</th>
    <th class="category">Mod�les de la fonction</th>
  </tr>
  <tr>
    <td style="text-align: center; width: 50%;">
      <select  style="width: 80%" id="modele_{{$class}}_prat" name="modele_{{$class}}_prat" onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}');" size="20">
      {{foreach from=$modeles.prat item=modele}}
        <option value="{{$modele->_id}}">{{$modele->nom}}</option>
      {{foreachelse}}
      	<option value="">{{tr}}CCompteRendu.none{{/tr}}</option>
      {{/foreach}}
      </select>
    </td>

    <td style="text-align: center; width: 50%;">
      <select style="width: 80%" id="modele_{{$class}}_func" name="modele_{{$class}}_func" onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}');" size="20">
      {{foreach from=$modeles.func item=modele}}
        <option value="{{$modele->_id}}">{{$modele->nom}}</option>
      {{foreachelse}}
      	<option value="">{{tr}}CCompteRendu.none{{/tr}}</option>
	    {{/foreach}}
      </select>
    </td>
  </tr>
</table>
