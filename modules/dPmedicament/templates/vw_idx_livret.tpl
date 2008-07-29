{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

Main.add(function () {
  // Initialisation des onglets du menu
  Control.Tabs.create('tabs-livret', false);
});


function loadLivretArbreATC(codeATC){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_livret_arbre_ATC");
  url.addParam("codeATC", codeATC);
  url.requestUpdate("ATC", { waitingText: null } );
}


var Livret = {
  // Ajout d'un produit dans le livret
  addProduit: function(code_cip) {
    // Submit du formulaire d'ajout de produit
    var oForm = document.addProduit;
    oForm.code_cip.value = code_cip;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){
        Livret.reloadAlpha('', code_cip);
        Livret.reloadATC('',code_cip);
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
    this.urlEditProd = new Url;
    this.urlEditProd.setModuleAction("dPmedicament", "edit_produit_livret");
    this.urlEditProd.addParam("code_cip", code_cip);
    this.urlEditProd.addParam("lettre", lettre);
    this.urlEditProd.addParam("codeATC", codeATC);
    this.urlEditProd.popup(385, 230, "Modification d'un �l�ment");
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajout�
  reloadAlpha: function(lettre, codeCIP) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_vw_livret");
    url.addParam("lettre", lettre);
    url.addParam("code_cip", codeCIP);
    url.requestUpdate("livret", { waitingText : null });
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajout�
  reloadATC: function(codeATC, codeCIP) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_vw_livret_arbre_ATC");
    url.addParam("codeATC", codeATC);
    // code permet de selectionner le bon code dans le cas d'un ajout de produit
    url.addParam("code_cip", codeCIP);
    url.requestUpdate("ATC", { waitingText : null });
  }
};


// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(815, 620, "Descriptif produit");
}

function printLivret(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "print_livret");
  url.popup(850, 650, "Livret Th�rapeutique");
}

</script>

<!-- Ajout d'un produit dans le livret -->
<table class="form">
   <tr>
     <th class="title">
       <div style="float: right">
       <button type="button" class="print" onclick="printLivret()">Imprimer le livret</button>
       </div>
       Livret Th�rapeutique
     </th>
   </tr>
   <tr>
     <td>
       <form action="?" method="get" name="searchProd" onsubmit="return false;">
         <input type="text" name="produit" value=""/>
         <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
         <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
         <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
         <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
         <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
         <input type="hidden" name="code_cip" onchange="Livret.addProduit(this.value);" />
         <script type="text/javascript">
          MedSelector.init = function(onglet){
            this.sForm = "searchProd";
            this.sView = "produit";
            this.sCode = "code_cip";
            this.sSearch = document.searchProd.produit.value;
            this.sOnglet = onglet;
            this.selfClose = false;
            this.pop();
          }
          /*
       	  MedSelector.doSet = function(){
            var oForm = document[MedSelector.sForm];
            Form.Element.setValue(oForm[MedSelector.sView], MedSelector.prepared.nom);
            Livret.addProduit(MedSelector.prepared.code);
          }*/
        </script>
      </form>
    </td>
  </tr>
</table>

<ul id="tabs-livret" class="control_tabs">
  <li><a href="#livret">Par ordre alphab�tique</a></li>
  <li><a href="#ATC">Par classe ATC</a></li>
</ul>
<hr class="control_tabs" />

<!-- Affichage des produits du livret en fonction de la lettre -->
<div id="livret" style="display: none;">
  {{include file="inc_vw_livret.tpl"}}
</div>

<!-- Affichage des produits du livret en fonction de la classe ATC -->
<div id="ATC" style="display: none;">
{{include file="inc_vw_livret_arbre_ATC.tpl"}}
</div>