{{assign var=typeDate value="mode_grille"}}
{{assign var=line value=$filter_line_element}}
{{assign var=type value="mode_grille"}} 
 
<script type="text/javascript">

changeButton = function(oCheckbox, element_id, oTokenField, modeCategorie){
  if(oCheckbox.checked){
    oTokenField.add(element_id);
  } else {
    oTokenField.remove(element_id);
  }
}

// Ajout de tous les elements d'une categorie
function addCategorie(categorie_id, oTokenField){
  // Parcours de tous les boutons
  $$('input.'+categorie_id).each( function(oCheckbox) {
    elt = oCheckbox.id;
    elts = elt.split("-");
    id  = elts[1];
    
    // Si element pas encore selectionn�
    if(!oCheckbox.checked){
      oCheckbox.checked = true;
      oTokenField.add(id);
    }
  }); 
}

function resetModeEasy(){
  $$('input').each( function(oCheckbox) {
    if(oCheckbox.checked){
      if(!oCheckbox.hasClassName("med")){
	      elt = oCheckbox.id;
	      elts = elt.split("-");
	      id = elts[1];
	      if($('label-'+id)){
	        $('label-'+id).setStyle("color: #070");
		    }
		    oCheckbox.checked = false;
		  } else {
		    oCheckbox.checked = false;
		  }
    }
  });
  
  
  var oFormToken = document.add_med_element;
  oFormToken.token_med.value = '';
  oFormToken.token_elt.value = '';
  
  $$('input.valeur').each( function(input) {
	   input.value = '';
	})
	 
}

function submitAllElements(){
  // Divs
  var oDivMoment = $('momentmode_grille');
  var oDivFoisPar = $('foisParmode_grille');
  var oDivTousLes = $('tousLesmode_grille');

  // Forms
  var oForm = document.add_med_element;
  var oFormMoment = document.addPriseMomentmode_grille;
  var oFormFoisPar = document.addPriseFoisParmode_grille;
  var oFormTousLes = document.addPriseTousLesmode_grille;
  
  
  // Formulaire par defaut
  if(document.forms["editDates-{{$typeDate}}-"]){
    var oFormDate = document.forms["editDates-{{$typeDate}}-"];
    oForm.debut.value = oFormDate.debut.value;
    oForm.duree.value = oFormDate.duree.value;
    oForm.unite_duree.value = oFormDate.unite_duree.value;
    if(oForm.time_debut){
      oForm.time_debut.value = oFormDate.time_debut.value;
    }
  }
  // Formulaire dans le cas d'un protocole
  if(document.forms["editDuree-{{$typeDate}}-"]){
    var oFormDate = document.forms["editDuree-{{$typeDate}}-"];
    oForm.duree.value = oFormDate.duree.value;
    if(oFormDate.jour_decalage){
      oForm.jour_decalage.value = oFormDate.jour_decalage.value;
    }
    oForm.decalage_line.value = oFormDate.decalage_line.value;
    oForm.time_debut.value = oFormDate.time_debut.value;
    if(oFormDate.jour_decalage_fin){
      oForm.jour_decalage_fin.value = oFormDate.jour_decalage_fin.value;
      oForm.decalage_line_fin.value = oFormDate.decalage_line_fin.value;
      oForm.time_fin.value = oFormDate.time_fin.value;
    }   
    
  }
  


  if(oDivMoment.visible() && oFormMoment.moment_unitaire_id.value && oFormMoment.quantite.value){
    oForm.moment_unitaire_id.value = oFormMoment.moment_unitaire_id.value;
    oForm.quantite.value = oFormMoment.quantite.value;
  }
  if(oDivFoisPar.visible() && oFormFoisPar.nb_fois.value && oFormFoisPar.unite_fois.value && oFormFoisPar.quantite.value){
    oForm.nb_fois.value = oFormFoisPar.nb_fois.value;
    oForm.unite_fois.value = oFormFoisPar.unite_fois.value;
    oForm.quantite.value = oFormFoisPar.quantite.value;
  }
  if(oDivTousLes.visible() && oFormTousLes.nb_tous_les.value && oFormTousLes.unite_tous_les.value && oFormTousLes.quantite.value){
    oForm.nb_tous_les.value = oFormTousLes.nb_tous_les.value;
    oForm.unite_tous_les.value = oFormTousLes.unite_tous_les.value;
    oForm.quantite.value = oFormTousLes.quantite.value;
    oForm.moment_unitaire_id.value = oFormTousLes.moment_unitaire_id.value;
    oForm.decalage_prise.value = oFormTousLes.decalage_prise.value;
  }
  submitFormAjax(oForm,'systemMsg');
  resetModeEasy();
}


Main.add( function(){
  // Initialisation des onglets
  menuTabs = Control.Tabs.create('main_tab_group', false); 
  // Initialisation des TokenFields
  oMedField = new TokenField(document.add_med_element.token_med); 
  oEltField = new TokenField(document.add_med_element.token_elt); 
  
  // Modification du praticien_id si celui-ci est sp�cifi�
  if(window.opener.document.selPraticienLine){
    var oFormPraticien = window.opener.document.selPraticienLine;
    var oForm = document.add_med_element;
    oForm.praticien_id.value = oFormPraticien.praticien_id.value;
  }
  
  // Elements deja dans la prescription
  
	var elements = {{$elements|@json}};
	$$('input').each( function(oCheckbox) {
	  if(!oCheckbox.hasClassName("cat")){
		  var _id = oCheckbox.id;
		  var elts = _id.split("-");
		  var id = elts[1];
		  if(elements.include(id)){
		    $('label-'+id).setStyle("color: #070");
		  }
	  }
	});
	 
} );


