{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$curr_line}}
<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}
                  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}" 
      {{if !$mode_induction_perop}}id="line_medicament_{{$line->_id}}"{{/if}}>
  <!-- Header de la ligne -->
  <tr  class="hoverable">
    <td style="text-align: center; width: 5%;" class="text">
      <!-- Suppression de la ligne -->
      {{if $line->_can_delete_line}}
        <button type="button" class="trash notext" onclick="Prescription.delLine({{$line->_id}})" style="float: left">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
		  {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non pr�sent dans le livret Th�rapeutique" />
      {{/if}}  
		  {{if $line->stupefiant}}
        <img src="images/icons/stup.png" title="Produit stup�fiant" />
      {{/if}}
      {{if !$line->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
      {{/if}}
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" title="Produit Hospitalier" />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" title="Produit g�n�rique" />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <img src="images/icons/medicament_barre.gif" title="Produit supprim�" />
      {{/if}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
    </td>
    <td style="width: 25%" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="text {{if $line->traitement_personnel}}traitement{{/if}}
				            {{if $line->perop}}perop{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole && !$mode_induction_perop}}
         Main.add( function(){
             moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      {{if $line->_ref_parent_line->_id}}
        {{assign var=parent_line value=$line->_ref_parent_line}}
        <img style="float: right" src="images/icons/history.gif" title="Ligne poss�dant un historique" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_line->_guid}}')"/>
      {{/if}}
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');" style="font-weight: bold;">
        {{$line->_ucd_view}}
        <br /> 
        <span style="font-size: 0.8em; opacity: 0.7">
         {{$line->_forme_galenique}}
        </span>
      </a>
      {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
			{{if $line->perop}}{{mb_label object=$line field="perop"}}&nbsp;{{/if}}
      {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
      {{if $line->traitement_personnel}}Traitement personnel&nbsp;{{/if}}
    </td>
    <td style="width: 37%;" class="text">
	    {{if $line->_ref_prises|@count}}
	      {{foreach from=$line->_ref_prises item=_prise name=prises}}
	        {{$_prise->_view}} 
					{{if !$smarty.foreach.prises.last}}, {{/if}}
	      {{/foreach}}
	    {{else}}
	      Aucune posologie
	    {{/if}}
    </td>
    <td class="text" style="width: 8%" >
    	{{if !$line->_protocole}}
        <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
					{{if @$modules.messagerie}}
					<a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$line->_view}}')">
					  <img src="images/icons/mbmail.png" title="Envoyer un message" />
					</a>
					{{/if}}
					{{if $line->signee}}
					  <img src="images/icons/tick.png" title="Ligne sign�e par le praticien" />
					{{else}}
					  <img src="images/icons/cross.png" title="Ligne non sign�e par le praticien" />
					{{/if}}
          {{if $prescription_reelle->type == "sejour"}}
	          {{if $line->valide_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Sign�e par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non sign�e par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
			   <label title="{{$line->_ref_praticien->_view}}">{{$line->_ref_praticien->_shortview}}</label>
       </div>
			 {{else}}
			 -
			 {{/if}}
    </td>
		{{if !$line->_protocole}}
	    {{if $line->fin}}
			  <td colspan="2" style="width: 25%" class="text">
			     {{if !$mode_induction_perop}}   
            <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$line->_guid}}');"></button>
          {{/if}}
					  A partir de la fin du s�jour jusqu'au {{mb_value object=$line field=fin}}
           
				</td>
			{{else}}
				<td style="width: 15%" class="text">
	        <!-- Date de debut -->
	        {{if $line->debut}}
	          {{mb_value object=$line field=debut}}
	          {{if $line->time_debut}}
	            � {{mb_value object=$line field=time_debut}}
	          {{/if}}
	        {{/if}}
	      </td>
	      <td style="width: 10%" class="text">
	        {{if !$mode_induction_perop}}   
	          <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$line->_guid}}');"></button>
	        {{/if}}
	        <!-- Duree de la ligne -->
	        {{if $line->duree && $line->unite_duree}}
	          {{mb_value object=$line field=duree}}  
	          {{mb_value object=$line field=unite_duree}}
	        {{/if}}
	      </td>
			{{/if}}
		{{else}}
		 <td style="width: 25%" class="text">
		 	 {{if !$mode_induction_perop}}   
          <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$line->_guid}}');"></button>
       {{/if}}
		 
			 <!-- Duree de la prise --> 
	     {{if $line->duree && $line->duree != "1"}}
	      Dur�e de {{mb_value object=$line field=duree}} jour(s) 
	     {{/if}}
	    
	     <!-- Date de debut de la ligne -->
	    {{if $line->jour_decalage && $line->unite_decalage}} 
	      {{if $line->duree > 1 || $line->jour_decalage_fin}} � partir de{{else}} � {{/if}}
	      {{if $prescription->object_class == "CSejour"}} {{$line->jour_decalage}} {{else}} J {{/if}}
	      {{if ($line->unite_decalage == "jour" && $line->decalage_line != 0) || ($line->unite_decalage == "heure")}}
	        {{if $line->decalage_line >= 0}}+{{/if}} 
	        {{mb_value object=$line field=decalage_line size="3"}}
	        {{if $prescription->object_class == "CSejour"}} 
	          {{mb_value object=$line field=unite_decalage}}
	        {{else}}
	          (jours)
	        {{/if}} 
	      {{/if}}
	      {{if $line->time_debut}}
	        � {{mb_value object=$line field=time_debut}}
	      {{/if}}
	     {{/if}}
	     <!-- Date de fin -->
	     {{if $line->jour_decalage_fin && $line->unite_decalage_fin}}
	       jusqu'� {{$line->jour_decalage_fin}}
	       {{if ($line->unite_decalage_fin == "jour" && $line->decalage_line_fin != 0) || ($line->unite_decalage_fin == "heure")}}
	         {{if $line->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line_fin increment=1 }}
	         {{mb_value object=$line field=unite_decalage_fin }}
	       {{/if}}
	       {{if $line->time_fin}} 
	        � {{mb_value showPlus=1 object=$line field=time_fin}}    
	       {{/if}}  
	     {{elseif $line->jour_decalage && !$line->duree && $prescription->type == "sejour"}}
	        jusqu'� la fin du s�jour.
	     {{/if}}
		 </td>
		{{/if}}
  </tr>
</table>