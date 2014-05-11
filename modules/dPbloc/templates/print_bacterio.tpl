{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 15961 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr class="clear">
    <th colspan="100">
      <h1>
        <a href="#" onclick="window.print()">
          Bacterio du {{$date|date_format:$conf.date}}
          -
          {{$bloc}}
          -
          {{$operations|@count}} pr�l�vements
        </a>
      </h1>
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=COperation field=time_operation}}</th>
    <th>{{mb_title class=COperation field=libelle}}</th>
    <th>{{mb_title class=CSejour field=patient_id}}</th>
    <th>{{mb_title class=COperation field=chir_id}}</th>
    <th>{{mb_title class=COperation field=salle_id}}</th>
    <th>{{mb_title class=COperation field=flacons_bacterio}}</th>
    <th>{{mb_title class=COperation field=labo_bacterio}}</th>
    <th>{{mb_title class=COperation field=description_bacterio}}</th>
  </tr>
  {{foreach from=$operations item=_op}}
  <tr>
    <td>{{mb_value object=$_op field=_datetime_best}}</td>
    <td class="text">{{mb_include module=planningOp template=inc_vw_operation _operation=$_op}}</td>
    <td class="text">{{mb_include module=system template=inc_vw_mbobject object=$_op->_ref_patient}}</td>
    <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}</td>
    <td class="text">{{mb_include module=system template=inc_vw_mbobject object=$_op->_ref_salle}}</td>
    <td class="button">{{mb_value object=$_op field=flacons_bacterio}}</td>
    <td class="text">{{mb_value object=$_op field=labo_bacterio}}</td>
    <td class="text">{{mb_value object=$_op field=description_bacterio}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="100" class="empty">
      {{tr}}COperation.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>