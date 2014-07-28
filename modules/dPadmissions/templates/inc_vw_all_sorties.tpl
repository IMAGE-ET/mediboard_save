{{* $Id: inc_vw_all_admissions.tpl 10988 2010-12-30 15:00:26Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 10988 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="3">
      <a style="display: inline;" href="#1" onclick="$V(getForm('selType').date, '{{$lastmonth}}'); reloadFullSorties()">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="#1" onclick="$V(getForm('selType').date, '{{$nextmonth}}'); reloadFullSorties()">&gt;&gt;&gt;</a>
    </th>
  </tr>

  <tr>
    <th class="text">
      Date
    </th>
    <th class="text">
      <a class="{{if $selSortis=='0'}}selected{{else}}selectable{{/if}}" title="Toutes les sorties" href="#1" onclick="filterAdm(0)">
        Sorties
      </a>
    </th>

    <th class="text">
      {{if $conf.ref_pays == "2" && $current_m == "dPpmsi"}}
        <a class="{{if $selSortis=='nf'}}selected{{else}}selectable{{/if}}" title="Sorties non factur�es" href="#1" onclick="filterAdm('nf')">
          Non fact.
        </a>
      {{else}}
        <a class="{{if $selSortis=='n'}}selected{{else}}selectable{{/if}}" title="Sorties non effectu�es" href="#1" onclick="filterAdm('n')">
          Non Eff.
        </a>
      {{/if}}
    </th>
  </tr>

  {{foreach from=$days key=day item=counts}}
  <tr {{if $day == $date}} class="selected" {{/if}}>
    {{assign var=day_number value=$day|date_format:"%w"}}
    <td style="text-align: right;
      {{if array_key_exists($day, $bank_holidays)}}
        background-color: #fc0;
      {{elseif $day_number == '0' || $day_number == '6'}}
        background-color: #ccc;
      {{/if}}">
      <a href="#1" onclick="reloadSortiesDate(this, '{{$day|iso_date}}')" title="{{$day|date_format:$conf.longdate}}">
        <strong>
	        {{$day|date_format:"%a"|upper|substr:0:1}}
	        {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $selSortis=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
    </td>
    {{if $conf.ref_pays == "2" && $current_m == "dPpmsi"}}
    <td {{if $selSortis=='nf' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num5}}{{$counts.num5}}{{else}}-{{/if}}
    </td>
    {{else}}
    <td {{if $selSortis=='n' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10" class="empty">Pas d'admission ce mois</td>
	</tr>
  {{/foreach}}
</table>