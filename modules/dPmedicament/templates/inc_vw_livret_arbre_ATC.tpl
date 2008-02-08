<script type="text/javascript">

// Fermeture de la popup de modification de produit a la fin du 2eme refresh (reloadATC)
if(Livret.urlEditProd) {
  Livret.urlEditProd.close();
}
if(MedSelector.oUrl) {
  MedSelector.close();
}
	
</script>


{{if !$codeATC}}
<div class="big-info">
  Veuillez s�lectionner la classe ATC dans laquelle vous d�sirez rechercher des produits
</div>
{{/if}}
  
<table class="main">
  <tr>
    <td>
      <!-- Affichage du nom du chapitre et de son code -->
			{{if $chapitreATC}}
			<table class="tbl">
			  <tr>
			    <td style="width: 20px; text-align: center">
			      <a href="#" onclick="loadLivretArbreATC('{{$codeNiveauSup}}')">
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
			    <td class="text"><a href="#" onclick="loadLivretArbreATC('{{$elementATC->Code}}')">{{$elementATC->Libelle}}</a></td>
			  </tr>
			  {{/foreach}}
			</table>
    </td>
  </tr>
</table>

{{if $codeATC}}
<table class="tbl">
  <tr>
    <th colspan="10">{{$listProduits|@count}} produits dans le livret</th>
  </tr>  
  <tr>
    <th>Actions</th>
    <th>Libelle</th>
    <th>Code CIP</th>
    <th>Code UCD</th>
    <th>Prix H�pital</th>
    <th>Prix Ville</th>
    <th>Date Prix H�pital</th>
    <th>Date Prix Ville</th>
    <th>Code Interne</th>
    <th>Commentaire</th>
  </tr>
  {{foreach from=$listProduits item=produit}}
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Livret.delProduit('{{$produit->code_cip}}','','{{$codeATC}}')">
        {{tr}}Delete{{/tr}}
      </button>
      <button type="button" class="edit notext" onclick="Livret.editProduit('{{$produit->code_cip}}','','{{$codeATC}}')">
        {{tr}}Modify{{/tr}} 
      </button>
    </td>  
    <td>
      <a href="#produit{{$produit->code_cip}}" onclick="viewProduit({{$produit->code_cip}})">
        {{$produit->_ref_produit->libelle}}
      </a>
    </td>
    <td>
      {{$produit->code_cip}}
    </td>
    <td>
      {{$produit->_ref_produit->code_ucd}}
    </td>
    <td>
      {{if $produit->prix_hopital}}
        {{$produit->prix_hopital}}&euro;
      {{/if}}
    </td>
    <td>
      {{if $produit->prix_ville}}
        {{$produit->prix_ville}}&euro;
      {{/if}}
    </td>
    <td>
      {{$produit->date_prix_hopital|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      {{$produit->date_prix_ville|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      {{$produit->code_interne}}
    </td> 
    <td>
      {{$produit->commentaire}}
    </td>   
  </tr>
  {{/foreach}}
</table>
{{/if}}