{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="plageInfos" value=$plageJour.$colonne}}

{{if is_string($plageJour.$colonne) && $plageJour.$colonne == "empty"}}
  <td class="empty {{if $curr_mins == '00'}}hour_start{{/if}}"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "hours"}}
  <td class="empty hour_start" rowspan="4"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "full"}}
{{else}}
  <td class="nonEmpty{{$style}} {{if $curr_mins == '00'}}hour_start{{/if}}" rowspan="{{$plageInfos->_nbQuartHeure}}">
  
  {{assign var="pct" value=$plageInfos->_fill_rate}}
  {{if $pct gt 100}}
  {{assign var="pct" value=100}}
  {{/if}}
  {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
  {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
  {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
  {{else}}{{assign var="backgroundClass" value="full"}}
  {{/if}}

  {{if $colonne=="plagesConsult"}}
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$plageInfos->date}}&amp;chirSel={{$pratSel}}" 
       onmouseover="viewItem(this, '{{$plageInfos->_guid}}','{{$curr_day}}')">
      {{if $plageInfos->libelle}}{{$plageInfos->libelle}}<br />{{/if}}
      {{$plageInfos->debut|date_format:$conf.time}} - {{$plageInfos->fin|date_format:$conf.time}}
    </a>
    <div class="progressBar">
      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
      <div class="text">{{$plageInfos->_affected}} / {{$plageInfos->_total}}</div>
    </div>
  {{else}}
    <a href="?m=dPplanningOp&amp;tab=vw_idx_planning&amp;date={{$plageInfos->date}}&amp;selPrat={{$pratSel}}"
       onmouseover="viewItem(this, '{{$plageInfos->_guid}}','{{$curr_day}}')">
      {{$plageInfos->_ref_salle->nom}}<br />
      {{$plageInfos->debut|date_format:$conf.time}} - {{$plageInfos->fin|date_format:$conf.time}}
    </a>
    <div class="progressBar">
      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
      <div class="text">{{$plageInfos->_nb_operations}} Op.</div>
    </div>    
  {{/if}}
  </td>            
{{/if}}