{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
  url.popup(300, 400, "L�gende de la pancarte");
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

showPlanSoins = function(sejour_id, date){
  $('dossier_traitement').update("");
  Prescription.loadTraitement(sejour_id, date);
	modalWindow = modal($('dossier_traitement'));
}


refreshLinePancarte = function(prescription_id){
  var url = new Url("soins", "vw_pancarte_service");
	url.addParam("prescription_id", prescription_id);
  url.requestUpdate("pancarte_line_"+prescription_id);
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

<button type="button" class="search" onclick="viewLegendPancarte();" style="float: right;">L�gende</button>
<div style="text-align: center">
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
</div>
			
<ul id="tab-pancarte" class="control_tabs">
  <li><a href="#viewPancarte">Pancarte {{$service->_view}}</a></li>
  <li><a href="#viewTransmissions">Transmissions</a></li>
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
	    <th rowspan="2" class="title narrow">Praticien</th>
	      {{foreach from=$tabHours key=_date item=_hours_by_moment}}
	        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
	          <th class="{{$_date}}-{{$moment_journee}} title"
	              colspan="{{if $moment_journee == 'soir'}}{{$count_soir}}{{/if}}{{if $moment_journee == 'nuit'}}{{$count_nuit}}{{/if}}{{if $moment_journee == 'matin'}}{{$count_matin}}{{/if}}">
		            <strong>{{$moment_journee}} du {{$_date|date_format:"%d/%m"}}</strong>
			    {{/foreach}} 
		    {{/foreach}}
	  </tr>
		<tr>
			{{foreach from=$tabHours key=_date item=_hours_by_moment}}
	      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
	         {{foreach from=$_dates key=_date_reelle item=_hours}}
	           {{foreach from=$_hours key=_heure_reelle item=_hour}}
	             <th>{{$_hour}}h</th>   
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
		  	<td colspan="30">
		  		Aucune prise
		  	</td>
		  </tr>
		{{/foreach}}
	</table>
	</div>
	<div id="viewTransmissions" style="display: none;">
	</div>
</div>

<div id="dossier_traitement" style="width: 80%; height: 600px; overflow: auto;"></div>	