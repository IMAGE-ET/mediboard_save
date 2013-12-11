{{* $Id: inc_vw_all_admissions.tpl 6147 2009-04-21 14:41:09Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6147 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_preadmission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_preadmission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  <tr>
    <th class="text">Date</th>
    <th class="text">Pr�-ad.</th>
  </tr>
  {{foreach from=$days key=day item=count}}
    <tr class="preAdmission-day {{if $day == $date}}selected{{/if}}" id="paday_{{$day}}">
      {{assign var=day_number value=$day|date_format:"%w"}}
      <td style="text-align: right;
        {{if array_key_exists($day, $bank_holidays)}}
          background-color: #fc0;
        {{elseif $day_number == '0' || $day_number == '6'}}
          background-color: #ccc;
        {{/if}}">
        <a href="#" onclick="Admissions.updateListPreAdmissions('{{$day|iso_date}}', 0)">
          <strong>
            {{$day|date_format:"%a"|upper|substr:0:1}}
            {{$day|date_format:"%d"}}
          </strong>
        </a>
      </td>
      <td style="text-align: center;" {{if !$count.total}}class="empty"{{/if}}>
        {{$count.total}}
      </td>
    </tr>
  {{/foreach}}
</table>