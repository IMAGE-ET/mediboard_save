{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=sejours_ssr}}

<a class="button new" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
  Admettre un patient
</a>

<div style="float: right;">
   Affichage
   <form name="Filter" action="?" method="get">
      <input name="m" value="{{$m}}" type="hidden">
      <input name="tab" value="{{$tab}}" type="hidden">
      <select name="show" onchange="this.form.submit();">
        <option value="all"     {{if $show == "all"    }} selected="selected"{{/if}}>Tous les s�jours</option>
        <option value="nopresc" {{if $show == "nopresc"}} selected="selected"{{/if}}>S�jours sans prescription</option>
      </select>
    </form>
  </td>
  
<table id="sejours-ssr" class="tbl">
	<tr>
		<th class="title" colspan="10">
			S�jours SSR du {{$date|date_format:$dPconfig.longdate}}
	    <form name="selDate" action="?" method="get">
	      <input type="hidden" name="m" value="{{$m}}" />
				<script type="text/javascript">
				Main.add(function () {
				  Calendar.regField(getForm("selDate").date, null, { noView: true } );
				});
				</script>
				

	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	    </form>
	  </th>
	</tr>
	<tr>
    <th style="width: 20em;">{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sejours_ssr"}}</th>
    <th style="width:   1%;">
      <input type="text" size="6" onkeyup="SejoursSSR.filter(this)" id="filter-patient-name" />
    </th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="entree"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sejours_ssr"}}</th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="sortie"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sejours_ssr"}}</th>

		<th style="width:  5em;">{{mb_title class="CSejour" field="_num_dossier"}}</th>
    <th style="width: 20em;">{{mb_title class="CSejour" field="libelle"}}</th>
    <th style="width: 16em;">
		  {{mb_title class="CBilanSSR" field="_kine_referent_id"}} /
      {{mb_title class="CBilanSSR" field="_kine_journee_id"}}
		</th>
    
    <th style="width:   1%;" colspan="2"><label title="Ev�nements planifi�s pour ce patient (ce jour - pendant tout le s�jour)">Evt.</label></th>
	</tr>
	
	{{foreach from=$sejours key=sejour_id item=_sejour}}
	{{assign var=ssr_class value=""}}
	{{if $_sejour->annule == "1"}}
	{{assign var=ssr_class value=ssr-annule}}
	{{elseif !$_sejour->entree_reelle}}
	{{assign var=ssr_class value=ssr-prevu}}
	{{elseif $_sejour->sortie_reelle}}
	{{assign var=ssr_class value=ssr-termine}}
	{{/if}}

	<tr class="{{$ssr_class}}">
		<td colspan="2" class="text">
			{{assign var=link value="?m=$m&tab=vw_aed_sejour_ssr&sejour_id=$sejour_id"}}
			{{mb_include template=inc_view_patient patient=$_sejour->_ref_patient}}
		</td>

	  {{assign var=distance_class value=ssr-far}}
	  {{if $_sejour->_entree_relative == "-1"}}
	  {{assign var=distance_class value=ssr-close}}
	  {{elseif $_sejour->_entree_relative == "0"}}
	  {{assign var=distance_class value=ssr-today}}
	  {{/if}}
    <td class="{{$distance_class}}">
    	{{mb_value object=$_sejour field=entree format=$dPconfig.date}}
      <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
		</td>

    {{assign var=distance_class value=ssr-far}}
    {{if $_sejour->_sortie_relative == "1"}}
    {{assign var=distance_class value=ssr-close}}
    {{elseif $_sejour->_sortie_relative == "0"}}
    {{assign var=distance_class value=ssr-today}}
    {{/if}}
    <td class="{{$distance_class}}">
    	{{mb_value object=$_sejour field=sortie format=$dPconfig.date}}
      <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
		</td>
		
		<td>
      <a>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
         {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
        </span>
      </a>
		</td>
		
    <td class="text">
      {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
    	{{mb_value object=$_sejour field=libelle}}
		</td>
		
    <td class="text">
	    {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
      {{assign var=kine_referent value=$bilan->_ref_kine_referent}}
			{{if $kine_referent->_id}}
      	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_referent}}
        {{assign var=kine_journee value=$bilan->_ref_kine_journee}}
			  {{if $kine_journee->_id != $kine_referent->_id}}
          / {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_journee}}
				{{/if}}
      {{/if}}
		</td>
		
		{{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
		{{if !$prescription->_id}} 
	    <td colspan="2" style="text-align: center;">
	    	<img src="images/icons/calendar-broken.png" title="Aucune prescription, planification impossible" />
	    </td>
		{{else}}
	    <td style="text-align: right;">
	      {{if $_sejour->_count_evenements_ssr}} 
	      {{$_sejour->_count_evenements_ssr}}
	      {{/if}}
	    </td>
	
	    <td style="text-align: right;">
	      {{if $_sejour->_count.evenements_ssr}} 
	      {{$_sejour->_count.evenements_ssr}}
	      {{/if}}
	    </td>
		{{/if}}

	</tr>
	{{foreachelse}}
	<tr>
		<td colspan="10">
			<em>{{tr}}CSejour.none{{/tr}}</em>
		</td>
	</tr>
	{{/foreach}}
</table>