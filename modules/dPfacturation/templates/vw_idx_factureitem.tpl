<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="selFacture" action="index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
        
      <table class="form">
        <tr>
          <th class="category" colspan="10">S�lection d'une facture</th>
        </tr>
       
        <tr>
          <th>
            <label for="facture_id" title="S�lectionner la facture pour afficher ces �l�ments">Facture: </label>
          </th>
          <td>
            <select name="facture_id" onchange="submit()">
              <option value="">&mdash; Choisir une facture &mdash;</option>
              {{foreach from=$listFacture item=curr_facture}}
                <option value="{{$curr_facture->facture_id}}" {{if $curr_facture->facture_id == $facture->facture_id}} selected="selected" {{/if}}  >
                  {{$curr_facture->facture_id}} / {{$curr_facture->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <a class="buttonnew" href="index.php?m=dPfacturation&amp;tab=vw_idx_factureitem&amp;facture_id={{$facture->facture_id}}&amp;factureitem_id=0">
        Cr�er un nouvel �l�ment
      </a>
         </form>
        </table>
      
      
      <table class="tbl">
      
     
      <tr>
           <th class="title" colspan="4">El�ments(s) correspondant(s)</th>
         </tr>
         <tr>
           <th>Element</th>
           <th>Prix H.T</th>
           <th>Taxe</th>
           <th>Prix T.T.C</th>
         </tr>
          {{foreach from=$facture->_ref_items item=curr_refFactureItem}}
         <tr {{if $curr_refFactureItem->_id == $factureitem->_id}}class="selected"{{/if}}>
           <td class="text">
           <a href="index.php?m=dPfacturation&amp;tab=vw_idx_factureitem&amp;facture_id={{$curr_refFactureItem->facture_id}}&amp;factureitem_id={{$curr_refFactureItem->factureitem_id}}" title="Modifier l'element">
              {{$curr_refFactureItem->libelle}}
            </a>
            
            </td>
           <td>{{$curr_refFactureItem->prix_ht|string_format:"%.2f"}}</td>
           <td>{{$curr_refFactureItem->taxe}}</td>
           <td></td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucun �l�ment trouv�</td>
         </tr>
         {{/foreach}}
       </table>

    </td>
 
 
    
    
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editfactureitem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_factureitem_aed" />
      <input type="hidden" name="facture_id" value="{{$facture->facture_id}}" />
      <input type="hidden" name="factureitem_id" value="{{$factureitem->factureitem_id}}" />
      
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $factureitem->factureitem_id}}
          <th class="title modify" colspan="2">Modification de l'�l�ment {{$factureitem->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un �l�ment</th>
          {{/if}}
        </tr>
      
         
        <tr>
          <th>{{mb_label object=$factureitem field="libelle"}}</th>
          <td>{{mb_field object=$factureitem field="libelle"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$factureitem field="prix_ht"}}</th>
          <td>{{mb_field object=$factureitem field="prix_ht"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$factureitem field="taxe"}}</th>
          <td>{{mb_field object=$factureitem field="taxe"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
           {{if $factureitem->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'element',objName:'{{$factureitem->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>