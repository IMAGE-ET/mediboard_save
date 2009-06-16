{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=transmissions value=$prescription->_transmissions}}
{{assign var=perfusion_id value=$_perfusion->_id}}
	
	<td style="text-align: center;">
 		{{if $move_dossier_soin}}
		<script type="text/javascript">
			Main.add(function () {
		    $("line_{{$_perfusion->_guid}}").show();
		  });
		</script>
		{{/if}}
		-
	</td>
 	<td class="text">
 	  <div class="mediuser" style="border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}}">
 		<div>
	    {{if $_perfusion->_recent_modification}}
        <img style="float: right" src="images/icons/ampoule.png" alt="Ligne recemment modifi�e" title="Ligne recemment modifi�e"/>
      {{/if}}
      <!-- Pose et retrait de la perf sur le onclick de la perfusion -->
	    <a href="#{{$_perfusion->_guid}}" 
	       class="mediuser {{if @$transmissions.CPerfusion.$perfusion_id|@count}}transmission{{else}}transmission_possible{{/if}}"
	       onmouseover="ObjectTooltip.createEx(this, '{{$_perfusion->_guid}}')" 
			   onclick='editPerf("{{$_perfusion->_id}}","{{$date}}",document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}");
			            addCibleTransmission("CPerfusion","{{$_perfusion->_id}}","{{$_perfusion->_view}}");'>
	      {{$_perfusion->_view}}
	    </a>

	      <form name="editPerfusion-{{$_perfusion->_id}}" method="post" action="?" style="float: right">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_perfusion_aed" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
	        <input type="hidden" name="date_pose" value="{{$_perfusion->date_pose}}" />
	        <input type="hidden" name="time_pose" value="{{$_perfusion->time_pose}}" />
	        <input type="hidden" name="date_retrait" value="{{$_perfusion->date_retrait}}" />
	        <input type="hidden" name="time_retrait" value="{{$_perfusion->time_retrait}}" />

	        {{if !$_perfusion->date_pose}}<a href="#1" style="display: inline; border: 0px;" onclick="submitPosePerf(document.forms['editPerfusion-{{$_perfusion->_id}}']);">{{/if}}
	         <img src="images/icons/play.png" alt="" title="Pose de la perfusion" style="{{if $_perfusion->date_pose}}opacity: 0.5{{/if}}" />
	        {{if !$_perfusion->date_pose}}</a>{{/if}}

	        {{if !$_perfusion->date_retrait}}<a href="#1" style="display: inline; border: 0px;" onclick="submitRetraitPerf(document.forms['editPerfusion-{{$_perfusion->_id}}']);">{{/if}}
	         <img src="images/icons/stop.png" alt="" title="Retrait de la perfusion" style="{{if $_perfusion->date_retrait}}opacity: 0.5{{/if}}" />
	        {{if !$_perfusion->date_retrait}}</a>{{/if}}	         
	      </form>
	   
	  </div>
	  
	  {{if !$_perfusion->date_pose}}
    {{if ($_perfusion->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $_perfusion->_ref_substitution_lines.CPerfusion|@count) &&
          $_perfusion->_ref_substitute_for->substitution_plan_soin}}
    <form action="?" method="post" name="changeLine-{{$perfusion_id}}">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
      <select name="object_guid" style="width: 75px;" 
              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
      										loadTraitement(document.form_prescription.sejour_id.value,'{{$date}}','','administration');} } )">
        <option value="">Conserver</option>
        {{foreach from=$_perfusion->_ref_substitution_lines item=lines_subst_by_chap}}
          {{foreach from=$lines_subst_by_chap item=_line_subst}}
            <option value="{{$_line_subst->_guid}}">{{$_line_subst->_view}}
            {{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
	        {{/foreach}}
	      {{/foreach}}
      </select>
    </form>
    {{/if}}
    {{/if}}
	  </div>
	</td>
 	<td style="font-size: 1em;">
 	  <ul>
 	   {{foreach from=$_perfusion->_ref_lines item=_line}}
 	     <li style="margin-bottom: 7px;"><small>{{$_line->_ucd_view}} ({{$_line->_posologie}})</small></li>
 	   {{/foreach}}
 	  </ul>
 	  {{$_perfusion->_frequence}}
 	</td>	      
  <th></th>
  {{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      {{assign var=_date_hour value="$_date $_heure_reelle"}}	
			    <td class="{{$_view_date}}-{{$moment_journee}}"
			        style='cursor: pointer; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}
					    
					    {{if ($_date_hour >= $_perfusion->_debut|date_format:'%Y-%m-%d %H:00:00') && ($_date_hour < $_perfusion->_fin)}}
					      {{if ($_date_hour < $now) && $_perfusion->_pose}}
					        background-image: url(images/pictures/perf_vert.png);
					      {{else}}
					        background-image: url(images/pictures/perf_bleu.png);
					      {{/if}}
					      {{if ($_perfusion->_pose && $_perfusion->_pose <= $_date_hour)}}
					        {{if ($_perfusion->_retrait && $_perfusion->_retrait >= $_date_hour) || (!$_perfusion->_retrait && $_date_hour < $now)}}
					          background-image: url(images/pictures/perf_vert.png);
					        {{/if}}
					      {{/if}}
								{{if $_perfusion->_retrait && ($_perfusion->_retrait < $_date_hour)}}
								   background-image: url(images/pictures/perf_rouge.png);
								{{/if}}			    
					    {{else}}
					      background-color: #aaa;
					       {{if ($_perfusion->_pose && $_perfusion->_pose <= $_date_hour)}}
					        {{if ($_perfusion->_retrait && $_perfusion->_retrait > $_date_hour) || (!$_perfusion->_retrait && $_date_hour < $now)}}
					          background-image: url(images/pictures/perf_vert.png);
					        {{/if}}
					      {{/if}}
					    {{/if}}
					    background-repeat: repeat-x; background-position: bottom; margin-bottom: 10px;'>
					    
					   {{foreach from=$_perfusion->_ref_lines item=_perf_line name="foreach_perf_line"}}
						     {{if isset($_perf_line->_administrations.$_date.$_hour|smarty:nodefaults)}}
						       {{assign var=nb_adm value=$_perf_line->_administrations.$_date.$_hour}}
						     {{else}}
						       {{assign var=nb_adm value=""}}
						     {{/if}}

								 {{if isset($_perfusion->_prises_prevues.$_date.$_hour|smarty:nodefaults)}}
                   {{assign var=nb_prevue value=$_perf_line->_quantite_administration}}
                   {{assign var=hour_prevue value=$_perfusion->_prises_prevues.$_date.$_hour.real_hour}}
								 {{else}}
								   {{assign var=nb_prevue value=""}}
								   {{assign var=hour_prevue value=""}}
								 {{/if}}
						     

	               <div {{if $smarty.foreach.foreach_perf_line.last}}style="margin-bottom: 15px;"{{else}}style="margin-bottom: 5px;"{{/if}} 
	                    onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$_perfusion->_guid}}-{{$_date_hour}}');"
	                    ondblclick='addAdministrationPerf("{{$_perfusion->_id}}","{{$_date}}","{{$_hour}}","{{$hour_prevue}}",document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}");'
											class="administration tooltip-trigger
											       {{if $nb_prevue && ($nb_adm || $nb_adm == 0)}}
											         {{if $nb_prevue == $nb_adm}}administre
											         {{elseif $nb_adm == '0'}}administration_annulee
											         {{elseif $nb_adm}}administration_partielle
											         {{elseif $_date_hour < $now}}non_administre
											         {{else}}a_administrer{{/if}}
											       {{/if}}">
									
									 {{* Affichage des prises prevues et des administrations *}}
									 {{if $nb_adm}}{{$nb_adm}}{{elseif $nb_prevue}}0{{/if}}
									 {{if $nb_prevue}}/{{/if}}
									 {{if $nb_prevue}}{{$nb_prevue}}{{/if}}
									
									
						    </div>	
						    <div id="tooltip-content-{{$_perfusion->_guid}}-{{$_date_hour}}" style="display: none;">
						      Prises pr�vues � {{$hour_prevue|date_format:$dPconfig.time}}
						      <ul>
						      {{foreach from=$_perfusion->_ref_lines item=_perf_line}}
						        <li>{{$_perf_line->_ref_produit->libelle_abrege}} {{$_perf_line->_ref_produit->dosage}}: {{$_perf_line->_quantite_administration}} ml</li>
						      {{/foreach}}
						      </ul>
						    </div>
					    
					    {{/foreach}}
						     
						     
			    </td>
		    {{/foreach}}
     {{/foreach}}		   
   {{/foreach}}
 {{/foreach}}		
 <th></th>
 <td style="text-align: center">
   {{if $_perfusion->signature_prat}}
   <img src="images/icons/tick.png" alt="" title="Sign�e le {{$_perfusion->_ref_log_signature_prat->date|date_format:$dPconfig.datetime}} par {{$_perfusion->_ref_praticien->_view}}" />
   {{else}}
   <img src="images/icons/cross.png" alt="" title="Non sign�e par le praticien" />
   {{/if}}
 </td>
 <td style="text-align: center">
   {{if $_perfusion->signature_pharma}}
   <img src="images/icons/tick.png" alt="" title="Sign�e par le pharmacien" />
   {{else}}
   <img src="images/icons/cross.png" alt="" title="Non sign�e par le pharmacien" />
   {{/if}}
 </td>