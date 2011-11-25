{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function() {
  Control.Tabs.setTabCount.curry('board-sejours-{{$mode}}', '{{$sejours|@count}}');
})	
</script>
<table class="tbl">
  <tr>
    <th colspan="2">
      {{mb_title class=CSejour field=patient_id}} /
      {{mb_title class=CPatient field=_age}}
		</th>
    <th>{{mb_title class=CSejour field=libelle}}</th>
    <th class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th class="narrow">{{mb_title class=CSejour field=sortie}}</th>
    <th class="narrow" colspan="2"><label title="Evenements planifi�s par le r��ducateur (cette semaine - pendant tout le s�jour)">Evt.</label></th>
  </tr>
	
{{foreach from=$sejours item=_sejour}}
  {{assign var=patient value=$_sejour->_ref_patient}}
  {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
  <tr {{if $_sejour->_count_evenements_ssr_week}} style="font-weight: bold;" {{/if}}>
    <td class="text {{if !$bilan->_encours}}arretee{{/if}}">
      {{if $_sejour->_ref_prescription_sejour->_count_recent_modif_presc}}
      <img style="float: right" src="images/icons/ampoule.png" title="Prescription recemment modifi�e"/>
      {{/if}}
    	<a href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}#planification">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
          {{mb_value object=$_sejour field=patient_id}}
        </span>
    	</a>
		</td>
		<td class="narrow">
			{{mb_value object=$patient field=_age}}    
    </td>
    <td class="text">
		  {{if $bilan->hospit_de_jour}} 
		    <img style="float: right;"title="{{mb_value object=$bilan field=_demi_journees}}" src="modules/ssr/images/dj-{{$bilan->_demi_journees}}.png" />
		  {{/if}}
    	{{mb_value object=$_sejour field=libelle}}
		</td>
    <td>{{mb_value object=$_sejour field=entree format=$conf.date}}</td>
    <td>{{mb_value object=$_sejour field=sortie format=$conf.date}}</td>

    <td style="text-align: right;">
		  {{assign var=count_evenements value=$_sejour->_count_evenements_ssr_week}}
      {{$count_evenements|ternary:$count_evenements:"-"}}
    </td>

    <td style="text-align: right;">
      {{assign var=count_evenements value=$_sejour->_count_evenements_ssr}}
      {{$count_evenements|ternary:$count_evenements:"-"}}
    </td>
  </tr>
{{foreachelse}}
<tr>
	<td colspan="10" class="empty">{{tr}}CSejour.none{{/tr}}</td>
</tr>
{{/foreach}}
</table>
