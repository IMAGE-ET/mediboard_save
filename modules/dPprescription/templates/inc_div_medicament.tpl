<script type="text/javascript">

moveTbody = function(oTbody){
  var oTableMed = $('med');
	var oTableTrt = $('traitement');
	var oTableMedArt = $('med_art');
	var oTableTrtArt = $('traitement_art');
	
	if(oTbody.hasClassName('med')){
    if(oTbody.hasClassName('line_stopped')){
      oTableMedArt.insert(oTbody);		  
    } else {
      oTableMed.insert(oTbody);		  
    }	
  }
  if (oTbody.hasClassName('traitement')){
    if(oTbody.hasClassName('line_stopped')){
      oTableTrtArt.insert(oTbody);		  
    } else {
      oTableTrt.insert(oTbody);		  
    }		  	
  }
}


// Permet de changer la couleur de la ligne lorsqu'on stoppe la ligne
changeColor = function(object_id, object_class, oForm, traitement, cat_id){   
  if(oForm.date_arret){
    var date_arret = oForm.date_arret.value;
    var date_fin = date_arret;
  }
  
  if(oForm._heure_arret && oForm._min_arret){
    var heure_arret = oForm._heure_arret.value;
    var min_arret = oForm._min_arret.value;
    var date_fin = date_fin+" "+heure_arret+":"+min_arret+":00";
  }
    
  // Entete de la ligne
  var oDiv = $('th_line_'+object_class+'_'+object_id);
  if(object_class == 'CPrescriptionLineMedicament'){
    var oTbody = $('line_medicament_'+object_id);
  } else {
    var oTbody = $('line_element_'+object_id);
  }
  var classes_before = oTbody.className;
  if(date_fin != "" && date_fin <= '{{$now}}'){
    oDiv.addClassName("arretee");
    oTbody.addClassName("line_stopped");
  } else {
    oDiv.removeClassName("arretee");
    oTbody.removeClassName("line_stopped");
  }
  var classes_after = oTbody.className;
  
  // Deplacement de la ligne
  if(classes_before != classes_after){
	  if(object_class == 'CPrescriptionLineMedicament'){
	    moveTbody(oTbody);
	  } else {
	    moveTbodyElt(oTbody, cat_id);
	  }
  }
}

// Fonction lanc�e lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id){
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";
  submitFormAjax(oForm, 'systemMsg', { onComplete: 
    function(){
      // Preparation des prises pour la nouvelle posologie selectionn�e
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_id", curr_line_id);
      url.addParam("no_poso", oForm.no_poso.value);
      url.addParam("code_cip", oForm._code_cip.value);
      url.requestUpdate('prises-Med'+curr_line_id, { waitingText: null });
    } 
   }
  );
}

Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}");

// Permet de mettre la ligne en traitement
transfertTraitement = function(line_id){
  if(!line_id){
    return;
  }
  var oForm = document.transfertToTraitement;
  oForm.prescription_line_id.value = line_id;
  submitFormAjax(oForm, "systemMsg");
}

// Initialisation des dates pour les calendars
var date = new Date().toDATE();
var dDate = Date.fromDATE(date); 
dDate.addDays(-1);
date = dDate.toDATE();

dates = {  
  limit: {
    start: date,
    stop: null
  }
}

changePraticienMed = function(praticien_id){
  var oFormAddLine = document.addLine;
  var oFormAddLineCommentMed = document.addLineCommentMed;
  
  oFormAddLine.praticien_id.value = praticien_id;
  oFormAddLineCommentMed.praticien_id.value = praticien_id;
}

// On met � jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticienMed(document.selPraticienLine.praticien_id.value);
  }
} );


{{if $prescription->type == "sortie"}}
  {{if $prescription->_praticiens|@count}}
  var praticiens = {{$prescription->_praticiens|smarty:nodefaults|escape:"htmlall"|@json}};
  var chps = document.selSortie.selPraticien;
  chps.innerHTML = "";
  chps.insert('<option value="">Tous</option>');
  for(var prat in praticiens){
    chps.insert('<option value='+prat+'>'+praticiens[prat]+'</option>');
  }
  var praticien_sortie_id = {{$praticien_sortie_id|json}};
  
  $A(chps).each( function(option) {
	  option.selected = option.value==praticien_sortie_id;
	});
  {{/if}}	 
{{/if}}


// Test permettant de pr�-selectionner la case � cocher 
testPharma = function(line_id){
  // si on est pas en mode pharmacie, on sort de la fonction
  {{if !$mode_pharma}}return;{{/if}} 
  var oFormAccordPraticien = document.forms["editLineAccordPraticien-"+line_id];
  if(oFormAccordPraticien.accord_praticien.value == 0){
    if(confirm("Modifiez vous cette ligne en accord avec le praticien ?")){
      oFormAccordPraticien.__accord_praticien.checked = true;
      $V(oFormAccordPraticien.accord_praticien,"1");
    }
  }
}


