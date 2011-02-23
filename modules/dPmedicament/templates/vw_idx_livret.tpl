{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

// UpdateFields de l'autocomplete
function updateFieldsProduitLivret(selected) {
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();	
  var code_cip = dn[0].innerHTML;
  if(dn[1]){
	  Livret.reloadAlpha('', code_cip);
    Livret.reloadATC('', code_cip);
  }
	$('searchProdLivret_produit').value = "";
}

Main.add(function () {
  $('_list_produits').hide();
 
  // Initialisation des onglets du menu
  Control.Tabs.create('tabs-livret', true);
  
  // Preparation du formulaire
  prepareForm(document.searchProdLivret);
  // Autocomplete
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.autoComplete("searchProdLivret_produit", "produit_livret_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsProduitLivret,
    callback: 
      function(input, queryString){
        {{if isset($livret_cabinet|smarty:nodefaults)}}
          queryString += "&function_guid="+$("function_guid").value+"&livret_cabinet=1";
        {{/if}}
        return (queryString + "&inLivret=1&search_libelle_long=true&search_by_cis=0"); 
      }
    } );
});


function loadLivretArbreATC(codeATC){
  var url = new Url("dPmedicament", "httpreq_vw_livret_arbre_ATC");
  if (codeATC !== '') {
    url.addParam("codeATC", codeATC);
  }
  url.requestUpdate("ATC");
}

var nb_produits = 0;

