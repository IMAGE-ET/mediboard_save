{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" id="prescription_line_mix-{{$_prescription_line_mix->_id}}">
<tbody class="hoverable {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}line_stopped{{/if}}">
{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
  <tr>
    <th colspan="8" id="th-perf-{{$_prescription_line_mix->_id}}" class="text element {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}arretee{{/if}}">
      
		{{if $_prescription_line_mix->_ref_parent_line->_id}}
      <div style="float: left">
        {{assign var=parent_perf value=$_prescription_line_mix->_ref_parent_line}}
        <img src="images/icons/history.gif" title="Ligne poss�dant un historique" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')"/>
      </div>
      {{/if}}
      
      <div class="mediuser" style="float: right; {{if !$_prescription_line_mix->_protocole}}border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}};{{/if}}">	  
        {{if !$_prescription_line_mix->_protocole}}
				<!-- Siganture du praticien -->
        {{if $_prescription_line_mix->_can_vw_signature_praticien}}
          {{$_prescription_line_mix->_ref_praticien->_view}}
					{{if $_prescription_line_mix->signature_prat}}
					  <img src="images/icons/tick.png" title="Ligne sign�e par le praticien" /> 
					{{else}}
					  <img src="images/icons/cross.png" title="Ligne non sign�e par le praticien" /> 
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
						{{if $_prescription_line_mix->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Sign�e par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non sign�e par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
        {{/if}}
				{{/if}} 
       <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '{{$_prescription_line_mix->_guid}}');"></button>
      </div>
        
      <strong>
				Perfusion :
				{{foreach from=$_prescription_line_mix->_ref_lines item=_line name=perf_line}}
				 {{$_line->_ref_produit->libelle_abrege}}{{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td>
    	
			     {{if $_prescription_line_mix->_can_delete_prescription_line_mix}}
					 <form name="editPerf-{{$_prescription_line_mix->_id}}" action="" method="post">
				        <input type="hidden" name="m" value="dPprescription" />
				        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
				        <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
				        <input type="hidden" name="del" value="1" />
								<button type="button" class="trash notext" onclick="return onSubmitFormAjax(this.form, { 
                  onComplete: function(){
                      Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
                  }        
                } );"></button>
							</form>
              {{/if}}
							
      <strong>{{mb_label object=$_prescription_line_mix field="type"}}</strong>:
      {{if $_prescription_line_mix->type}}
        {{mb_value object=$_prescription_line_mix field="type"}}
      {{else}}
        -
      {{/if}}
    </td>
    <td>
      {{if $_prescription_line_mix->vitesse}}
	      <strong>{{mb_label object=$_prescription_line_mix field="vitesse"}}</strong>:
	      {{mb_value object=$_prescription_line_mix field="vitesse"}} ml/h
      {{elseif $_prescription_line_mix->nb_tous_les}}
        <strong>Fr�quence</strong>: toutes les {{mb_value object=$_prescription_line_mix field="nb_tous_les"}} h
      {{/if}}
    </td>
    <td>
      <strong>{{mb_value object=$_prescription_line_mix field="voie"}}</strong>
    </td>
    <td>
      <strong>{{mb_label object=$_prescription_line_mix field="date_debut"}}</strong>:
      {{if $_prescription_line_mix->date_debut}}
        {{mb_value object=$_prescription_line_mix field=date_debut}}
      {{/if}}
      {{if $_prescription_line_mix->time_debut}}
	      � 
		    {{mb_value object=$_prescription_line_mix field=time_debut}}
	    {{/if}}
	  </td>
    <td>
		  <strong>{{mb_label object=$_prescription_line_mix field=duree}}</strong>:
			{{mb_value object=$_prescription_line_mix field=duree}}heures
	  </td>	    
  </tr>
  {{if $_prescription_line_mix->type == "PCA"}}
    <tr>
      <td>
				<strong>{{mb_label object=$_prescription_line_mix field=mode_bolus}}</strong>:
				{{mb_value object=$_prescription_line_mix field=mode_bolus}}
      </td>
      {{if $_prescription_line_mix->mode_bolus != "sans_bolus"}}
      <td>
				<strong>{{mb_label object=$_prescription_line_mix field=dose_bolus}}</strong>:
				{{mb_value object=$_prescription_line_mix field=dose_bolus}} mg
      </td>
      <td>
				<strong>{{mb_label object=$_prescription_line_mix field=periode_interdite}}</strong>:
				{{mb_value object=$_prescription_line_mix field=periode_interdite}} min
      </td>
      {{else}}
      <td colspan="2" />
      {{/if}}
      <td />
      <td />
    </tr>
  {{/if}}
  <tr>
    <td colspan="8">
      <table class="form">
	      {{foreach from=$_prescription_line_mix->_ref_lines item=line}}
	        <tr>
	          <td style="border: none; width:30%" class="text">
	            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	            {{if $line->_can_vw_livret_therapeutique}}
					      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non pr�sent dans le livret Th�rapeutique" />
					    {{/if}}
						  {{if $line->stupefiant}}
				        <img src="images/icons/stup.png" title="Produit stup�fiant" />
				      {{/if}}
					    {{if !$line->_ref_produit->inT2A}}
				        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
				      {{/if}}
					    {{if $line->_can_vw_generique}}
					      <img src="images/icons/generiques.gif" title="Produit g�n�rique" />
					    {{/if}}
              {{if $line->_ref_produit->_supprime}}
                <img src="images/icons/medicament_barre.gif" title="Produit supprim�" />
              {{/if}}
		          <strong>{{$line->_ucd_view}}</strong>
		          <span style="font-size:0.8em; opacity:0.7">
		            {{$line->_forme_galenique}}
		          </span>
	          </td>
	          <td style="border: none; width:20%">
	            <strong>{{mb_label object=$line field=quantite}}</strong>:
	            {{mb_value object=$line field=quantite size=4}}
	            {{mb_value object=$line field=unite size=4}}
	          </td>
	        </tr>
	      {{foreachelse}}
	      	<div class="small-info">
		        Aucun produit n'est associ� � la perfusion
		      </div>
	      {{/foreach}}
      </table>
    </td>
  </tr>
</tbody>
</table>