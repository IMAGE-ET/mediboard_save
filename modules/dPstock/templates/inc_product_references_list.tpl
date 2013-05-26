{{* $Id: inc_references_list.tpl 7948 2010-01-30 18:54:06Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 *}}

<div id="tab-references" style="display: none;">

<button class="new" type="button" onclick="location.href='?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id={{$product->_id}}'">
  {{tr}}CProductReference-title-create{{/tr}}
</button>

<table class="tbl">
  <tr>
    <th class="narrow">{{mb_title class=CProductReference field=code}}</th>
    <th>{{mb_title class=CProductReference field=societe_id}}</th>
     
    {{if $conf.dPstock.CProductReference.show_cond_price}}
      <th class="narrow">{{mb_title class=CProductReference field=_cond_price}}</th>
    {{/if}}
		
    <th colspan="2" class="narrow">{{mb_title class=CProductReference field=price}}</th>
  </tr>
  
	{{foreach from=$product->_ref_references item=_reference}}
  {{assign var=_product value=$_reference->_ref_product}}
  <tr>
    <td {{if $_reference->cancelled}}class="cancelled"{{/if}}>
      <a href="?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id={{$_reference->_id}}">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_guid}}')">
          {{if $_reference->code}}
            {{mb_value object=$_reference field=code}}
          {{else}}
            [Aucun code]
          {{/if}}
        </strong>
      </a>
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_reference->_ref_societe->_guid}}')">
        {{mb_value object=$_reference field=societe_id}}
      </span>
    </td>

    {{if $conf.dPstock.CProductReference.show_cond_price}}
    <td style="text-align: right;">{{mb_value object=$_reference field=_cond_price}}</td>
    {{/if}}

    <td style="text-align: right;">
      <label title="{{$_reference->quantity}} x {{$_product->item_title}}">
        {{mb_value object=$_reference field=quantity}} x
      </label>
    </td>
    <td style="text-align: right;"><strong>{{mb_value object=$_reference field=price}}</strong></td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}CProductReference.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
</div>