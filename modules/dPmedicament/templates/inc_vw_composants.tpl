<script type="text/javascript">
 
loadComposants = function(composant, code, libelle, dialog){
  var oForm = document.rechercheComposant;
  
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_composants");
  url.addParam("composant", composant);
  url.addParam("code", code);
  url.addParam("libelle", libelle);
  url.addParam("dialog", dialog);
  url.addParam("rechercheLivretComposant", getCheckedValue(oForm.rechercheLivretComposant));
  url.requestUpdate("composant", { waitingText: null } );
}


</script>

<table class="form">
  <tr>
    <td>
      <form name="rechercheComposant" action="?" method="get" onsubmit="return false;">
        Composant recherch�
        <input type="text" name="composant" value="{{$composant}}" />
        <button type="button" class="search" onclick="loadComposants(this.form.composant.value, '', '', '{{$dialog}}')">Rechercher</button>
        <br />
        <br />
        <input type="checkbox" name="rechercheLivretComposant" value="1" {{if $rechercheLivretComposant == 1}}checked = "checked"{{/if}} />
        Rechercher uniquement dans le livret th�rapeutique
      </form>
    </td>
  </tr>
</table>

{{if $composant && $composition}}
<table class="tbl">
  <tr>
    <th>{{$composition->_ref_composants|@count}} compositions trouv�es</th>
  </tr>
  {{foreach from=$composition->_ref_composants item="_composant"}}
    <tr>
      <td><a href="#" onclick="loadComposants('', '{{$_composant->Code}}', '{{$_composant->Libelle}}', '{{$dialog}}')">{{$_composant->Libelle}}</a></td>
    </tr>
  {{/foreach}}
</table>
{{/if}}

{{if $code}}
<table class="tbl">
  <tr>
    <th {{if $dialog}}colspan="2"{{/if}}>{{$composition->_ref_produits|@count}} produits trouv�s contenant {{$libelle}}</th>
  </tr>
  {{foreach from=$composition->_ref_produits item="_produit"}}
    <tr>
      {{if $dialog}}
      <td>
        <img src="./images/icons/plus.gif" onclick="setClose('{{$_produit->Libelle}}', '{{$_produit->CodeCIP}}')" alt="Ajouter � la prescription" title="Ajouter � la prescription" />
      </td>
      {{/if}}
      <td>
        <a href="#produit{{$_produit->CodeCIP}}" onclick="viewProduit({{$_produit->CodeCIP}})">
        {{$_produit->Libelle}}
        </a>
        
      </td>
      
    </tr>
  {{/foreach}}
</table>
{{/if}}