{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="show_statut" value=$conf.dPurgences.show_statut}}

<script type="text/javascript">
  Main.add(function() {
    Veille.refresh();
    Missing.refresh();
    
    {{if $type == "MainCourante"}}
      $$("a[href=#holder_main_courante] small")[0].update("({{$listSejours|@count}})");
    {{else if $type == "UHCD"}}
      var tab = $$("a[href=#holder_uhcd]")[0];
      tab.down("small").update("({{$listSejours|@count}})");
      {{if $listSejours|@count == '0'}}
        tab.addClassName('empty');
      {{else}}
        tab.removeClassName('empty');
      {{/if}}
    {{/if}}
    
	  {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });

  fillRetour = function(rpu_id, type) {
    var oForm = getForm("editRPU-"+rpu_id);
    $V(oForm[type], "current");
    oForm.onsubmit();
  }

  fillDiag = function(rpu_id) {
    {{if $type == "MainCourante"}}
      MainCourante.stop();
    {{else if $type == "UHCD"}}
      UHCD.stop();
    {{/if}}
    var url = new Url("dPurgences", "ajax_edit_diag");
    url.addParam("rpu_id", rpu_id);
    url.requestModal(500, 200);
    url.modalObject.observe("afterClose", function(){
      {{if $type == "MainCourante"}}
        MainCourante.start();
      {{else if $type == "UHCD"}}
        UHCD.start();
      {{/if}}
    });
  }
</script>

<div class="small-info" style="display: none;" id="filter-indicator">
  <strong>R�sultats filtr�s</strong>.
  <br />
  Les r�sultats sont filtr�s et le rafra�chissement est d�sactiv�. 
  {{if $type == "MainCourante"}}
    <button class="change" onclick="MainCourante.start()">Relancer</button>
  {{else if $type == "UHCD"}}
    <button class="change" onclick="UHCD.start()">Relancer</button>
  {{/if}}
</div>

<table class="tbl">
  <tr>
    <th style="width: 8em;">
		  {{mb_colonne class=CRPU field="ccmu"        order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
    <th style="width: 16em;">
    	{{mb_colonne class=CRPU field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
		
		<th class="narrow">
  		{{if $type == "MainCourante"}}
        <input type="text" size="6" onkeyup="MainCourante.filter(this, 'filter-indicator')" id="filter-patient-name-{{$type}}" />
      {{else if $type == "UHCD"}}
        <input type="text" size="6" onkeyup="UHCD.filter(this, 'filter-indicator')" id="filter-patient-name-{{$type}}" />
      {{/if}}
		</th>
		
    <th style="width: 10em;">
		  {{mb_colonne class=CRPU field="_entree"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
    {{if $conf.dPurgences.responsable_rpu_view}}
    <th class="narrow">{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th style="width: 10em;">{{mb_title class=CRPU field=_attente}} / {{mb_title class=CRPU field=_presence}}</th>
    {{if $medicalView}}
			<th style="width: 16em;">
			{{if $conf.dPurgences.diag_prat_view}}
	      {{tr}}CRPU-diag_infirmier-court{{/tr}} / {{tr}}Medical{{/tr}}
			{{else}}
			  {{tr}}CRPU-diag_infirmier-court{{/tr}}
			{{/if}}
		  </th>
    {{/if}}
    <th style="width: 0em;">{{tr}}CRPU.pec{{/tr}}</th>
  </tr>

  {{foreach from=$listSejours item=_sejour key=sejour_id}}
    {{assign var=rpu value=$_sejour->_ref_rpu}}
    {{assign var=rpu_id value=$rpu->_id}}
    {{assign var=patient value=$_sejour->_ref_patient}}
    {{assign var=consult value=$rpu->_ref_consult}}
  
    {{assign var=background value=none}}
    {{if $consult && $consult->_id}}{{assign var=background value="#ccf"}}{{/if}}
    
    {{* Param to create/edit a RPU *}}
    {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
    {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
    
    <tr class="
  	 {{if !$_sejour->sortie_reelle && $_sejour->_veille}}veille{{/if}}
     {{if !$rpu_id}}missing{{/if}}
    ">
    	{{if $_sejour->annule}}
      <td class="cancelled">
        {{tr}}Cancelled{{/tr}}
      </td>
  	  {{else}}
  
      <td class="ccmu-{{$rpu->ccmu}} text" 
        {{if $_sejour->sortie_reelle || ($rpu->mutation_sejour_id && $conf.dPurgences.create_sejour_hospit)}}
          style="border-right: 5px solid black"
        {{/if}}>
        <a href="{{$rpu_link}}">
          {{if $rpu->ccmu}}
  				  {{mb_value object=$rpu field=ccmu}}
          {{/if}}
        </a>
        {{if $rpu->box_id}}
          {{assign var=rpu_box_id value=$rpu->box_id}}
          {{if array_key_exists($rpu_box_id, $boxes)}}
            <strong>{{$boxes.$rpu_box_id->_view}}</strong>
          {{/if}}
        {{/if}}
      </td>
      {{/if}}
  
    	{{if $_sejour->annule}}
    	<td colspan="2" class="text cancelled">
  	  {{else}}
      <td colspan="2" class="text" style="background-color: {{$background}};">
      {{/if}}
        <button type="button" class="search notext" title="Synth�se" onclick="showSynthese('{{$_sejour->_id}}');" style="float: right">Synth�se</button>
        {{mb_include template=inc_rpu_patient}}
      </td>
  
    	{{if $_sejour->annule}}
      <td class="cancelled" colspan="{{if $conf.dPurgences.responsable_rpu_view}}4{{else}}3{{/if}}">
        {{tr}}Cancelled{{/tr}}
      </td>
  		<td class="cancelled">
  		  {{if $rpu->_ref_consult->_id}}
          {{mb_include template="inc_pec_praticien"}}
  			{{/if}}
      </td>
  
  	  {{else}}
  
      <td class="text" style="background-color: {{$background}}; text-align: center;">
  			{{mb_include module=system template=inc_object_notes object=$_sejour mode=view float=right}}
        
  			{{if $isImedsInstalled}}
  			  {{mb_include module=Imeds template=inc_sejour_labo sejour=$_sejour link="$rpu_link#Imeds"}}
        {{/if}}
  
        <a href="{{$rpu_link}}">
        	<span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
            {{mb_value object=$_sejour field=_entree date=$date}}
           {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
          </span>
        </a>
  								
        {{if $show_statut == 1}}
          <div style="clear: both; font-weight: bold; padding-top: 3px;">
            {{if $type == "MainCourante"}}
              <form name="editRPU-{{$rpu->_id}}"
                onsubmit="return onSubmitFormAjax(this, {onComplete: function() { MainCourante.start() }});" method="post" action="?">              
            {{else if $type == "UHCD"}}
              <form name="editRPU-{{$rpu->_id}}"
                onsubmit="return onSubmitFormAjax(this, {onComplete: function() { UHCD.start() }});" method="post" action="?">
            {{/if}}
      
              <input type="hidden" name="m" value="dPurgences" />
              <input type="hidden" name="dosql" value="do_rpu_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="radio_fin" value="{{$rpu->radio_fin}}" />
              <input type="hidden" name="bio_retour" value="{{$rpu->bio_retour}}" />
              <input type="hidden" name="specia_arr" value="{{$rpu->specia_arr}}" />
              {{mb_key object=$rpu}}
          
              {{if $rpu->radio_debut}}
                {{if !$rpu->radio_fin}} 
                  <a onclick="fillRetour('{{$rpu->_id}}', 'radio_fin')" href="#1" style="display: inline;">
                {{/if}}
                <img src="modules/soins/images/radio{{if !$rpu->radio_fin}}_grey{{/if}}.png"
                  {{if !$rpu->radio_fin}}
                    title="{{tr}}CRPU-radio_debut{{/tr}} � {{$rpu->radio_debut|date_format:$conf.time}}"
                  {{else}}
                    title="{{tr}}CRPU-radio_fin{{/tr}} � {{$rpu->radio_fin|date_format:$conf.time}}"
                  {{/if}}/>
                {{if !$rpu->radio_fin}}
                  </a>
                {{/if}}
              {{elseif !$rpu->radio_fin}}
               <img src="images/icons/placeholder.png"/>
              {{/if}}
             
              {{if $rpu->bio_depart}}
                {{if !$rpu->bio_retour}}
                  <a onclick="fillRetour('{{$rpu->_id}}', 'bio_retour')" href="#1" style="display: inline;">
                {{/if}}
                <img src="images/icons/labo{{if !$rpu->bio_retour}}_grey{{/if}}.png"
                  {{if !$rpu->bio_retour}}
                    title="{{tr}}CRPU-bio_depart{{/tr}} � {{$rpu->bio_depart|date_format:$conf.time}}"
                  {{else}}
                    title="{{tr}}CRPU-bio_retour{{/tr}} � {{$rpu->bio_retour|date_format:$conf.time}}"
                  {{/if}}/>
                {{if !$rpu->bio_retour}}
                   </a>
                {{/if}}
              {{elseif !$rpu->bio_retour}}
                <img src="images/icons/placeholder.png"/>
              {{/if}}
  
              {{if $rpu->specia_att}}
                {{if !$rpu->specia_arr}}
                  <a onclick="fillRetour('{{$rpu->_id}}', 'specia_arr')" href="#1" style="display: inline;">
                {{/if}}
                <img src="modules/soins/images/stethoscope{{if !$rpu->specia_arr}}_grey{{/if}}.png"
                  {{if !$rpu->specia_arr}}
                    title="{{tr}}CRPU-specia_att{{/tr}} � {{$rpu->specia_att|date_format:$conf.time}}"
                  {{else}}
                    title="{{tr}}CRPU-specia_arr{{/tr}} � {{$rpu->specia_arr|date_format:$conf.time}}"
                  {{/if}}/>
                {{if !$rpu->specia_arr}}
                  </a>
                {{/if}}
              {{elseif !$rpu->specia_arr}}
                <img src="images/icons/placeholder.png"/>
              {{/if}}
  
              {{if $_sejour->_nb_files_docs > 0}}
                <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$rpu->_id}}#doc-items" style="display: inline">
                  <img src="images/icons/docitem.png"
                    title="{{$_sejour->_nb_files|default:0}} {{tr}}CMbObject-back-files{{/tr}} / {{$_sejour->_nb_docs|default:0}} {{tr}}CMbObject-back-documents{{/tr}}"/></a>
              {{else}}
                <img src="images/icons/placeholder.png"/>
              {{/if}}
  
              {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
              {{if $prescription->_id}}
                <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$rpu->_id}}#suivisoins" style="display: inline;">
                  {{if $prescription->_count_recent_modif_presc}}
                    <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
                  {{else}}
    	              <img src="images/icons/ampoule_grey.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
    	            {{/if}}
                </a>
  	    	    {{else}}
                <img src="images/icons/placeholder.png"/>
  			      {{/if}}
  			      
  			      {{if $_sejour->UHCD}}
  			        <img src="images/icons/uhcd.png"/>
  			      {{/if}}
            </form>
          </div>
        {{/if}}
      </td>
      
      {{if $conf.dPurgences.responsable_rpu_view}}
      <td class="text" style="background-color: {{$background}};">
        <a href="{{$rpu_link}}">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        </a>
      </td>
      {{/if}}
  
      {{if $rpu->_id}}
        {{if $rpu->mutation_sejour_id}}
  			  {{mb_include template=inc_dossier_mutation colspan=1}}
        {{else}} 
    		  <td style="background-color: {{$background}}; text-align: center">
    		    {{if $consult && $consult->_id}}
      		    {{if !$_sejour->sortie_reelle && $show_statut}}
                {{mb_include template=inc_icone_attente}}
              {{/if}}
    			    <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
    			      Consult. {{$consult->heure|date_format:$conf.time}}
    			      {{if $date != $consult->_ref_plageconsult->date}}
    			      <br/>le {{$consult->_ref_plageconsult->date|date_format:$conf.date}}
    			      {{/if}}
    			    </a>
    			    {{if !$_sejour->sortie_reelle}}
    			      ({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})
    			    {{elseif $_sejour->sortie_reelle}}
                {{if $_sejour->mode_sortie != "normal"}}
                  ({{mb_value object=$_sejour field=mode_sortie}}
                {{else}}
                  (sortie
                {{/if}}
                � {{$_sejour->sortie_reelle|date_format:$conf.time}})
    			    {{/if}}
    		    {{else}}
    		      {{mb_include template="inc_attente" sejour=$_sejour}}
    	      {{/if}}
    	    </td>
        {{/if}} 
      
  	    {{if $medicalView}}
    	    <td class="text" style="background-color: {{$background}};">
            {{if $admin_urgences}}
              <button class="edit notext" style="float: right;" title="{{tr}}CRPU-modif_diag_infirmier{{/tr}}" onclick="fillDiag('{{$rpu->_id}}')"></button>
            {{/if}}
  				  {{if $rpu->date_at}} 
  					<img src="images/icons/accident_travail.png" />
  				  {{/if}}
    				{{if $rpu->motif && $conf.dPurgences.diag_prat_view}}
    				  <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
    				  	<strong>{{mb_title class=$rpu field=motif}}</strong> : {{$rpu->motif|nl2br}}
    				  </span>
    	      {{else}}
    	       {{if $rpu->diag_infirmier}}
    				  <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
                {{$rpu->diag_infirmier|nl2br}}
              </span>
              {{else}}
                {{if $rpu->motif_entree}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');" class="compact">
                    {{mb_label object=$rpu field="motif_entree"}} : {{$rpu->motif_entree|nl2br}}
                  </span> 
                {{/if}} 
              {{/if}}
    	      {{/if}}
    	    </td>
  	    {{/if}}
  	
  	    <td class="button {{if $_sejour->type != "urg"}}arretee{{/if}}" style="background-color: {{$background}};">
  			  {{mb_include template="inc_pec_praticien"}}
  	    </td>
  
  		{{else}}
  			<!-- Pas de RPU pour ce s�jour d'urgence -->
  			<td colspan="{{$medicalView|ternary:3:2}}">
  			  <div class="small-warning">
  			  	{{tr}}CRPU.no_assoc{{/tr}}
  			  	<br />
  			  	{{tr}}CRPU.no_assoc_clic{{/tr}}
  			  	<a class="button action new" href="{{$rpu_link}}">{{tr}}CRPU-title-create{{/tr}}</a>
  			  </div>
  			</td>
  		{{/if}}
      {{/if}}
    </tr>
  {{foreachelse}}
    <tr><td colspan="10" class="empty">{{tr}}CSejour.none_main_courante{{/tr}}</td></tr>
  {{/foreach}}
</table>