var Livret = {
  // Ajout d'un produit dans le livret
  addProduit: function(code_cip, view_produit) {
    // Submit du formulaire d'ajout de produit
    var oForm = document.addProduit;
    oForm.code_cip.value = code_cip;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){
        nb_produits++;
        if(nb_produits == 5){
          $('list_produits').update("");
        }
        $('_list_produits').show();
        $('list_produits').insert(code_cip+": "+view_produit+"<br />");
      }  
    });
  },
  
  // Suppression d'un produit du livret
  delProduit: function(code_cip, lettre, codeATC) {
    // Submit du formulaire de suppression du produit
    var oForm = document.addProduit;
    oForm.code_cip.value = code_cip;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function() {
        Livret.reloadAlpha(lettre, code_cip);
        Livret.reloadATC(codeATC, code_cip);
      } 
    });
  },
  
  editProduit: function(code_cip, lettre, codeATC) {
    this.urlEditProd = new Url("dPmedicament", "edit_produit_livret");
    this.urlEditProd.addParam("code_cip", code_cip);
    this.urlEditProd.addParam("lettre", lettre);
    this.urlEditProd.addParam("codeATC", codeATC);
    {{if isset($livret_cabinet|smarty:nodefaults)}}
      this.urlEditProd.addParam("function_guid", "{{$function_guid}}");
    {{/if}}
    this.urlEditProd.popup(385, 230, "Modification d'un �l�ment");
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajout�
  reloadAlpha: function(lettre, codeCIP) {
    var url = new Url("dPmedicament", "httpreq_vw_livret");
    url.addParam("lettre", lettre);
    url.addParam("code_cip", codeCIP);
    {{if isset($livret_cabinet|smarty:nodefaults)}}
      url.addParam("function_guid", "{{$function_guid}}");
    {{/if}}
    url.requestUpdate("livret");
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajout�
  reloadATC: function(codeATC, codeCIP) {
    var url = new Url("dPmedicament", "httpreq_vw_livret_arbre_ATC");
    url.addParam("codeATC", codeATC);
    // code permet de selectionner le bon code dans le cas d'un ajout de produit
    url.addParam("code_cip", codeCIP);
    url.requestUpdate("ATC");
  }
};

function printLivret(orderbyATC){
  var url = new Url("dPmedicament", "print_livret");
  url.addParam("orderby", orderbyATC ? "atc" : "libelle");
  {{if isset($livret_cabinet|smarty:nodefaults)}}
  url.addParam("function_guid", "{{$function_guid}}");
  {{/if}}
  url.popup(850, 650, "Livret Th�rapeutique");
}

function reloadWithFunction(function_guid) {
  var url = new Url("dPcabinet", "vw_idx_livret", "tab");
  url.addParam("function_guid", function_guid);
  url.redirect();
}

</script>

<div onclick="this.hide();" id="_list_produits" style="max-height: 100px; overflow: auto; position: fixed; top: 100px; right: 10px; background: #fff;
                                border: 1px solid #666; padding: 5px; right: 5px;">
	<strong>Produits r�cemment rajout�s</strong>
	<div id="list_produits" ></div>
</div>

<!-- Ajout d'un produit dans le livret -->
<table class="form">
   <tr>
     <th class="title" colspan="2">
       <span style="float: right">
         <button type="button" class="print" onclick="printLivret($('orderby-atc').checked)">Imprimer le livret</button>
         <label>
           <input type="checkbox" id="orderby-atc" /> Trier par classe ATC
         </label>
       </span>
       {{if isset($livret_cabinet|smarty:nodefaults)}}
         <span style="float: left">
           <select name="function_guid" id="function_guid" onchange="reloadWithFunction(this.value)"
             {{if $functions|@count == 1}}disabled="disabled"{{/if}}>
             {{foreach from=$functions item=_function}}
               <option value="{{$_function->_guid}}"
                 {{if $_function->_guid == $function_guid}}selected="selected"{{/if}}>{{$_function->text}}</option>
             {{/foreach}}
           </select>
         </span>
       {{/if}}
       Livret {{if isset($livret_cabinet|smarty:nodefaults)}}de prescription{{else}}Th�rapeutique{{/if}}
     </th>
   </tr>
   <tr>
     <th class="category">Ajout d'un produit dans le livret</th>
     <th class="category">Recherche d'un produit dans le livret</th>
   </tr>
   <tr>
     <td>
       <form action="?" method="get" name="searchProd" onsubmit="return false;">
         <!-- Champ permettant d'effectuer une recherche par autocomplete-->
         <input type="text" name="produit" value="" class="autocomplete"/>
         <!-- Champ permettant de stocker le libelle dans le cas d'une recherche par la popup -->
         <input type="text" name="_produit" value="" style="display: none;"/>  
         <div style="display:none; width: 30em;" class="autocomplete" id="produit_auto_complete"></div>
				 <button type="button" class="search" onclick="MedSelector.init('produit');">{{tr}}Search{{/tr}}</button>
         <input type="hidden" name="code_cip" onchange="Livret.addProduit(this.value, this.form._produit.value);" />
         <script type="text/javascript">
          MedSelector.init = function(onglet){
            this.sForm = "searchProd";
            this.sView = "_produit";
            this.sCode = "code_cip";
            this.sSearch = document.searchProd.produit.value;
            this.sSearchByCIS = "0";
            this.sOnglet = onglet;
            this.selfClose = false;
            this.pop();
          }
        </script>
      </form>
    </td>
    <td>
      Libelle
       <form action="?" method="get" name="searchProdLivret" onsubmit="return false;">
         <input type="text" name="produit" value="" class="autocomplete" />
         <div style="display:none;" class="autocomplete" id="produit_livret_auto_complete"></div>
       </form>
    </td>
  </tr>
</table>

<ul id="tabs-livret" class="control_tabs">
  <li><a href="#livret">Par ordre alphab�tique</a></li>
  <li><a href="#ATC">Par classe ATC</a></li>
  {{if @$modules.dPstock}}
    <li><a href="#stocks">Stocks</a></li>
  {{/if}}
</ul>
<hr class="control_tabs" />

<!-- Affichage des produits du livret en fonction de la lettre -->
<div id="livret" style="display: none;">
  {{mb_include module=dPmedicament template=inc_vw_livret}}
</div>

<!-- Affichage des produits du livret en fonction de la classe ATC -->
<div id="ATC" style="display: none;">
  {{mb_include module=dPmedicament template=inc_vw_livret_arbre_ATC}}
</div>

{{if @$modules.dPstock}}
  <!-- Affichage des produits des stocks -->
  <div id="stocks" style="display: none;">
    {{mb_include module=dPmedicament template=inc_vw_stock_products}}
  </div>
{{/if}}