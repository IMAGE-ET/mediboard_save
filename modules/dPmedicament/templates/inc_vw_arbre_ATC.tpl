<table class="main">
  <tr>
    <td class="halfPane">
      <!-- Affichage du nom du chapitre et de son code -->
			{{if $chapitreATC}}
			<table class="tbl">
			  <tr>
			    <td style="width: 20px; text-align: center">
			      <a href="#" onclick="loadArbreATC('{{$codeNiveauSup}}', '{{$dialog}}')">
			        <img src="./images/icons/uparrow.png" alt="Retour" title="Retour" />
            </a>
          </td> 
			    <th>{{$chapitreATC}} ({{$codeATC}})</th>
			  </tr>
			</table>
      {{/if}}
			<!-- Affichage de l'arborescence -->
			<table class="tbl">
			  {{foreach from=$arbreATC item=elementATC}}
			  <tr>
			    <td>{{$elementATC->Code}}</td>
			    <td class="text"><a href="#" onclick="loadArbreATC('{{$elementATC->Code}}', '{{$dialog}}')">{{$elementATC->Libelle}}</a></td>
			  </tr>
			  {{/foreach}}
			</table>
    </td>
    <td class="halfPane">
			<!-- Affichage des produits -->
			{{if $classeATC->_refs_produits|@count}}
			<table class="tbl">
			  <tr>
			    <th colspan="2">{{$classeATC->_refs_produits|@count}} produits trouv�s (niveau {{$niveauCodeATC}})</th>
			  </tr>
			  {{if $niveauCodeATC >= $dPconfig.dPmedicament.CBcbClasseATC.niveauATC}}
			  {{foreach from=$classeATC->_refs_produits item=produit}}
			  <tr>
			    <td>
			    {{if $dialog}}
           <img src="./images/icons/plus.gif" onclick="setClose('{{$produit->Libelle}}', '{{$produit->CodeCIP}}')" alt="Ajouter � la prescription" title="Ajouter � la prescription" />
          {{/if}}
			    {{$produit->CodeClasse}}</td>
			    <td class="text"><a href="" onclick="viewProduit({{$produit->CodeCIP}}); return false;">{{$produit->Libelle}}</a></td>
			  </tr>
			  {{/foreach}}
			  {{/if}}
			</table>
			{{else}}
			<table class="tbl">
			  <tr>
			    <th colspan="2">Aucun produit trouv�</th>
			  </tr>
			</table>
			{{/if}}
    </td>
  </tr>
</table>