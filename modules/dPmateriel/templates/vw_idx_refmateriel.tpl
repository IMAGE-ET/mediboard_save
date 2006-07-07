<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button" href="index.php?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id=0">
        Cr�er une nouvelle r�f�rence
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Mat�riel</th>
          <th>Fournisseur</th>
          <th>Quantit�</th>
          <th>Prix</th>
          <th>Prix Unitaire</th>
        </tr>
        {foreach from=$listReference item=curr_reference}
        <tr>
          <td>
            <a href="index.php?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id={$curr_reference->reference_id}" title="Modifier la r�f�rence">
              {$curr_reference->reference_id}
            </a>
          </td>
          <td>{$curr_reference->_ref_fournisseur->societe}</td>
          <td>{$curr_reference->_ref_materiel->nom}</td>
          <td>{$curr_reference->quantite}</td>
          <td>{$curr_reference->prix}</td>
          <td>{$curr_reference->_prix_unitaire|string_format:"%.2f"}</td>
        </tr>
        {/foreach}
      </table>    
    </td>
    <td class="halfPane">
      {if $canEdit}
      <form name="editreference" action="./index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_refmateriel_aed" />
	  <input type="hidden" name="reference_id" value="{$reference->reference_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {if $reference->reference_id}
          <th class="title" colspan="2" style="color:#f00;">Modification de la r�f�rence {$reference->_view}</th>
          {else}
          <th class="title" colspan="2">Cr�ation d'une r�f�rence</th>
          {/if}
        </tr>
        <tr>
          <th><label for="fournisseur_id" title="Fournisseur, obligatoire">Fournisseur</label></th>
          <td><select name="fournisseur_id" title="{$reference->_props.fournisseur_id}">
            <option value="">&mdash; Choisir un Fournisseur</option>
            {foreach from=$listFournisseur item=curr_fournisseur}
              <option value="{$curr_fournisseur->fournisseur_id}" {if $reference->fournisseur_id == $curr_fournisseur->fournisseur_id} selected="selected" {/if} >
              {$curr_fournisseur->societe}
              </option>
            {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="materiel_id" title="Mat�riel, obligatoire">Mat�riel</label></th>
          <td><select name="materiel_id" title="{$reference->_props.materiel_id}">
            <option value="">&mdash; Choisir un Mat�riel</option>
            {foreach from=$listCategory item=curr_cat}
               <optgroup label="{$curr_cat->category_name}">
               {foreach from=$curr_cat->_ref_materiel item=curr_materiel}
                 <option value="{$curr_materiel->materiel_id}" {if $reference->materiel_id == $curr_materiel->materiel_id} selected="selected" {/if} >
                 {$curr_materiel->nom}
                 </option>
               {/foreach}
               </optgroup>
            {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="quantite" title="Quantit�, obligatoire">Quantit�</label></th>
          <td><input name="quantite" title="{$reference->_props.quantite}" type="text" value="{$reference->quantite}" /></td>
        </tr>
        <tr>
          <th><label for="prix" title="Prix, obligatoire">Prix</label></th>
          <td><input name="prix" title="{$reference->_props.prix}" type="text" value="{$reference->prix}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Valider</button>
            {if $reference->reference_id}
              <button type="button" onclick="confirmDeletion(this.form,{ldelim}typeName:'la r�f�rence',objName:'{$reference->_view|escape:javascript}'{rdelim})">Supprimer</button>
            {/if}
          </td>
        </tr>        
      </table>
      </form>
      {/if}
    </td>
  </tr>
</table>