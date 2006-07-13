<table class="main">
  <tr>
    <td class="HalfPane">
      <a class="buttonnew" href="index.php?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id=0">
        Cr�er un nouveau stock
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Mat�riel</th>
          <th>Groupe</th>
          <th>Seuil de Commande</th>
          <th>Quantit�</th>
        </tr>
        {{foreach from=$listStock item=curr_stock}}
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->stock_id}}" title="Modifier le stock">
              {{$curr_stock->stock_id}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_materiel&amp;materiel_id={{$curr_stock->_ref_materiel->materiel_id}}" title="Modifier le mat�riel">
              {{$curr_stock->_ref_materiel->nom}} ({{$curr_stock->_ref_materiel->_ref_category->category_name}})
              {{if $curr_stock->_ref_materiel->code_barre}}<br />{{$curr_stock->_ref_materiel->code_barre}}{{/if}}
              {{if $curr_stock->_ref_materiel->description}}<br />{{$curr_stock->_ref_materiel->description}}{{/if}}
            </a>
          </td>
          <td>{{$curr_stock->_ref_group->text}}</td>
          <td>{{$curr_stock->seuil_cmd}}</td>
          <td>{{$curr_stock->quantite}}</td>
        </tr>
        {{/foreach}}
      </table>
        
    </td>
    <td class="HalfPane">
      <form name="editStock" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_stock_aed" />  
	  <input type="hidden" name="stock_id" value="{{$stock->stock_id}}" />
      <input type="hidden" name="del" value="0" />  
      <table class="form">
        <tr>
          {{if $stock->stock_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification du stock {{$stock->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un stock</th>
          {{/if}}
        </tr>  
        <tr>
          <th><label for="materiel_id" title="Mat�riel, obligatoire">Mat�riel</label></th>
          <td><select name="materiel_id" title="{{$stock->_props.materiel_id}}">
            <option value="">&mdash; Choisir un Mat�riel</option>
            {{foreach from=$listCategory item=curr_cat}}
               <optgroup label="{{$curr_cat->category_name}}">
               {{foreach from=$curr_cat->_ref_materiel item=curr_materiel}}
                 <option value="{{$curr_materiel->materiel_id}}" {{if $stock->materiel_id == $curr_materiel->materiel_id}} selected="selected" {{/if}} >
                 {{$curr_materiel->nom}}
                 </option>
               {{/foreach}}
               </optgroup>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="group_id" title="Groupe, obligatoire">Groupe</label></th>
          <td><select name="group_id" title="{{$stock->_props.group_id}}">
            <option value="">&mdash; Choisir un Groupe</option>
            {{foreach from=$listGroupes item=curr_groupes}}
              <option value="{{$curr_groupes->group_id}}" {{if $stock->group_id == $curr_groupes->group_id}} selected="selected" {{/if}} >
              {{$curr_groupes->text}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>        
        <tr>
          <th><label for="seuil_cmd" title="Seuil de Commande, obligatoire">Seuil de Commande</label></th>
          <td><input name="seuil_cmd" title="{{$stock->_props.seuil_cmd}}" type="text" value="{{$stock->seuil_cmd}}" /></td>
        </tr>
        <tr>
          <th><label for="quantite" title="Quantit�, obligatoire">Quantit�</label></th>
          <td><input name="quantite" title="{{$stock->_props.quantite}}" type="text" value="{{$stock->quantite}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $stock->stock_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le stock',objName:'{{$stock->_view|escape:javascript}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>