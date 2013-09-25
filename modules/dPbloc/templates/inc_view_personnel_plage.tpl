{{* $Id: $*}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>Anesth�siste</th>
    <td class="greedyPane">
      <form name="editPlage" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_plagesop_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="_repeat" value="1" />
        <input type="hidden" name="_type_repeat" value="simple" />
      
        <select name="anesth_id" style="width: 15em;" onchange="onSubmitFormAjax(this.form, {onComplete: reloadPersonnelPrevu});">
          <option value="">&mdash; Aucun anesth�siste</option>
          {{foreach from=$listAnesth item=_anesth}}
            <option value="{{$_anesth->_id}}" {{if $plage->anesth_id == $_anesth->_id}}selected="selected"{{/if}}>
              {{$_anesth->_view}}
            </option>
          {{/foreach}}
        </select>
      </form>
    </td>
    <td colspan="2"></td>
  </tr>
  <tr>
    {{mb_include module=bloc template=inc_view_personnel_type  name="IADE"      list=$listPersIADE     type="iade"}}
    {{mb_include module=bloc template=inc_view_personnel_type  name="Sagefemme" list=$listPersSageFem  type="sagefemme"}}
  </tr>
  <tr>
    {{mb_include module=bloc template=inc_view_personnel_type  name="AideOp"    list=$listPersAideOp   type="op"}}
    {{mb_include module=bloc template=inc_view_personnel_type  name="Manipulateur" list=$listPersManip type="manipulateur"}}
  </tr>
  <tr>
    {{mb_include module=bloc template=inc_view_personnel_type  name="Panseuse"  list=$listPersPanseuse type="op_panseuse"}}
    <td colspan="2"></td>
  </tr>
</table>