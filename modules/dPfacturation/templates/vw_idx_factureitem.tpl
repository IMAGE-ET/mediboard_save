<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="selFacture" action="index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
      	<tr>
      		<td>
 				<a class="buttonnew" href="index.php?m=dPfacturation&amp;tab=vw_idx_factureitem&amp;facture_id={{$facture->facture_id}}&amp;factureitem_id=0">
        	Cr�er un nouvel �l�ment
      			</a>     		
      		</td>
      	</tr>
        <tr>
          <th class="title" colspan="0">S�lection d'une facture</th>
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
       </table> 
      </form>
      {{include file="list_element.tpl"}}  
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
          <th>{{mb_label object=$factureitem field="libelle"}}
          <br />
      	  <select name="_helpers_libelle" size="1" onchange="pasteHelperContent(this)">
        	<option value="">&mdash; Choisir une aide</option>
        	{{html_options options=$libelleItem->_aides.libelle.no_enum}}
          </select>
          <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CFactureItem', this.form.libelle)" />
        </th>
          <td>{{mb_field object=$libelleItem field="libelle"}}</td>
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