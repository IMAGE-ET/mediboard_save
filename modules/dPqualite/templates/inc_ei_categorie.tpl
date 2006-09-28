<table class="tbl">
  <tr>
    <th>id</th>
    <th>Nom</th>
  </tr>
  {{foreach from=$listCategories item=curr_cat}}
  <tr>
    <td>
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}" title="Modifier la cat�gorie">
        {{$curr_cat->ei_categorie_id}}
      </a>
    </td>
    <td class="text">
      <a href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id={{$curr_cat->ei_categorie_id}}" title="Modifier la cat�gorie">
        {{$curr_cat->nom}}
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="2">
      Actuellement, il n'y a aucune cat�gorie.
    </td>
  </tr>
  {{/foreach}}
</table><br />

{{if $categorie->ei_categorie_id}}
<a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_categorie_id=0">
  Cr�er une nouvelle Cat�gorie
</a>
{{/if}}
<form name="editCategorie" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_eiCategorie_aed" />
<input type="hidden" name="ei_categorie_id" value="{{$categorie->ei_categorie_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $categorie->ei_categorie_id}}
    <th colspan="2" class="category" style="color:#f00;">
      Modification de la cat�gorie : {{$categorie->nom}}
    {{else}}
    <th colspan="2" class="category">
      Cr�ation d'une nouvelle cat�gorie
    {{/if}}
    </th>
  </tr>
  <tr>
    <th>
      <label for="nom" title="Veuillez saisir un nom pour la cat�gorie">Nom de la cat�gorie</label>
    </th>
    <td>
      <input type="text" name="nom" value="{{$categorie->nom}}" title="{{$categorie->_props.nom}}" />
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">            
      {{if $categorie->ei_categorie_id}}
      <button class="modify" type="submit">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie',objName:'{{$categorie->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
      {{else}}
      <button class="submit" type="submit">Cr�er</button>
      {{/if}}
    </td>
  </tr>  
</table>
</form>