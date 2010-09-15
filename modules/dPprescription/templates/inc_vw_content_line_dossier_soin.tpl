{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
{{if $move_dossier_soin}}
	Main.add(function () {
  	// On vide les valeurs du formulaires d'ajout/modification de planification
    document.addPlanif.select('input').each(function(element){
      if(element.name != "dosql" && element.name != "m" && element.name != "del" && element.name != "planification"){
         $V(element, "");
       }
    });
  });
{{/if}}
</script>

{{* Parcours du tableau de dates *}}
{{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
  {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
    {{foreach from=$_dates key=_date item=_hours}}
      {{foreach from=$_hours key=_heure_reelle item=_hour}}
        
			  {{assign var=administrations_in_hour value=""}}	
				{{if isset($line->_administrations.$unite_prise.$_date.$_hour|smarty:nodefaults)}}
				  {{assign var=administrations_in_hour value=$line->_administrations.$unite_prise.$_date.$_hour}}
        {{/if}}
				
        {{assign var=transmissions_in_hour value=""}}  
        {{if isset($line->_transmissions.$unite_prise.$_date.$_hour|smarty:nodefaults)}}
          {{assign var=transmissions_in_hour value=$line->_transmissions.$unite_prise.$_date.$_hour}}
        {{/if}}
				
				{{assign var=line_plan_soin value=""}}
				{{if isset($line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour|smarty:nodefaults)}}
			    {{assign var=line_plan_soin value=$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour}}	
				{{/if}}
		
					{{* Stockage de la liste des administrations *}}
				  {{assign var=list_administrations value=""}}
				  {{if @$administrations_in_hour.list}}
				    {{assign var=list_administrations value=$administrations_in_hour.list}}
				  {{/if}}		
				    
				  {{* Information sur une eventuelle planification *}}
				  {{assign var=planification_id value=""}}
				  {{assign var=origine_date_planif value=""}}
				  {{if @$administrations_in_hour.planification_id}}
				    {{assign var=planification_id value=$administrations_in_hour.planification_id}}
				    {{assign var=origine_date_planif value=$administrations_in_hour.original_date_planif}}
				  {{/if}}
				  		  
				  {{* Construction du dateTime courant *}}
				  {{assign var=_date_hour value="$_date $_heure_reelle"}}						    				
					
					{{* Initialisations *}}
					{{assign var=quantite value="-"}}
					{{assign var=quantite_depart value="-"}}
					{{assign var=heure_reelle value=""}}				
					
					{{* Quantite planifi�e *}}          
		      {{if @$administrations_in_hour.quantite_planifiee}}
		        {{assign var=quantite value=$administrations_in_hour.quantite_planifiee}}
		      {{else}}
			      {{if @array_key_exists($_hour, @$line->_quantity_by_date.$unite_prise.$_date.quantites)}}
					    {{assign var=quantite value=$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour.total}}
					    {{*  Heure reelle de la prise prevue *}}
					    {{if @$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour.0.heure_reelle}}
					      {{assign var=heure_reelle value=$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour.0.heure_reelle}}
					    {{/if}}
				    {{/if}}
				  {{/if}}
				  
				  {{* Sauvegarde de la quantite *}}
				  {{assign var=_quantite value=$quantite}}
					{{if !$heure_reelle}}
					  {{assign var=heure_reelle value=$_hour}}
					{{/if}}					
					
					
					{{* Si une case du plan de soins est compos�e de plusieurs administrations *}}
				  {{assign var=multiple_adm value=0}}
          {{if isset($line_plan_soin.nb_adm|smarty:nodefaults) && $line_plan_soin.nb_adm > 1}}
				    {{assign var=multiple_adm value=1}}
				  {{/if}}
				 
					 
					{{* Affichage de la case *}} 
				  <td id="drop_{{$line_id}}_{{$line_class}}_{{$unite_prise}}_{{$_date}}_{{$_hour}}" 
				   		class="{{$line_id}}_{{$line_class}} {{$_view_date}}-{{$moment_journee}} {{if ($quantite == '0' || $quantite == '-')}}canDrop{{/if}} colorPlanif {{$_hour}}" 
				   		style='display: none; text-align: center; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
				   			   
					  <div id="drag_{{$line_id}}_{{$unite_prise}}_{{$_date}}_{{$heure_reelle}}_{{$_quantite}}_{{$planification_id}}"
					       onmouseover='{{if $origine_date_planif || @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
					  										ObjectTooltip.createDOM(this, "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}");
					  								  {{/if}}'
					  		{{if ($line->_fin_reelle && ($line->_fin_reelle|date_format:"%Y-%m-%d %H:00:00" < $_date_hour)) || $line->_debut_reel|date_format:"%Y-%m-%d %H:00:00" > $_date_hour || !$line->_active}}
						      style="background-color: #aaa"
						      {{if $dPconfig.dPprescription.CAdministration.hors_plage}}
						        onclick='toggleSelectForAdministration(this, {{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_hour}}:00:00");'
					          ondblclick='addAdministration({{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_hour}}:00:00","{{$list_administrations}}","{{$planification_id}}","{{$multiple_adm}}");'
						      {{/if}}
						    {{else}}
						      onclick='toggleSelectForAdministration(this, {{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_hour}}:00:00","{{$list_administrations}}","{{$planification_id}}");'
					        ondblclick='addAdministration({{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_hour}}:00:00","{{$list_administrations}}","{{$planification_id}}","{{$multiple_adm}}");'
						    {{/if}}
						  
						     class="{{if $quantite!="-" && @$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour|@count < 5}}draggablePlanif{{/if}} administration
									      {{if $quantite > 0}}
											    {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date) && @$administrations_in_hour.quantite != ''}}
												    {{if @$administrations_in_hour.quantite == $quantite}} administre 
												    {{elseif @$administrations_in_hour.quantite == 0}} administration_annulee 
												    {{else}} administration_partielle {{/if}}
											    {{else}}
											      {{if $_date_hour < $now}} non_administre {{else}} a_administrer {{/if}}
											    {{/if}}
										    {{/if}}
							 					{{if @$line->_transmissions.$unite_prise.$_date.$_hour.nb}}transmission{{/if}}
							 					{{if @$administrations_in_hour.quantite_planifiee}}planification{{/if}}
												{{if $multiple_adm}}multiple_adm{{/if}}">
							 					 						 
							 {{* Affichage du contenu de la case (quantite administree / quantite prevue) *}}
				       {{if $quantite!="-" || @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
								 {{* Initialisation de la quantite *}}
								 {{if !$quantite}}
								   {{assign var=quantite value="0"}}
								 {{/if}}
								 
								 {{if @$administrations_in_hour.quantite_planifiee}}
								    {{if @$administrations_in_hour.quantite}}
								      {{$administrations_in_hour.quantite}}
								    {{else}}
								      0
								    {{/if}}
								    /{{$administrations_in_hour.quantite_planifiee}}
								 {{else}}
						       {{if @$administrations_in_hour.quantite}}
							       {{$administrations_in_hour.quantite}}/{{$quantite}}
						       {{elseif $line->_active}}
							       {{if $quantite}}0/{{$quantite}}{{/if}}
							     {{/if}}
								 {{/if}}
						   {{/if}}   
				 </div>	
 
         {{if isset($line_plan_soin.nb_adm|smarty:nodefaults) && $line_plan_soin.nb_adm > 1}}
				   
					 {{assign var=adm_in_hour value=""}}
					 {{if isset($administrations_in_hour.adm_in_hour|smarty:nodefaults)}}
				     {{assign var=adm_in_hour value=$administrations_in_hour.adm_in_hour}}
				   {{/if}}
					 
           <button type="button" class="down notext" onclick="$('subadm_{{$line_id}}_{{$line_class}}_{{$unite_prise}}_{{$_date}}_{{$_hour}}').toggle();"></button>
           <div id="subadm_{{$line_id}}_{{$line_class}}_{{$unite_prise}}_{{$_date}}_{{$_hour}}" style="display: none;" class="tooltip">
             {{foreach from=$line_plan_soin item=_sub_adm}}
               {{if $_sub_adm.quantite}}
							   {{assign var=minute value=$_sub_adm.full_hour|date_format:"%M"}}
							   <div class="administration 
								      {{if isset($adm_in_hour.$minute|smarty:nodefaults)}}
											  {{if $adm_in_hour.$minute == $_sub_adm.quantite}}administre{{else}}administration_partielle{{/if}}
											{{else}}
											  a_administrer 
											{{/if}}" 
											style="margin: 2px;"
											onclick='toggleSelectForAdministration(this,{{$line_id}}, "{{$_sub_adm.quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_sub_adm.full_hour}}:00");'
                      ondblclick='addAdministration({{$line_id}}, "{{$_sub_adm.quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}} {{$_sub_adm.full_hour}}:00","","");'>
									  
										{{$_sub_adm.full_hour|date_format:"%Hh%M"}}:
										{{if isset($adm_in_hour.$minute|smarty:nodefaults) && $adm_in_hour.$minute != $_sub_adm.quantite}}
										  {{$adm_in_hour.$minute}} /
										{{/if}}
										{{$_sub_adm.quantite}} {{$line->_unite_administration}}
						     </div>
						   {{/if}}
             {{/foreach}}
           </div>
         {{/if}}

					{{if $quantite && @$line->_quantity_by_date.$unite_prise.$_date.quantites.$_hour|@count < 5}}
			    <script type="text/javascript">
			      // Pour empecher de deplacer une case ou il y a plusieurs prises
		        drag = new Draggable("drag_{{$line_id}}_{{$unite_prise}}_{{$_date}}_{{$heure_reelle}}_{{$_quantite}}_{{$planification_id}}", oDragOptions);
          </script>
				  {{/if}}

			    {{* Tooltip d'affichage de la date d'origine, des administrations et des transmissions *}}  
			    <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
				     {{if $origine_date_planif}}
				       <strong>Date d'origine:</strong> {{$origine_date_planif|date_format:$dPconfig.datetime}}<br />
				     {{/if}}
				     {{if @is_array($administrations_in_hour.administrations)}}
				       <strong>Administrations:</strong>
				     <ul>
						   {{foreach from=$administrations_in_hour.administrations item=_log_administration}}
						     {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
								 <li>
								 	 {{$_log_administration->_ref_object->quantite}}
								   {{if $line_class == "CPrescriptionLineMedicament"}}
							       {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administr� par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}
							     {{else}}
								     {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectu� par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}
							     {{/if}}
								 <li>
							   <ul>
							     {{foreach from=$transmissions_in_hour.list.$administration_id item=_transmission}}
								     <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}</li>
							     {{/foreach}}
							   </ul>
						   {{/foreach}}
					   </ul>
				     {{/if}}
	         </div>
			   </td> 
     {{/foreach}}
    {{/foreach}}		   
  {{/foreach}}
{{/foreach}}