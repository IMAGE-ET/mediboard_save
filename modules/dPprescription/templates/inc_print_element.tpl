{{assign var=chapitre_category value=$elt->_ref_element_prescription->_ref_category_prescription->chapitre}}
{{if !$elt->_protocole}}
<li>
  <strong>{{$elt->_ref_element_prescription->_view}} {{if $elt->conditionnel}}(Conditionnel){{/if}}</strong>

  {{if $elt->commentaire}}
  <em>({{$elt->commentaire}})</em>
  {{/if}}
  {{if  $chapitre_category != "dmi" && ($elt->_ref_prises|@count || $elt->_duree_prise || $elt->date_arret) }}
	  <ul>
	    <li>
			  <!-- Affichage des prises s'il y en a -->
			    {{foreach from=$elt->_ref_prises item=prise name=foreach_prise}}
				    {{if $prise->quantite}}
				        {{$prise->_view}}
				        {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
				    {{/if}}
				  {{/foreach}}
				   
				  {{$elt->_duree_prise}}
									
				  <!-- Affichage de la date d'arret -->
					{{if $elt->date_arret}}
					  (Element arr�t� le {{$elt->date_arret|date_format:"%d/%m/%Y"}}) 
					{{/if}}
			</li>
	  </ul>
  {{/if}}    
</li>
{{else}}
<li>
  <strong>{{$elt->_ref_element_prescription->_view}} {{if $elt->conditionnel}}(Conditionnel){{/if}}</strong>

  {{if $elt->commentaire}}
  <em>({{$elt->commentaire}})</em>
  {{/if}}
  {{if  $chapitre_category != "dmi"}}
	  <ul>
	    <li>
			<!-- Affichage des prises s'il y en a -->
		  {{foreach from=$elt->_ref_prises item=prise}}
		    {{if $prise->quantite}}
		      {{$prise->_view}}, 
		    {{/if}}
		  {{/foreach}}
		  
		  <!-- Duree de la prise --> 
		 {{if $elt->duree}}
     Dur�e de {{mb_value object=$elt field=duree}} jour(s) 
    {{/if}}
    
    <!-- Date de debut de la ligne -->
    {{if $elt->jour_decalage && $elt->unite_decalage}} 
      {{if $elt->duree > 1 || $elt->jour_decalage_fin}}
	    � partir de
	    {{else}}
	    �
	    {{/if}}
			{{if $prescription->object_class == "CSejour"}}
			 {{assign var=line_jour_decalage value=$elt->jour_decalage}}
			 {{$traduction.$line_jour_decalage}}
			{{else}}
			 J
			{{/if}}
			
			{{if ($elt->unite_decalage == "jour" && $elt->decalage_line > 0) || ($elt->unite_decalage == "heure")}}
			{{if $elt->decalage_line >= 0}}+{{/if}} {{mb_value object=$elt field=decalage_line size="3"}}
			{{if $prescription->object_class == "CSejour"}}
			  {{mb_value object=$elt field=unite_decalage}}
			{{else}}
			 (jours)
			{{/if}} 
			{{/if}}
			
			 <!-- Heure de debut -->
			 {{if $elt->time_debut}}
				 � {{mb_value object=$elt field=time_debut}}
			 {{/if}}
		 {{/if}}
		 
		 {{if $elt->jour_decalage_fin && $elt->unite_decalage_fin}}
		   {{assign var=line_jour_decalage_fin value=$elt->jour_decalage_fin}}
			 <!-- Date de fin -->
			 jusqu'� {{$traduction.$line_jour_decalage_fin}}
			 
			 {{if ($elt->unite_decalage_fin == "jour" && $elt->decalage_line_fin > 0) || ($elt->unite_decalage_fin == "heure")}}
				 {{if $elt->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$elt field=decalage_line_fin increment=1 }}
				 {{mb_value object=$elt field=unite_decalage_fin }}
			 {{/if}}
			 <!-- Heure de fin -->
			 {{if $elt->time_fin}} 
				� {{mb_value showPlus=1 object=$elt field=time_fin}}		
			 {{/if}}	
		 {{elseif !$elt->duree}}
		 pendant 1 jour
		 {{/if}}
		 
		 {{if !$elt->duree && !($elt->jour_decalage && $elt->unite_decalage) && !($elt->jour_decalage_fin && $elt->unite_decalage_fin)}}
		 Aucune date
		 {{/if}}
     {{if $elt->commentaire}}
       , {{mb_value object=$elt field="commentaire"}}
     {{/if}}
     
			</li>
	  </ul>
  {{/if}}    
</li>
{{/if}}