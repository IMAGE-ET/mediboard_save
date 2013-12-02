<table class="main tbl">
  <tr>
    <th class="category" style="width: 50%;">
      {{mb_label class=CReglement field=mode}}
      ({{mb_label class=CReglement field=banque_id}})
    </th>
    <th class="category">{{mb_label class=CReglement field=reference}}</th>
    <th class="category">{{mb_label class=CReglement field=tireur}}</th>
    <th class="category narrow">{{mb_label class=CReglement field=montant}}</th>
    <th class="category narrow">{{mb_label class=CReglement field=date}}</th>
  </tr>
  
  <!--  Liste des reglements deja effectu�s -->
  {{foreach from=$object->_ref_reglements item=_reglement}}
  <tr>
    <td>
      {{mb_value object=$_reglement field=mode}}
      {{if $_reglement->_ref_banque->_id}}
        ({{$_reglement->_ref_banque}})
      {{/if}}
    </td>
    <td>{{mb_value object=$_reglement field=reference}}</td>
    <td>{{mb_value object=$_reglement field=tireur}}</td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_reglement->_guid}}');">
        {{mb_value object=$_reglement field=montant}}
      </span> 
    </td>
    <td> {{mb_value object=$_reglement field=date format="%d/%m/%Y"}} </td>
  </tr>
  {{/foreach}}
  <tr>
    <td colspan="5" style="text-align: center;">
      <strong> Aucun r�glement � percevoir du patient </strong>
    </td>
  </tr>
</table>