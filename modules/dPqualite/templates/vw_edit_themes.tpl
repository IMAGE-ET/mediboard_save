<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
    
      <form name="FrmTypeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="typeVue">Classification</label>
      <select name="typeVue" onchange="this.form.submit();">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Gestion des Chapitres</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Gestion des Th�mes</option>
      </select>
      </form><br />
    
      <a class="buttonnew" href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_theme_id=0">
        Cr�er un nouveau Th�me
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
        </tr>
        {{foreach from=$listThemes item=curr_theme}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_theme_id={{$curr_theme->doc_theme_id}}" title="Modifier le th�me">
              {{$curr_theme->nom}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editTh�me" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_theme_aed" />
	  <input type="hidden" name="doc_theme_id" value="{{$theme->doc_theme_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $theme->doc_theme_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification du th�me: {{$theme->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un th�me</th>
          {{/if}}
        </tr>   
        <tr>
          <th><label for="nom" title="Nom du th�me, obligatoire">Nom</label></th>
          <td><input name="nom" title="{{$theme->_props.nom}}" type="text" value="{{$theme->nom}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">            
            {{if $theme->doc_theme_id}}
              <button class="modify" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le th�me',objName:'{{$theme->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
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