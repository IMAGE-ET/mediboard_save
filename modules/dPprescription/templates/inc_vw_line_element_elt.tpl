<!-- Initialisation des variables -->
{{assign var=line value=$_line_element}}
{{assign var=dosql value="do_prescription_line_element_aed"}}
{{assign var=div_refresh value=$element}}
{{assign var=typeDate value=$element}}
{{assign var=category value=$line->_ref_element_prescription->_ref_category_prescription}}
<tbody id="line_element_{{$line->_id}}" class="hoverable elt
       {{if $line->_date_arret_fin && $line->_date_arret_fin < $now && !$line->_protocole}}line_stopped{{/if}}">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th id="th_line_CPrescriptionLineElement_{{$line->_id}}" colspan="8"
        class="element {{if $line->_date_arret_fin && $line->_date_arret_fin < $now && !$line->_protocole}} arretee{{/if}}">
      <script type="text/javascript">
         Main.add( function(){
           moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
         });
      </script>
      <div style="position: absolute">
        <!-- Formulaire ALD -->
          {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}} 
      </div>    
      <div class="div_signature">
        <!-- Affichage de la signature du praticien -->
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else}}
          {{$line->_ref_praticien->_view}}    
        {{/if}}  
	      <!-- Affichage du formulaire de signature du praticien --> 
        {{if $line->_can_view_form_signature_praticien}} 
	        {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
	      {{/if}}
	      <!-- Affichage du formulaire de signature de l'infirmiere -->
	      {{if $line->_can_view_form_signature_infirmiere}} 
	        {{include file="../../dPprescription/templates/line/inc_vw_form_validation_infirmiere.tpl"}}  
	      {{/if}}
	    </div>
	    <!-- View de l'element -->
	    {{$line->_ref_element_prescription->_view}}
	  </th>
	</tr>
  {{if $category->chapitre != "dmi"}}
  <!-- Si protocole, possibilit� de rajouter une dur�e et un decalage entre les lignes -->
  {{if $line->_protocole}}
    {{include file="../../dPprescription/templates/line/inc_vw_duree_protocole_line.tpl"}}
  {{/if}}
  <tr>
    <td style="width: 25px" {{if $category->chapitre != "dmi"}}rowspan="3"{{/if}} >
      {{if $line->_can_delete_line}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$line->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    <!-- Gestion des dates -->
    {{if !$line->_protocole}}
    <td colspan="2">
      {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}
	      <script type="text/javascript">
	        if(document.forms["editDates-{{$typeDate}}-{{$line->_id}}"]){
		        var oForm = document.forms["editDates-{{$typeDate}}-{{$line->_id}}"]
		        prepareForm(oForm); 
		        if(oForm.debut){
		          Calendar.regField('editDates-{{$typeDate}}-{{$line->_id}}', "debut", false, dates);
		        }
		        if(oForm._fin){
		          Calendar.regField('editDates-{{$typeDate}}-{{$line->_id}}', "_fin", false, dates);      
		        }
		        if(oForm.fin){
		           Calendar.regField('editDates-{{$typeDate}}-{{$line->_id}}', "fin", false, dates);    
		        }
	        }
	      </script>
    </td>

    {{/if}}
  </tr>
  
  <!-- Affichage des pososlogies -->
  {{if $category->chapitre != "anapath" && $category->chapitre != "consult" && $category->chapitre != "imagerie"}}
  <tr>
    <td colspan="3">
      {{if $line->_can_modify_poso}}
	      <table style="width: 100%">
	       <tr>
			    <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
			      {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Soin"}}
			    </td>
			    <td style="border:none">
			      <img src="images/icons/a_right.png" title="" alt="" />
			    </td>
				  <td style="border:none; text-align: left;">
			      <div id="prises-Soin{{$line->_id}}">
			        <!-- Parcours des prises -->
			        {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Soin"}}
			      </div>
			    </td>
	      </table>
      {{else}}
        <table>
          <tr>
          <!-- 
            <td style="border:none;">
         
             {{if !$line->signee}}
			        <div class="little-info">
						     Vous devez �tre le responsable de cette ligne pour rajouter des posologies <br />
						  </div>
						  {{/if}}
						  
			      </td>
			       -->
			      <td style="border:none;"> 
			        <!-- Affichage des prises -->
			        {{if $line->_ref_prises|@count}}
			        <ul>
			        {{foreach from=$line->_ref_prises item=prise name=foreach_prise}}
			          <li>{{$prise->_view}}</li>
			        {{/foreach}}
			        </ul>
			        {{else}}
			         Aucune posologie
			        {{/if}}
			      </td>
          </tr>
        </table>
      {{/if}}
    </td>
  </tr>
  {{/if}}
  {{/if}}
  <tr>
    {{if $category->chapitre == "dmi"}}
    <td style="width: 25px">
      {{if $line->_can_delete_line}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$line->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    </td>
    {{/if}}
    <td {{if $category->chapitre != "dmi"}}colspan="3"{{else}}colspan="6"{{/if}}>
      {{if $prescription->type == "sortie" || $line->_protocole}}
	      <div style="float: right">
	        <!-- Formulaire de selection d'un executant -->
	        {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
	      </div>
      {{/if}}
      <!-- Formulaire d'ajout de commentaire -->
      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
    </td>   
  </tr>
  {{if (($category->chapitre == "biologie" || $category->chapitre == "kine" || $category->chapitre == "soin" || $category->chapitre == "dm") && $prescription->type != "sortie") && !$line->_protocole }}
  <tr>
  <td></td>
    <td>
	    <div style="float: right">
		    <!-- Formulaire de selection d'un executant -->
		    {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
		  </div>
	 
     {{if ($prescription->type == "sejour" || $prescription->type == "pre_admission") && !$line->_protocole}}
        <div id="stop-CPrescriptionLineElement-{{$line->_id}}"> 
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineElement"}}
        </div>
     {{/if}}
    </td>
    
  
  </tr>
  {{/if}}
</tbody>