</script>

<table class="form">
  <tr>
    <th class="category">Dates</th>
    <th class="category">Fr�quence</th>
  </tr>
  <tr>
    <td>
	    {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl" 
	              perm_edit=1
	              dosql=CPrescriptionLineElement}}	      
	              
	     <script type="text/javascript">
	     if(document.forms["editDates-{{$typeDate}}-"]){
	       prepareForm(document.forms["editDates-{{$typeDate}}-"]);  
				 {{if !$line->fin}} 
	         regFieldCalendar("editDates-{{$typeDate}}-", "debut");
	         regFieldCalendar("editDates-{{$typeDate}}-", "_fin");     
	       {{/if}}
	       {{if $line->fin}}
	         regFieldCalendar("editDates-{{$typeDate}}-", "fin");     
	       {{/if}}  
	       }
	     </script>
	  
	  </td>
	  <td>
	    {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl"}}
	  </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
		  <form name="add_med_element" action="?" method="post">
			  <input type="hidden" name="m" value="dPprescription" />
			  <input type="hidden" name="dosql" value="do_add_elements_easy_aed" />
			  <input type="hidden" name="token_med" value="" />
			  <input type="hidden" name="token_elt" value="" />
			  <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
			  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			  <input type="hidden" name="debut" value="" />
			  <input type="hidden" name="duree" value="" />
			  <input type="hidden" name="unite_duree" value="jour" />
			  <input type="hidden" name="mode_protocole" value="{{$mode_protocole}}" />
			  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
			  <input type="hidden" name="decalage_line" value="" />
			  <input type="hidden" name="jour_decalage" value="" />
			  <input type="hidden" name="time_debut" value="" />
			  <input type="hidden" name="jour_decalage_fin" value="" />
			  <input type="hidden" name="decalage_line_fin" value="" />
			  <input type="hidden" name="time_fin" value="" />
			  <input class="valeur" type="hidden" name="quantite" value="" />
			  <input class="valeur" type="hidden" name="nb_fois" value="" />
			  <input class="valeur" type="hidden" name="unite_fois" value="" />
			  <input class="valeur" type="hidden" name="moment_unitaire_id" value="" />
			  <input class="valeur" type="hidden" name="nb_tous_les" value="" />
			  <input class="valeur" type="hidden" name="unite_tous_les" value="" />
			  <input class="valeur" type="hidden" name="decalage_prise" value="" />
			  <button type="button" 
			          class="submit" 
			          onclick="submitAllElements();">Ajouter les �l�ments � la prescription</button>
			</form>
    </td>
  </tr>
</table>


<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#div_medicament">M�dicaments</a></li>
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_nom_chapitre}}
  <li><a href="#div_{{$_nom_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_nom_chapitre}}{{/tr}}</a></li>
  {{/foreach}}
</ul>
<hr class="control_tabs" />

<form action="" method="get" onsubmit="return false;">
<!-- Affichage des elements -->
{{assign var=numCols value=4}}
<table class="main">
  <tr>
  <td>
		<!-- Affichage des divs -->
		<div id="div_medicament" style="display: none">
		  <table class="tbl">
		  <tr>
		    <th colspan="4">Favoris</th>
		  </tr>
		  <tr>
		  {{foreach from=$medicaments item=medicament name=meds}}
		    {{assign var=i value=$smarty.foreach.meds.iteration}}
		        <td style="width: 1%;">
		          
		          <input type="checkbox" name="med-{{$medicament->code_cip}}"
		                 class="med"
		                 onclick="changeButton(this,'{{$medicament->code_cip}}',oMedField);" />
		        </td>
		        <td>
		          <label for="med-{{$medicament->code_cip}}">{{$medicament->libelle}}</label>
		        </td>
		     {{if ((($i % 2) == 0))}}</tr>{{if !$smarty.foreach.meds.last}}<tr>{{/if}}{{/if}}
		  {{/foreach}}
		   </table>
		</div>
		{{foreach from=$chapitres key=name_chap item=chapitre}}
		  <div id="div_{{$name_chap}}" style="display: none">
		    <table class="tbl">
		    {{foreach from=$chapitre item=categorie}}
		      <tr>
		        <th colspan="{{$numCols*2}}">{{$categorie->_view}}
		        {{$categorie->_id}}
		          {{assign var=categorie_id value=$categorie->_id}}
		          <button  id="{{$categorie->_id}}" class="cat tick"  style="position: absolute; right: 12px; margin-top: -2px;" onclick="addCategorie('{{$categorie->_id}}',oEltField);" title="Ajouter cet �l�ment">
		          Ajouter tous les �l�ments de la cat�gorie
		          </button>
		        </th>
		       </tr>
		      {{if $categorie->_ref_elements_prescription|@count}}
		      <tr>
		      {{/if}}
		      {{foreach from=$categorie->_ref_elements_prescription item=element name=elements}}
		        {{assign var=i value=$smarty.foreach.elements.iteration}}
		        <td style="width: 1%;">
		          <input type="checkbox" name="elt-{{$element->_id}}" 
		                  class="{{$categorie->_id}}" 
		                  onclick="changeButton(this,'{{$element->_id}}',oEltField);" />    
            </td>
            <td>		         
              <label id="label-{{$element->_id}}" for="elt-{{$element->_id}}">{{$element->_view}}</label>
		        </td>
		        {{if (($i % $numCols) == 0)}}</tr>{{if !$smarty.foreach.elements.last}}<tr>{{/if}}{{/if}}
		      {{/foreach}}
		     
		    {{/foreach}}
		    </table>
		  </div>
      {{/foreach}}
    </td>
  </tr>
</table>  
</form>