</script>


<form name="transfertToTraitement" action="?" method="post">
  <input type="hidden" name="dosql" value="do_prescription_traitement_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_id" value="" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  
  <input type="hidden" name="_traitement" value="1" />
  <input type="hidden" name="_type" value="{{$prescription->type}}" />
</form>


<!-- Cas normal -->
<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">  
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="prescription_line_medicament_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
  <input type="hidden" name="code_cip" value=""/>
  <!-- Date de debut -->
  <input type="hidden" name="debut" value="{{$today}}" />
  <input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  <input type="hidden" name="refresh_pharma" value="{{$refresh_pharma}}" />
  {{if $prescription->type=="pre_admission" && $prescription->object_id}}
  <input type="hidden" name="callback" value="transfertTraitement" />
  {{/if}}  
</form>

<!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
{{if $prescription->object_id && ($is_praticien || $mode_pharma)}}
<div style="float: right">
  <form name="valideAllLines" method="post" action="">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
    <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    <input type="hidden" name="chapitre" value="medicament" />
    <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
    <button class="tick" type="button" onclick="submitFormAjax(this.form,'systemMsg')">
    {{if $mode_pharma}}
      Validation pharmacien pour toutes les lignes
    {{else}}
      Signer les lignes de m�dicaments
    {{/if}}
    </button>
  </form>
</div>
{{/if}}

<select name="advAction" style="float: right">
    <option value="">&mdash; Actions sp�cifiques</option>
    <option value="stopPerso" onclick="Prescription.stopTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Arret des traitements perso</option>
    <option value="goPerso" onclick="Prescription.goTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Reprise des traitements perso</option>
  </select>
  
{{if $prescription->_can_add_line}}

  
<!-- Affichage des div des medicaments et autres produits -->
  <form action="?" method="get" name="searchProd" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';" style="width: 170px;">
      <option value="">&mdash; M�dicaments les plus utilis�s</option>
      {{foreach from=$listFavoris.medicament item=curr_prod}}
      <option value="{{$curr_prod->code_cip}}">
        {{$curr_prod->libelle}}
      </option>
      {{/foreach}}
    </select>
    <button class="new" onclick="$('add_line_comment_med').show();">Ajouter un commentaire</button>
    
    <br />
	  <input type="text" name="produit" value="" size="12" />
	  
	  <input type="checkbox" name="_recherche_livret" value="1" {{if $prescription->type=="sejour"}}checked="checked"{{/if}} />
	  
	  Livret Th�rap.
	  
	  <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  <button type="button" class="search" onclick="MedSelector.init('produit');">Rechercher</button>
	  <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
	  {{if $prescription->type == "sejour"}}
	  <input type="hidden" name="_recherche_livret" value="1" />
	  {{else}}
	  <input type="hidden" name="_recherche_livret" value="0" />
	  {{/if}}
	  <script type="text/javascript">
		  MedSelector.init = function(onglet){
		    this.sForm = "searchProd";
		    this.sView = "produit";
		    this.sCode = "code_cip";
		    this.sRechercheLivret = document.searchProd._recherche_livret.value;
		    this.sSearch = document.searchProd.produit.value;
		    this.sOnglet = onglet;
		    this.selfClose = false;
		    this.pop();
		  }
	</script>
  </form>
  
  <br />
  
  <div id="add_line_comment_med" style="display: none">
   <button class="cancel notext" type="button" onclick="$('add_line_comment_med').hide();">Cacher</button>
   <form name="addLineCommentMed" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
      <input type="hidden" name="chapitre" value="medicament" />
      <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="this.form.onsubmit();">Ajouter</button>
    </form>
 </div> 
{{elseif !$mode_pharma}}
  <div class="big-info">
    L'ajout de lignes dans la prescription est r�serv� aux praticiens ou aux infirmi�res 
    entre {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_start}} heures et {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_stop}} heures
  </div>
{{else}}
<br />
{{/if}}


{{if $prescription->object_id}}
  {{assign var=traitements value=$prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines}}
{{else}}
  {{assign var=traitements value=""}}
{{/if}}

<!-- Declaration des tableaux permettant de stocker toutes les lignes -->
<table class="tbl" id="med">
</table>

<table class="tbl" id="med_art">
</table>

<table class="tbl" id="traitement">
</table>

<table class="tbl" id="traitement_art">
</table>



