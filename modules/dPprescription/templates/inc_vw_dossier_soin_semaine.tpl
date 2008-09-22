<script type="text/javascript">
  
  var date_init = new Date().toDATE();
	dates = {  
	  limit: {
	    start: date_init,
	    stop: null
	  }
	}
	
	calculDuree = function(date1, date2, oForm, now, prescription_id){
	  var dDate1 = Date.fromDATE(date1); 
	  var dDate2 = Date.fromDATE(date2); 
	  var date = dDate2 - dDate1;
	  nb_days = date / (24 * 60 * 60 * 1000);
	  oForm.duree.value = parseInt(oForm.duree.value,10) + nb_days;
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
	    calculSoinSemaine(now,prescription_id); 
	  } });              		        
	}
	
Main.add(function () {
  var tabs = Control.Tabs.create('tab_categories_plan', true);
});

</script>

{{if $prescription->_id}}
	<table class="tbl">
	  <tr>
	    <th colspan="3" class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
	  </tr>
	  <tr>
	    <td>Poids: {{$patient->_ref_constantes_medicales->poids}} kg</td>
	    <td>Age: {{$patient->_age}}</td>
	    <td>Taille: {{$patient->_ref_constantes_medicales->taille}}
	  </tr>
	</table>
	<table>
	  <tr>
	    <td style="width: 1%">
			 <table>
			 	<tr>
				  <td>
					  <ul id="tab_categories_plan" class="control_tabs_vertical">
						  {{if $prescription->_lines.med}}
						    <li><a href="#plan_med">M�dicaments</a></li>
						  {{/if}}
							{{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
							{{foreach from=$specs_chapitre->_list item=_chapitre}}
							  {{if array_key_exists($_chapitre, $prescription->_lines.elt)}}
							    <li><a href="#plan_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
							  {{/if}}
							{{/foreach}}
					  </ul>	
		 	      </td>
	 	      </tr>
	      </table>  
	    </td>
	    <td>
	      <table class="tbl">  
				  <tr>
				    <th>Cat�gorie</th>
				    <th>Libelle</th>
				    {{foreach from=$dates item=date}}
				    <th>
				      {{$date|date_format:"%d/%m/%Y"}}
				    </th>
				    {{/foreach}}
				  </tr>
	
				  <!-- Affichage des medicaments -->
				  <tbody id="plan_med" style="display: none;">
				  {{foreach from=$prescription->_lines.med item=lines_unite_prise name="foreach_line"}}
				    {{assign var=prescription_id value=$prescription->_id}}
				    {{foreach from=$lines_unite_prise key=unite_prise item=line_med name="foreach_med"}}
				      <!-- Si l'unite de prise est bien exprim� en format texte et pas en identifiant de prise -->
				       {{if $smarty.foreach.foreach_med.first}}
				        {{include file="inc_vw_line_dossier_soin_semaine.tpl" 
				                  line=$line_med 
				                  dosql=do_prescription_line_medicament_aed 
				                  type=med
				                  nodebug=true
				                  first_foreach=foreach_med
				                  last_foreach=foreach_line}}    
					    {{/if}}
				    {{/foreach}}
				  {{/foreach}}
				  </tbody>
	  
					<!-- Affichage des elements -->
					{{foreach from=$prescription->_lines.elt key=name_chap item=elements_chap name="foreach_element"}}
					  {{if !$smarty.foreach.foreach_element.first}}
						</tbody>
						{{/if}}
						<tbody id="plan_cat-{{$name_chap}}" style="display: none;">  
					 {{foreach from=$elements_chap key=name_cat item=elements_cat}}
					   {{assign var=categorie value=$categories.$name_chap.$name_cat}}
					   {{foreach from=$elements_cat item=_element name="foreach_cat"}}
					     {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 
					        {{if $smarty.foreach.foreach_elt.first}}
					         {{include file="inc_vw_line_dossier_soin_semaine.tpl" 
					                   line=$element 
					                   dosql=do_prescription_line_element_aed 
					                   type=elt
					                   nodebug=true
					                   first_foreach=foreach_cat
							               last_foreach=foreach_elt}} 
					        {{/if}}
					      {{/foreach}}
					    {{/foreach}}
					  {{/foreach}}
					{{/foreach}}	
	        </tbody>
	      </table>
	    </td>
	  </tr>
	</table>
{{else}}
  <div class="big-info">
    Ce dossier ne poss�de pas de prescription de s�jour
  </div>
{{/if}} 