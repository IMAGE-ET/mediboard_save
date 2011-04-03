{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table id="{{$plateau->_guid}}" class="main" style="border-spacing: 4px; border-collapse: separate; width: auto;">
  {{if !$conf.ssr.repartition.show_tabs}}
	<tr>
		<th class="title" colspan="{{$plateau->_ref_techniciens|@count}}">
			{{$plateau}}
		</th>
	</tr>
	{{/if}}
  <tr>
	  {{foreach from=$plateau->_ref_techniciens item=_technicien}}
      <td style="width: 150px;">
			{{mb_include template=inc_repartition_technicien technicien_id=$_technicien->_id}}
			</td>
		{{foreachelse}}
	    <td style="width: 150px;" class="text empty">{{tr}}CPlateauTechnique-back-techniciens.empty{{/tr}}</td>
	  {{/foreach}}
  </tr>	
</table>