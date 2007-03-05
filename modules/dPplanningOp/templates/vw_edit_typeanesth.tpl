<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_typeanesth&amp;type_anesth_id=0">
        Cr�er un nouveau type d'anesth�sie
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
      {{mb_field object=$type_anesth field="type_anesth_id" hidden=1 prop=""}}
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $type_anesth->type_anesth_id}}
          <th class="title modify" colspan="2">Modification du type d'anesth�sie {{$type_anesth->name}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un type d'anesth�sie</th>
          {{/if}}
        </tr> 
        <tr>
          <th>{{mb_label object=$type_anesth field="name"}}</th>
          <td>{{mb_field object=$type_anesth field="name"}}</td>
        </tr>  
        <tr>
          <td class="button" colspan="2">
            {{if $type_anesth->type_anesth_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le type d\'anesth�sie',objName:'{{$type_anesth->name|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
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