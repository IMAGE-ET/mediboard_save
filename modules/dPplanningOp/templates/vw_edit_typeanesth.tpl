<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id=0">
        Cr�er unnouveau type d'anesth�sie
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Type d'anesth�sie</th>
        </tr>
        {{foreach from=$listTypeAnesth item=curr_TypeAnesth}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id={{$curr_TypeAnesth->type_anesth_id}}" title="Modifier le type d'anesth�sie">
              {{$curr_TypeAnesth->type_anesth_id}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id={{$curr_TypeAnesth->type_anesth_id}}" title="Modifier le type d'anesth�sie">
              {{$curr_TypeAnesth->name}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editType" action="./index.php?m={{$m}}&amp;tab=vw_edit_typeanesth" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_typeanesth_aed" />
	  <input type="hidden" name="type_anesth_id" value="{{$type_anesth->type_anesth_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $type_anesth->type_anesth_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification du type d'anesth�sie {{$type_anesth->name}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un type d'anesth�sie</th>
          {{/if}}
        </tr> 
        <tr>
          <th><label for="name" title="Nom du type d'anesth�sie, obligatoire">Nom du type d'anesth�sie</label></th>
          <td><input name="name" title="{{$type_anesth->_props.name}}" type="text" value="{{$type_anesth->name}}" /></td>
        </tr>  
        <tr>
          <td class="button" colspan="2">
            {{if $type_anesth->type_anesth_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le type d\'anesth�sie',objName:'{{$type_anesth->name|escape:"javascript"}}'})">Supprimer</button>
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