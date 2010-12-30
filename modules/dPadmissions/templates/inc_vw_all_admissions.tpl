{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
		
  <tr>
    <th rowspan="2">Date</th>
  </tr>

  <tr>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} title="Toutes les admissions"     href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=0">Adm.     </a></th>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='n'}}"selected"{{else}}"selectable"{{/if}} title="Admissions non pr�par�es"  href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=n">Non pr�p.</a></th>
    <th class="text"><a class={{if $selAdmis=='n' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} title="Admissions non effectu�es" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=n&amp;selSaisis=0">Non eff. </a></th>
  </tr>

  {{foreach from=$days key=day item=counts}}
  <tr {{if $day == $date}}class="selected"{{/if}}>
    {{assign var=day_number value=$day|date_format:"%w"}}
    <td align="right"
      {{if in_array($day, $bank_holidays)}}
        style="background-color: #fc0"
      {{elseif $day_number == '0' || $day_number == '6'}}
        style="background-color: #ccc;"
      {{/if}}>
      <a href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
        <strong>
	        {{$day|date_format:"%a"|upper|substr:0:1}}
	        {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='n' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num3}}{{$counts.num3}}{{else}}-{{/if}}
    </td>
    <td {{if $selAdmis=='n' && $selSaisis=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10"><em>Pas d'admission ce mois</em></td>
	</tr>
  {{/foreach}}
</table>