<script type="text/javascript">      
     
moveTbodyElt = function(oTbody, cat_id){
  var oTableElt = $('elt_'+cat_id);
	var oTableEltArt = $('elt_art_'+cat_id);
	
	if(oTbody.hasClassName('elt')){
    if(oTbody.hasClassName('line_stopped')){
      oTableEltArt.insert(oTbody);		  
    } else {
      oTableElt.insert(oTbody);		  
    }	
  }
}


// On vide toutes les valeurs du formulaire d'ajout d'element

	var oForm = document.addLineElement;
	oForm.prescription_line_element_id.value = "";
	oForm.del.value = "0";
	oForm.element_prescription_id.value = "";
	
	// Preselection des executants
	preselectExecutant = function(executant_id, category_id){
	 $$('select.executant-'+category_id).each( function(select) {
	   select.value = executant_id;
	   select.onchange();
	 })
	}

changePraticienElt = function(praticien_id, element){
  var oFormAddLineElement = document.addLineElement;
  var oFormAddLineCommentElement = document.forms['addLineComment'+element];
  
  oFormAddLineElement.praticien_id.value = praticien_id;
  if(oFormAddLineCommentElement){
    oFormAddLineCommentElement.praticien_id.value = praticien_id;
  }
}

// On met � jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
	  changePraticienElt(document.selPraticienLine.praticien_id.value, '{{$element}}');
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


</script>

<!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
{{if $prescription->object_id && $is_praticien}}
<div style="float: right">
  <form name="valideAllLines-{{$element}}" method="post" action="">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
    <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    <input type="hidden" name="chapitre" value="{{$element}}" />
    <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
    	Signer les lignes "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}"
    </button>
  </form>
</div>
{{/if}}

<!-- Formulaire d'ajout de ligne d'elements et de commentaires -->
{{if $prescription->_can_add_line}}
  {{include file="inc_vw_form_addLine.tpl"}}
{{else}}
  <div class="big-info">
    L'ajout de lignes dans la prescription est r�serv� aux praticiens ou aux infirmi�res 
    entre {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_start}} heures et {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_stop}} heures
  </div>
{{/if}}

{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}

  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  {{assign var=nb_lines value=0}}
  
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$element.$category_id}}
	  
	  <!-- Elements d'une categorie-->
	  <table class="tbl" id="elt_{{$category->_id}}">

	  <tr>
	    <th class="title" colspan="9">{{$category->_view}}</th>
	  </tr>	  
	  </table>
	  <table class="tbl" id="elt_art_{{$category->_id}}">

	  </table>
    <table class="tbl">
	  {{foreach from=$lines_cat.element item=line_element}}
	    {{if !($prescription->type == "sortie" && $praticien_sortie_id != $line_element->praticien_id) || !$praticien_sortie_id}}
	      {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element prescription_reelle=$prescription}}
	    {{/if}}
	  {{/foreach}}
	  </table>
	  
	  <!-- Commentaires d'une categorie -->
	  <table class="tbl">
	  {{if $lines_cat.comment|@count}}
	  <tr>
	    <th colspan="9" class="element">Commentaires</th>
	  </tr>
	  {{/if}}
	  {{foreach from=$lines_cat.comment item=line_comment}}
	    {{if !($prescription->type == "sortie" && $praticien_sortie_id != $line_comment->praticien_id) || !$praticien_sortie_id}}
	      {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment prescription_reelle=$prescription}}
	    {{/if}}
	  {{/foreach}}
	  </table>
	  {{/foreach}}
	  
	  
  {{else}}
  <div class="big-info"> 
     Il n'y a aucun �l�ment de type "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}" dans cette prescription.
  </div>
  {{/if}}
  
  <br />
  
  {{if $prescription->object_id}}
  <!-- Affichage de l'historique -->
  <table class="tbl">
		{{foreach from=$historique key=type_prescription item=hist_prescription}}
		  {{if is_array($hist_prescription->_ref_lines_elements_comments) && array_key_exists($element, $hist_prescription->_ref_lines_elements_comments)}}
	    {{foreach from=$hist_prescription->_ref_lines_elements_comments.$element item=_hist_lines name="foreach_hist_elt"}}
		    {{if $smarty.foreach.foreach_hist_elt.first}}
			    <tr>
			      <th colspan="7" class="title">Historique {{tr}}CPrescription.type.{{$type_prescription}}{{/tr}}</th>
			    </tr>
	      {{/if}}
			  {{foreach from=$_hist_lines item=_hist_line}}
			    {{foreach from=$_hist_line item=_line}}
			      <tr>
			        <!-- Affichage d'une ligne de commentaire -->
			        {{if $_line->_class_name == "CPrescriptionLineComment"}}
			          <td colspan="4">
			            {{$_line->commentaire}}
			          </td>
			        {{else}}
			          {{assign var=chapitre value=$_line->_ref_element_prescription->_ref_category_prescription->chapitre}}
			          <!-- Affichage d'une ligne d'element -->
					      <td
					      {{if $chapitre == "dmi"}}
					       colspan="4"
					      {{/if}}><a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$_line->_class_name}}', object_id: {{$_line->_id}} } })">{{$_line->_view}}</a></td>
					      
					      {{if $chapitre != "dmi"}}
						      {{if !$_line->fin}}
							    <td>
							      {{mb_label object=$_line field="debut"}}: {{mb_value object=$_line field="debut"}}
							    </td>
							    {{if $chapitre != "anapath" && $chapitre != "imagerie" && $chapitre != "consult"}}
							    <td>
							      {{mb_label object=$_line field="duree"}}: 
							        {{if $_line->duree && $_line->unite_duree}}
							          {{mb_value object=$_line field="duree"}}  
							          {{mb_value object=$_line field="unite_duree"}}
							        {{else}}
							        -
							        {{/if}}
							    </td>
							    <td>
							      {{mb_label object=$_line field="_fin"}}: {{mb_value object=$_line field="_fin"}}
							    </td>
							    {{/if}}
							    {{else}}
							    <td colspan="3">
							      {{mb_label object=$_line field="fin"}}: {{mb_value object=$_line field="fin"}}
							    </td>
						      {{/if}}
					      {{/if}}
				      {{/if}}
				        <td>
					        Praticien: {{$_line->_ref_praticien->_view}}
					      </td>
                <td>
				      		{{mb_label object=$_line field="ald"}}:
						      {{if $_line->ald}}
						        Oui
						      {{else}}
						        Non
						      {{/if}}
				        </td>
				        <td>
			            Ex�cutant: {{$_line->_ref_executant->_view}}
			          </td>
				    </tr>
			    {{/foreach}}
			  {{/foreach}}
			{{/foreach}}
			{{/if}}
		{{/foreach}}
	</table>
	
  {{/if}}
<script type="text/javascript">

Prescription.refreshTabHeader('div_{{$element}}','{{$prescription->_counts_by_chapitre.$element}}');

if(document.search{{$element}}){
	// Autocomplete
	prepareForm(document.search{{$element}});
	  
	url = new Url();
	url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
	url.addParam("category", "{{$element}}");
	url.autoComplete("search{{$element}}_{{$element}}", "{{$element}}_auto_complete", {
	  minChars: 2,
	  updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
	} );
}
</script>