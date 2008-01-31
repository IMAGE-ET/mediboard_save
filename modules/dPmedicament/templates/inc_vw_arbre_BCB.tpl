<table class="main">
  <tr>
    <td class="halfPane">
      <!-- Affichage du nom du chapitre et de son code -->
			{{if $chapitreBCB}}
			<table class="tbl">
			  <tr>
			    <td style="width: 20px; text-align: center">
			      <a href="#" onclick="loadArbreBCB('{{$codeNiveauSup}}', '{{$dialog}}')">
			        <img src="./images/icons/uparrow.png" alt="Retour" title="Retour" />
            </a>
          </td> 
			    <th>{{$chapitreBCB}} ({{$codeBCB}})</th>
			  </tr>
			</table>
      {{/if}}
			<!-- Affichage de l'arborescence -->
			<table class="tbl">
			  {{foreach from=$arbreBCB item=elementBCB}}
			  <tr>
			    <td>{{$elementBCB->Code}}</td>
			    <td class="text"><a href="#" onclick="loadArbreBCB('{{$elementBCB->Code}}', '{{$dialog}}')">{{$elementBCB->Libelle}}</a></td>
			  </tr>
			  {{/foreach}}
			</table>
    </td>
    <td class="halfPane">
			<!-- Affichage des produits -->
			{{if $classeBCB->_refs_produits|@count}}
			<table class="tbl">
			  <tr>
			    <th colspan="2">{{$classeBCB->_refs_produits|@count}} produits trouv�s (niveau {{$niveauCodeBCB}})</th>
			  </tr>
			  {{if $niveauCodeBCB >= $dPconfig.dPmedicament.CBcbClasseTherapeutique.niveauBCB}}
			  {{foreach from=$classeBCB->_refs_produits item=produit}}
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