<table class="tbl">
  <tr>
    <th>id</th>
    <th>Nom</th>
    <th>Cat�gorie</th>
  </tr>
  {{foreach from=$listItems item=curr_item}}
  <tr>
    <td>
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_item_id={{$curr_item->ei_item_id}}" title="Modifier l'item">
        {{$curr_item->ei_item_id}}
      </a>
    </td>
    <td class="text">
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_item_id={{$curr_item->ei_item_id}}" title="Modifier l'item">
        {{$curr_item->nom}}
      </a>
    </td>
    <td class="text">
      {{$curr_item->_ref_categorie->nom}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3">
      Actuellement, il n'y a aucun Item.
    </td>
  </tr>
  {{/foreach}}
</table><br />

{{if $item->ei_item_id}}
<a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_item_id=0">
  Cr�er un nouvel Item
</a>
{{/if}}
<form name="editCategorie" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_eiItem_aed" />
<input type="hidden" name="ei_item_id" value="{{$item->ei_item_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $item->ei_item_id}}
    <th colspan="2" class="category" style="color:#f00;">
      Modification de l'item : {{$item->nom}}
    {{else}}
    <th colspan="2" class="category">
      Cr�ation d'un nouvel item
    {{/if}}
    </th>
  </tr>
  <tr>
    <th>
      <label for="nom" title="Veuillez saisir un nom pour l'item">Nom de l'item</label>
    </th>
    <td>
      <input type="text" name="nom" value="{{$item->nom}}" title="{{$item->_props.nom}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="ei_categorie_id" title="Veuillez choisir une cat�gorie">Cat�gorie</label>
    </th>
    <td>
      <select name="ei_categorie_id" title="{{$item->_props.ei_categorie_id}}">
        <option value="">&mdash; Veuillez choisir une Cat�gorie</option>
        {{foreach from=$listCategories item=curr_cat}}        
        <option value="{{$curr_cat->ei_categorie_id}}"{{if $curr_cat->ei_categorie_id==$item->ei_categorie_id}} selected="selected"{{/if}}>
          {{$curr_cat->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">            
      {{if $item->ei_item_id}}
      <button class="modify" type="submit">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'item',objName:'{{$item->_view|escape:"javascript"}}'})">Supprimer</button>
      {{else}}
      <button class="submit" type="submit">Cr�er</button>
      {{/if}}
    </td>
  </tr>  
</table>
</form>