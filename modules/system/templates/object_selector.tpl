<script type="text/javascript">

function setClose(selClass,keywords,key,val){
  var oObject = {
    objClass : selClass,
    id : key,
    view : val,
    keywords : keywords
  }

  window.opener.setObject(oObject);
  window.close();
}
</script>

<form action="index.php" name="frmSelector" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="a" value="object_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">
  <tr>
    <th class="category" colspan="3">Crit�res de s�lection</th>
  </tr>
  <tr>
    <th><label for="selClass" title="Veuillez S�lectionner une Class">Choix du type d'objet</label></th>
    <td>
      <select title="str|notNull" name="selClass">
        <option value="">&mdash; Choisissez un type</option>
        {{foreach from=$listClass item=curr_listClass}}
        <option value="{{$curr_listClass}}"{{if $selClass==$curr_listClass}} selected="selected"{{/if}}>{{$curr_listClass}}</option>
        {{/foreach}}
      </select>
    </td>
    <td></td>
  </tr>
  <tr>
    <th><label for="keywords" title="Veuillez saisir un ou plusieurs mot cl�">Mots Cl�s</label></th>
    <td><input title="str|notNull" type="text" name="keywords" value="{{$keywords}}" /></td>
    <td><button class="search" type="submit">Rechercher</button></td>
  </tr>
</table>
</form>

{{if $selClass}}
<table class="tbl">
  <tr>
    <th align="center" colspan="2">R�sultat de la recherche</th>
  </tr>
  
  {{foreach from=$list item=curr_list}}
    <tr>
      <td>{{$curr_list->_view}}</td>     
      <td class="button"><button type="button" class="tick" onclick="setClose('{{$selClass}}', '{{$keywords|escape:javascript}}', {{$curr_list->$key}}, '{{$curr_list->_view|escape:javascript}}')">S�lectionner</button></td>
    </tr>
  {{/foreach}}
</table>
{{/if}}