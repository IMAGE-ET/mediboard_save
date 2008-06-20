{{assign var=dosql value="do_prescription_line_medicament_aed"}}
{{assign var=line value=$curr_line}}
{{assign var=div_refresh value="medicament"}}
{{assign var=typeDate value="Med"}}

<tbody id="line_medicament_{{$line->_id}}" class="hoverable 
  {{if $line->_traitement}}traitement{{else}}med{{/if}}
  {{if $line->_date_arret_fin && $line->_date_arret_fin < $now}}line_stopped{{/if}}">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="{{if $line->_traitement}}traitement{{/if}}
               {{if $line->_date_arret_fin && $line->_date_arret_fin < $now}}arretee{{/if}}">
      <script type="text/javascript">
         Main.add( function(){
           moveTbody($('line_medicament_{{$line->_id}}'));
         });
      </script>
      <div style="float:left;">
        {{if $line->_can_view_historique}}
          <img src="images/icons/history.gif" alt="Ligne poss�dant un historique" title="Ligne poss�dant un historique"/>
        {{/if}}
        {{if !$line->_traitement}}
	        <!-- Selecteur equivalent -->
	        {{if $line->_can_select_equivalent}}
	          {{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
	        {{/if}}	        
	        <!-- Formulaire ALD -->
		      {{if $line->_can_view_form_ald}}
	            {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
		      {{/if}}
		    {{/if}}  
	      <!-- Formulaire Traitement -->
        {{if $line->_can_vw_form_traitement}} 
          {{include file="../../dPprescription/templates/line/inc_vw_form_traitement.tpl"}}
        {{/if}} 
      </div>
      
      <!-- AFfichage de la signature du praticien -->
      <div style="float: right">
        {{if $line->_can_view_signature_praticien}}
            {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else}}
          {{if !$line->_traitement}}
            {{$line->_ref_praticien->_view}}    
          {{/if}}
        {{/if}}
        {{if $mode_pharma}}
        <!-- Vue pharmacie -->
          {{if !$line->_protocole}}
            {{include file="../../dPprescription/templates/line/inc_vw_form_accord_praticien.tpl"}}
            {{include file="../../dPprescription/templates/line/inc_vw_form_validation_pharma.tpl"}}
          {{/if}}
        {{elseif !$line->_protocole}}
        <!-- Vue normale  -->
          {{if $line->_traitement}}
            M�decin traitant (Cr�� par {{$line->_ref_praticien->_view}})
          {{else}}
					  {{if !$line->valide_pharma}}
						  {{if $line->_can_view_form_signature_praticien}}
							  {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
							{{elseif $line->_can_view_form_signature_infirmiere}}
							  {{include file="../../dPprescription/templates/line/inc_vw_form_validation_infirmiere.tpl"}}
							{{/if}}
					  {{else}}
						  (Valid� par le pharmacien)
					  {{/if}}	
			    {{/if}}
        {{/if}}
      </div>
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})">
        <strong>{{$line->_view}}</strong>
      </a>
    </th>
  </tr>
  <!-- Pas traitement ni protocole -->
  <tr>
    <td style="text-align: center">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non pr�sent dans le livret Th�rapeutique" title="Produit non pr�sent dans le livret Th�rapeutique" />
      <br />
      {{/if}}  
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      <br />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit g�n�rique" title="Produit g�n�rique" />
      <br />
      {{/if}}
    </td>
    
    {{if !$line->_protocole}}
    <td colspan="2">
      {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl"}}  
      <script type="text/javascript">
	      if(document.forms["editDates-Med-{{$line->_id}}"]){
		      var oForm = document.forms["editDates-Med-{{$line->_id}}"];
		      prepareForm(oForm);
		      
		      if(oForm.debut){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "debut", false, dates);
		      }
		      if(oForm._fin){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "_fin", false, dates);			      
		      }
		      if(oForm.fin){
		        Calendar.regField('editDates-Med-{{$line->_id}}', "fin", false, dates);		      
		      }
	      }
      </script>
	  </td>
    <td>
      <!-- Formulaire permettant de stopper la prise (seulement si type == "sejour" ou si type == "pre_admission" )-->
      {{if $prescription_reelle->type != "sortie"}}
        <div id="stop-CPrescriptionLineMedicament-{{$line->_id}}">
          {{include file="../../dPprescription/templates/line/inc_vw_stop_line.tpl" object_class="CPrescriptionLineMedicament"}}
        </div>
      {{/if}}
    </td>
    {{else}}
    <td colspan="3" />
    {{/if}}
  </tr> 
  
  
  <!-- Si protocole, possibilit� de rajouter une dur�e et un decalage entre les lignes -->
  {{if $line->_protocole}}
    {{include file="../../dPprescription/templates/line/inc_vw_duree_protocole_line.tpl"}}
  {{/if}}  
  
  <tr>  
	  <td style="text-align: center">
	    <!-- Affichage des alertes -->
	    {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	  </td>  
    <td colspan="3">
      <table style="width:100%">
        <tr>
          <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
			      <!-- Selection des posologies BCB -->
			      {{include file="../../dPprescription/templates/line/inc_vw_form_select_poso.tpl"}}
			      <!-- Ajout de posologies -->			       
			      {{if $line->_can_modify_poso}}
			        {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl" type="Med"}}	  
						{{/if}}
	        </td>
          <td style="border:none; padding: 0;"><img src="images/icons/a_right.png" title="" alt="" /></td>
	        <td style="border:none; text-align: left;">
	          {{if $line->_can_modify_poso}}
              <!-- Affichage des prises (modifiables) -->
              <div id="prises-Med{{$line->_id}}">
                {{include file="../../dPprescription/templates/line/inc_vw_prises_posologie.tpl" type="Med"}}
              </div>
            {{else}}
              <!-- Affichage des prises (non modifiables) -->
              {{if $line->_ref_prises|@count}}
              <ul>
              {{foreach from=$line->_ref_prises item=prise}}
                {{if $prise->quantite}}
                  <li>{{$prise->_view}}</li> 
                {{/if}}
              {{/foreach}}
              </ul>
              {{else}}
                Aucun posologie
              {{/if}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>    
  <tr>  
    <td>
      <!-- Suppression de la ligne -->
      {{if $line->_can_delete_line}}
        <button type="button" class="trash notext" onclick="Prescription.delLine({{$line->_id}})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    </td>
    <td colspan="4">
      <!-- Ajouter une ligne (m�me dans le cas du traitement)-->
      {{if $line->_can_vw_form_add_line_contigue}}
	      <div style="float: right;">
	        {{include file="../../dPprescription/templates/line/inc_vw_form_add_line_contigue.tpl"}}
	      </div>
      {{/if}}
      <!-- Ins�rer un commentaire dans la ligne -->
      {{include file="../../dPprescription/templates/line/inc_vw_form_add_comment.tpl"}}
    </td>
  </tr>
</tbody>