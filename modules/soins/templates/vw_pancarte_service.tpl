{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPpatients" script="patient"}}
{{mb_script module="dPprescription" script="plan_soins"}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPprescription" script="prescription"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}

<style type="text/css">

.modal div{
  text-align: left;
}

</style>

<script type="text/javascript">

function viewDossierSoin(sejour_id){
  var oForm = document.viewSoin;
  oForm.sejour_id.value = sejour_id;
  oForm.submit();
}
       
function viewLegendPancarte(){
  var url = new Url("soins", "vw_legende_pancarte");
  url.popup(300, 500, "L�gende de la pancarte");
}
       
function viewTransmissions(service_id, user_id, degre, observations, transmissions, refresh, order_col, order_way){
  var url = new Url("soins", "httpreq_vw_transmissions_pancarte");
  url.addParam("service_id", service_id);
  url.addParam("user_id", user_id);
  url.addParam("degre", degre);
  url.addParam("date", "{{$date}}");
  url.addParam("date_min", "{{$date_min}}");
  url.addParam("observations", observations?1:0);
  url.addParam("transmissions", transmissions?1:0);
  url.addParam("refresh", refresh);
  if(order_col && order_way){
	  url.addParam("order_col", order_col);
	  url.addParam("order_way", order_way);
  }
  if(user_id || degre || refresh){
    url.requestUpdate("_transmissions");
  } else {
    url.requestUpdate("viewTransmissions");
  }
}       

showDossierSoins = function(sejour_id, date){
  $('dossier_sejour').update("");
  var url = new Url("soins", "ajax_vw_dossier_sejour");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate($('dossier_sejour'));
	modalWindow = modal($('dossier_sejour'));
}


refreshLinePancarte = function(prescription_id){
  var url = new Url("soins", "vw_pancarte_service");
	url.addParam("prescription_id", prescription_id);
  url.requestUpdate("pancarte_line_"+prescription_id);
}

loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans) {
  if(!sejour_id) return;
  updateNbTrans(sejour_id);
  var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
  urlSuivi.addParam("sejour_id", sejour_id);
  urlSuivi.addParam("user_id", user_id);
  urlSuivi.addParam("cible", cible);
  if (!Object.isUndefined(show_obs)) {
    urlSuivi.addParam("_show_obs", show_obs);
  }
  if (!Object.isUndefined(show_trans)) {
    urlSuivi.addParam("_show_trans", show_trans);
  }
  urlSuivi.requestUpdate("dossier_suivi");
}

Main.add(function () {
  var tab_sejour = Control.Tabs.create('tab-pancarte', false);
  viewTransmissions($V(document.selService.service_id), null, null, '1', '1');
});

</script>

{{mb_script module="dPprescription" script="prescription"}}

<form name="viewSoin" method="get" action="?">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="tab" value="vw_idx_sejour" />
  <input type="hidden" name="sejour_id" value="" />
  <input type="hidden" name="date" value="{{$date}}" />
  <input type="hidden" name="mode" value="1" />
  <input type="hidden" name="_active_tab" value="dossier_soins" /> 
</form>
			
<ul id="tab-pancarte" class="control_tabs">
  <li><a href="#viewPancarte">Pancarte {{$service->_view}}</a></li>
  <li><a href="#viewTransmissions">Transmissions</a></li>
	<li style="margin-top: 2px; padding-left: 2em; vertical-align: top;">
	  <form name="selService" action="?" method="get">
	    <input type="hidden" name="m" value="{{$m}}" />
	    <input type="hidden" name="tab" value="vw_pancarte_service" />
	    <select name="service_id" onchange="this.form.submit();">
	      <option value="">&mdash; Choix d'un service</option>
	      {{foreach from=$services item=_service}}
	        <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
	      {{/foreach}}
	    </select>
	    le
	    {{mb_field object=$filter_line field="debut" register=true form=selService onchange="this.form.submit();"}}
	  </form>
	</li>
	<li style="float: right;">
		<button type="button" class="search" onclick="viewLegendPancarte();">L�gende</button>
	</li>
</ul>
<hr class="control_tabs" />

{{assign var=images value="CPrescription"|static:"images"}}

<div id="viewPancarte" style="display: none;">
	<table class="form">
	  <tr>
	    <th class="title">
	      Pancarte du service {{$service->_view}}
	    </th>
	  </tr>
	</table>
	<table class="tbl">
	  <tr>
	    <th rowspan="2" class="title narrow">Patient</th>
	    <th rowspan="2" class="title narrow">Lit</th>
	    <th rowspan="2" class="title narrow">Prat.</th>
	     {{foreach from=$count_composition_dossier key=_date item=_hours_by_moment}}
         {{foreach from=$_hours_by_moment key=moment_journee item=_count}}
				
					  {{if $composition_dossier|@count == 1}}
	            {{assign var=view_poste value="Journ�e"}}
	          {{else}}
						  {{assign var=tab_poste value='-'|explode:$moment_journee}}
		          {{assign var=num_poste value=$tab_poste|@end}}
		          {{assign var=libelle_poste value="Libelle poste $num_poste"}}
		          {{assign var=view_poste value=$configs.$libelle_poste}}
						{{/if}}	
					
	          <th class="{{$_date}}-{{$moment_journee}} title" colspan="{{$_count}}">
		            <strong>{{$view_poste}} du {{$_date|date_format:"%d/%m"}}</strong>
						</th>
			    {{/foreach}} 
		    {{/foreach}}
	  </tr>
		<tr>
			{{foreach from=$tabHours key=_date item=_hours_by_moment}}
	      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
	         {{foreach from=$_dates key=_date_reelle item=_hours}}
	           {{foreach from=$_hours key=_heure_reelle item=_hour}}
	             <th style="font-size: 0.8em;">{{$_hour}}h</th>   
		        {{/foreach}}
		      {{/foreach}}
		    {{/foreach}} 
	    {{/foreach}}	
		</tr>
		{{foreach from=$prescriptions item=_prescription}}
		  {{assign var=_prescription_id value=$_prescription->_id}}    
		  <tr id="pancarte_line_{{$_prescription_id}}">
		 	  {{mb_include module=soins template=inc_vw_line_pancarte_service}}
		 	</tr>
		{{foreachelse}}
		  <tr>
		  	<td colspan="30" class="empty">
		  		Aucune prise
		  	</td>
		  </tr>
		{{/foreach}}
	</table>
	</div>
	<div id="viewTransmissions" style="display: none;">
	</div>
</div>

<div id="dossier_sejour" style="width: 80%; height: 600px; overflow: auto; display: none;"></div>	