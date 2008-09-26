<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('prescription_tab_group', false);
} );

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

// Lancement du mode de saisie popup
viewEasyMode = function(mode_protocole, mode_pharma){
  var url = new Url();
  url.setModuleAction("dPprescription","vw_easy_mode");
  url.addParam("prescription_id", '{{$prescription->_id}}');
  url.addParam("mode_protocole", mode_protocole);
  url.addParam("mode_pharma", mode_pharma);
  url.popup(900,500,"Mode grille");
}

setPrimaryKeyDosql = function (form, object_class, object_id) {
  var field, dosql;
  switch (object_class) {
    case "CPrescriptionLineMedicament": 
      field = "prescription_line_medicament_id";
      dosql = "do_prescription_line_medicament_aed";
      break;
    case "CPrescriptionLineElement": 
      field = "prescription_line_element_id";
      dosql = "do_prescription_line_element_aed";
      break;
    case "CPrescriptionLineComment": 
      field = "prescription_line_comment_id";
      dosql = "do_prescription_line_comment_aed";
      break;
  }
  form[field].value = object_id;
  form.dosql.value = dosql;
}

submitALD = function(object_class, object_id, ald){
  var oForm = getForm("editLineALD-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  oForm.ald.value = ald ? "1" : "0";
  onSubmitFormAjax(oForm);
}

submitConditionnel = function(object_class, object_id, conditionnel){
  var oForm = getForm("editLineConditionnel-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  oForm.conditionnel.value = conditionnel ? "1" : "0";
  return onSubmitFormAjax(oForm);
}

submitValidationInfirmiere = function(object_class, object_id, prescription_id, div_refresh, mode_pharma) {
  var oForm = getForm("validation_infirmiere-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  return onSubmitFormAjax(oForm, { onComplete: 
    function() { 
      Prescription.reload(prescription_id, '', div_refresh, '', mode_pharma); 
    }
  });
}

submitValidationPharmacien = function(prescription_id, object_id, valide_pharma, mode_pharma) {
  var oForm = getForm("validation_pharma");
  prepareForm(oForm);
  oForm.valide_pharma.value = valide_pharma;
  oForm.prescription_line_medicament_id.value = object_id;
  onSubmitFormAjax(oForm, { onComplete: function() {
    Prescription.reload(prescription_id, '', 'medicament', '', mode_pharma); }
  });
}

submitValideAllLines = function (prescription_id, chapitre, mode_pharma) {
  var oForm = getForm("valideAllLines");
  prepareForm(oForm);
  oForm.prescription_id.value = prescription_id;
  oForm.chapitre.value = chapitre;
  if (mode_pharma) {
    oForm.mode_pharma.value = mode_pharma;
  }
  return onSubmitFormAjax(oForm);
}

submitAddComment = function (object_class, object_id, commentaire) {
  var oForm = getForm("addComment-"+object_class);
  prepareForm(oForm);
  
  setPrimaryKeyDosql(oForm, object_class, object_id);
  
  oForm.commentaire.value = commentaire;
  return onSubmitFormAjax(oForm);
}


/***************/

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
</script>

{{include file="../../dPprescription/templates/js_functions.tpl"}}

<form name="addPriseElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="CPrescriptionLineElement" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="nb_fois" value="" />
  <input type="hidden" name="unite_fois" value="" />
  <input type="hidden" name="moment_unitaire_id" value="" />
  <input type="hidden" name="nb_tous_les" value="" />
  <input type="hidden" name="unite_tous_les" value="" />
  <input type="hidden" name="chapitre" value="" />
</form>
	    
<!-- Formulaire d'ajout de ligne d'element dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />  
  <input type="hidden" name="debut" value="{{$today}}" />
  <input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="duree" value="" />
  <input type="hidden" name="unite_duree" value="" />
  <input type="hidden" name="callback" value="" />
  <input type="hidden" name="element_prescription_id" value=""/>
  <input type="hidden" name="_chapitre" value="" />
</form>


<!-- Tabulations -->
<ul id="prescription_tab_group" class="control_tabs">
  <li><a href="#div_medicament">M�dicaments</a></li>

{{if !$mode_pharma}}
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
  <li><a href="#div_{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
  {{/foreach}}
{{/if}}
</ul>

<hr class="control_tabs" />

{{if $prescription->_can_add_line}}
  {{if !$mode_protocole}}
  <table class="form" style="float: right; width: 110px;">
    <tr>
      <td class="date">
        <form name="selDateLine" action="?" method="get" style="float: right"> 
      
        {{if $prescription->type != "externe"}}   
	        <select name="debut_date" 
					        onchange="$('selDateLine_debut_da').innerHTML = new String;
	 				                    this.form.debut.value = '';
	 				          				  if(this.value == 'other') { 
	 				          					  $('calendarProt').show();
	 				          				  } else { 			    
	 				          				    this.form.debut.value = this.value;
	 				          				    $('calendarProt').hide();
	 				          				  }">
	     				  
				    <option value="other">Autre date</option>
				    <optgroup label="S�jour">
				      <option value="{{$prescription->_ref_object->_entree|date_format:'%Y-%m-%d'}}">Entr�e: {{$prescription->_ref_object->_entree|date_format:"%d/%m/%Y"}}</option>
				      <option value="{{$prescription->_ref_object->_sortie|date_format:'%Y-%m-%d'}}">Sortie: {{$prescription->_ref_object->_sortie|date_format:"%d/%m/%Y"}}</option>
				    </optgroup>
				    <optgroup label="Intervention">
				    {{foreach from=$prescription->_ref_object->_dates_operations item=_date_operation}}
				      <option value="{{$_date_operation}}">{{$_date_operation|date_format:"%d/%m/%Y"}}</option>
				    {{/foreach}}
						</optgroup>
				  </select>		 				
				  <!-- Prescription externe -->
				  <div id="calendarProt" style="border:none; margin-right: 60px">
				    {{mb_field object=$filter_line field="debut" form=selDateLine}}
				    {{mb_field object=$filter_line field="time_debut" form=selDateLine}}      
				  </div>
        {{else}}
           {{mb_field object=$filter_line field="debut" form="selDateLine"}}
        {{/if}}
        
         <script type="text/javascript">
	  	   Main.add( function(){
		       prepareForm(document.selDateLine);
		       Calendar.regField("selDateLine", "debut", false);
	    	} );
        </script>	
	    </form>
	    </td>
	  </tr>
	</table>
  {{/if}} 
{{/if}}



<!-- Declaration des divs -->
<div id="div_medicament" style="display:none;">
  {{include file="../../dPprescription/templates/inc_div_medicament.tpl"}}
</div>

{{if !$mode_pharma}}
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
    <div id="div_{{$_chapitre}}" style="display:none;">
      {{include file="../../dPprescription/templates/inc_div_element.tpl" element=$_chapitre}}
    </div>
  {{/foreach}}
{{/if}}

<!-- Formulaires regroup�s -->
<form name="editLineALD-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineALD-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineALD-CPrescriptionLineComment" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_comment_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ald" value="" />
</form>

<form name="editLineConditionnel-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="conditionnel" value="" />
</form>

<form name="editLineConditionnel-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="conditionnel" value="" />
</form>

<form name="validation_infirmiere-CPrescriptionLineMedicament" action="?" method="post">
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="valide_infirmiere" value="1" />
</form>

<form name="validation_infirmiere-CPrescriptionLineElement" action="?" method="post">
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="valide_infirmiere" value="1" />
</form>

<form name="validation_pharma" action="" method="post">
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="valide_pharma" value="" />
</form>

<form name="valideAllLines" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="chapitre" value="" />
  <input type="hidden" name="mode_pharma" value="" />
</form>

<form name="addComment-CPrescriptionLineMedicament" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>

<form name="addComment-CPrescriptionLineElement" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_element_id" value="" />
  <input type="hidden" name="commentaire" value="" />
</form>