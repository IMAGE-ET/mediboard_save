{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Intervention -->
<td class="text">
  {{if $curr_plageop|is_array || $curr_plageop->spec_id}}
    <strong>Dr {{$curr_op->_ref_chir->_view}}</strong>
    <br />
  {{/if}}
  {{if $curr_op->libelle}}
    {{$curr_op->libelle}}
    <br />
  {{/if}}
  {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
    {{if !$curr_code->_code7}}<strong>{{/if}}
    <em>{{$curr_code->code}}</em>
    {{if $filter->_ccam_libelle}}
      : {{$curr_code->libelleLong|truncate:60:"...":false}}
      <br/>
    {{else}}
      ;
    {{/if}}
    {{if !$curr_code->_code7}}</strong>{{/if}}
  {{/foreach}}
</td>
<td class="button">{{$curr_op->cote|truncate:1:""|capitalize}}</td>
<td class="{{if $curr_op->type_anesth != null}}text{{else}}button{{/if}}">
  {{if $curr_op->type_anesth != null}}
  {{$curr_op->_lu_type_anesth}}
  {{else}}
  &mdash;
  {{/if}}
</td>
<td class="text">{{$curr_op->rques|nl2br}}</td>
<td class="text">
  {{if $curr_op->commande_mat == '0' && $curr_op->materiel != ''}}
  <em>Materiel manquant:</em>
  {{/if}}
  {{$curr_op->materiel|nl2br}}
</td>