{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements}}
<table class="tbl">

  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{if !($prescription->type == "sortie" && $praticien_sortie_id != $curr_line->praticien_id) || !$praticien_sortie_id}}
    <!-- Si la ligne ne possede pas d'enfant -->
    {{if !$curr_line->child_id}}
      {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" prescription_reelle=$prescription}} 
    {{/if}}
    {{/if}}
  {{/foreach}}
 
  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
	  <th colspan="8">Commentaires</th>
	</tr>
  {{/if}}
  
  <!-- Affichage des traitements -->
  {{if $prescription->object_id && $traitements}}
	  {{foreach from=$traitements item=traitement}}
	      {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" curr_line=$traitement prescription=$prescription->_ref_object->_ref_prescription_traitement prescription_reelle=$prescription}}
	  {{/foreach}}
  {{/if}}
  
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{if !($prescription->type == "sortie" && $praticien_sortie_id != $_line_comment->praticien_id) || !$praticien_sortie_id}}
      {{include file="../../dPprescription/templates/inc_vw_line_comment_elt.tpl"}}
    {{/if}}
  {{/foreach}}
  
 </table> 
{{else}}
  <div class="big-info"> 
     Il n'y a aucun m�dicament dans cette prescription.
  </div>
{{/if}}

  <br />

{{if $prescription->object_id}}
<!-- Affichage de l'historique des prescriptions precedentes -->
<table class="tbl">
{{foreach from=$historique key=type_prescription item=hist_prescription}}
 {{if $hist_prescription->_ref_lines_med_comments.med|@count || $hist_prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
    <th colspan="5" class="title">Historique {{tr}}CPrescription.type.{{$type_prescription}}{{/tr}}</th>
  </tr>
  {{/if}}
  {{foreach from=$hist_prescription->_ref_lines_med_comments item=_type_hist_line}}
    {{foreach from=$_type_hist_line item=_hist_line}}
    <tr>
      <!-- Affichage d'une ligne de medicament -->
      {{if $_hist_line->_class_name == "CPrescriptionLineMedicament"}}
	      <td><a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$_hist_line->_class_name}}', object_id: {{$_hist_line->_id}} } })">{{$_hist_line->_view}}</a></td>
		    {{if !$_hist_line->fin}}
			    <td>
			      {{mb_label object=$_hist_line field="debut"}}: {{mb_value object=$_hist_line field="debut"}}
			    </td>
			    <td>
			      {{mb_label object=$_hist_line field="duree"}}: 
			        {{if $_hist_line->duree && $_hist_line->unite_duree}}
			          {{mb_value object=$_hist_line field="duree"}}  
			          {{mb_value object=$_hist_line field="unite_duree"}}
			        {{else}}
			        -
			        {{/if}}
			    </td>
			    <td>
			      {{mb_label object=$_hist_line field="_fin"}}: {{mb_value object=$_hist_line field="_fin"}}
			    </td>
			    {{else}}
			    <td colspan="3">
			      {{mb_label object=$_hist_line field="fin"}}: {{mb_value object=$_hist_line field="fin"}}
			    </td>
		    {{/if}}
	      <td>
	        Praticien: {{$_hist_line->_ref_praticien->_view}}
	      </td>
      {{else}}
      <!-- Affichage d'une ligne de commentaire -->
        <td colspan="3">
           {{$_hist_line->commentaire}}
         </td>
         <td>
           {{mb_label object=$_hist_line field="ald"}}:
		      {{if $_hist_line->ald}}
		        Oui
		      {{else}}
		        Non
		      {{/if}}
         </td>
         <td>
            Praticien: {{$_hist_line->_ref_praticien->_view}}
         </td>
      {{/if}}
    </tr>
    {{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>
{{/if}}

<script type="text/javascript">

if(document.addLine && document.searchProd){
	// UpdateFields de l'autocomplete de medicaments
	updateFieldsMedicament = function(selected) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLine(dn[0].firstChild.nodeValue);
	  $('searchProd_produit').value = "";
	}
	
	// Preparation des formulaire
	prepareForm(document.addLine);
	prepareForm(document.searchProd);
	
	var oFormProduit = document.searchProd;
	
	// Autocomplete des medicaments
	urlAuto = new Url();
	urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
	urlAuto.addParam("produit_max", 40);
	
	// callback => methode pour ajouter en post des parametres
	// Faire un mini framework pour rajouter des elements du meme formulaire
	urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
	  minChars: 3,
	  updateElement: updateFieldsMedicament,
	  callback: 
	    function(input, queryString){ 
	      return (queryString + "&inLivret="+$V(oFormProduit._recherche_livret)); 
	    }
	} );
}


</script>