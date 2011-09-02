{{* $Id: vw_placement_patients.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{mb_script module=dPhospi script=drag_patient}}


<style type="text/css">
	
div.patient{
	background-color: #EEE;
}

div#list-patients-non-placees{
 	min-width:100px;
	min-height:720px;
	clear:both;float:left;
 }
 
div#list-patients-non-placees p{
  font-size:medium;
	min-width:100px;
	background-color: #ABE;
	text-align:center;
}

div#grille{
 	float:left;
	margin-left:5px;
	margin-right:5px;
 }

div#grille table{
  border-spacing: 9px;
	border-collapse:separate;
}

div#grille td.chambre{
	vertical-align: top;
	white-space :normal;
	width:90px;
	height:60px;
	background-color: #ABE;
}

div#grille small{
  float: right;
	margin-top: -11px;
	background: #ABE;
  border-radius: 2px;
	padding: 0 3px;
}

div#grille td.pas-de-chambre{
  vertical-align: top;
	white-space :normal;
	width:90px;
	height:60px;
	background-color: white;
	border-color:white;	
}

</style>
	
<table class="main">
	<tr>
	  <th class="title" style="width:200px;">Patiens  </th>
	  <th class="title">{{if $service_id!=""}}{{$service_selectionne->nom}}{{else}}Plan{{/if}}</th>
	</tr>
	<tr>
		<td>
			<div id="list-patients-non-placees">
				<p>Non plac�s</p>						
			  {{foreach from=$chambre_non_affectees item=_affectation}}
				  {{assign var=_sejour value=$_affectation}}
				  {{assign var=_patient   value=$_sejour->_ref_patient}}
						<div data-sejour-id="{{$_sejour->sejour_id}}"
						  data-entree="{{$_sejour->entree_prevue}}"
						  data-sortie="{{$_sejour->sortie_prevue}}"
							onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')" class="patient" id="{{$_sejour->_id}}">
				       	 {{$_patient}}
						</div>
			  {{/foreach}}
			</div>
		</td>
		<td> 
			<div id="grille">
				<form action="?m=dPhospi&tab=vw_placement_patients" method="post">
					<select name="service_id" onchange="this.form.submit()">
						<option value="">&mdash; Service</option>
						{{foreach from=$les_services item=_service}}		
							<option value="{{$_service->_id}}" {{if $service_id!="" && $service_id==$_service->_id}}selected="selected"{{/if}}>{{ $_service->nom}}</option>
						{{/foreach}}
					</select>
				</form>
				
				<table class="main tbl">
				{{foreach from=$grille item=ligne }}
				<tr>
					{{foreach from=$ligne item=_zone }}
          {{if $_zone!="0"}}
					
					 <td data-chambre-id="{{$_zone->chambre_id}}" data-lit-id="{{foreach from=$_zone->_ref_lits item=i name=foo}}{{if $smarty.foreach.foo.first}}{{$i->_id}} {{/if}}{{/foreach}}" data-nb-lits="{{$_zone->_ref_lits|@count}}" 
					   class="chambre">
						<small>{{$_zone}}</small>						  
						{{foreach from=$chambres_affectees item=_affectation}}
						  {{if $_affectation->_ref_lit->_ref_chambre->nom==$_zone}}
						    {{assign var=_sejour value=$_affectation->_ref_sejour}}
						    {{assign var=_patient   value=$_sejour->_ref_patient}}
						    <div onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}');" class="patient" id="{{$_sejour->_id}}">{{$_patient}}  </div>
						  {{/if}}
						{{/foreach}}	
					</td> 
					{{else}}
					<td class="pas-de-chambre"></td>
					{{/if}}
					{{/foreach}}
				</tr>
				{{/foreach}}
				</table>
			</div>
		</td>
	</tr>
</table>