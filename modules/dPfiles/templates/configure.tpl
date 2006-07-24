<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;a=configure&amp;files_category_id=0">
        Cr�er une nouvelle cat�gorie de fichiers
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Cat�gorie</th>
          <th>Class</th>
        </tr>
        {{foreach from=$listCategory item=curr_category}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;a=configure&amp;files_category_id={{$curr_category->files_category_id}}" title="Modifier la cat�gorie">
              {{$curr_category->files_category_id}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;a=configure&amp;files_category_id={{$curr_category->files_category_id}}" title="Modifier le cat�gorie">
              {{$curr_category->nom}}
            </a>
          </td>
          <td class="text">
              {{$curr_category->class}}
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editCat" action="./index.php?m={{$m}}&amp;a=configure" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_filescategory_aed" />
	  <input type="hidden" name="files_category_id" value="{{$category->files_category_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->files_category_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification de la cat�gorie {{$category->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'une cat�gorie</th>
          {{/if}}
        </tr> 
        <tr>
          <th><label for="nom" title="Nom de la cat�gorie, obligatoire">Cat�gorie</label></th>
          <td><input name="nom" title="{{$category->_props.nom}}" type="text" value="{{$category->nom}}" /></td>
        </tr>
        <tr>
          <th><label for="class" title="Class de la cat�gorie">Class</label></th>
          <td>
            <select name="class">
            <option value="">&mdash; Toutes</option>
            {{foreach from=$listClass item=curr_listClass}}
            <option value="{{$curr_listClass}}"{{if $category->class==$curr_listClass}} selected="selected"{{/if}}>{{$curr_listClass}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>        
        <tr>
          <td class="button" colspan="2">
            {{if $category->files_category_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie',objName:'{{$category->_view|escape:javascript}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>