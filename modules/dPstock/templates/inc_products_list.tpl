{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  selectProduct(getForm("edit_product").product_id.value);
});
</script>

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=$dPconfig.dPstock.CProduct.pagination_size}}

<table class="tbl">
  <tr>
    <th rowspan="2" style="width: 16px;"></th>
    <th colspan="10">{{mb_title class=CProduct field=name}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CProduct field=societe_id}}</th>
    <th>{{mb_title class=CProduct field=quantity}}</th>
    <th>{{mb_label class=CProduct field=item_title}}</th>
    <!--<th>{{mb_title class=CProduct field=packaging}}</th>-->
  </tr>
  {{foreach from=$list_products item=_product}}
    <tbody class="hoverable product-{{$_product->_id}}">
    <tr>
      <td rowspan="2" {{if $_product->_in_order}}class="ok"{{/if}}>
        {{mb_include module=dPstock template=inc_product_in_order product=$_product}}
      </td>
      <td colspan="10" style="font-weight: bold;">
        <a href="#1" onclick="return editProduct({{$_product->_id}})">
          {{$_product->name|truncate:80}}
        </a>
      </td>
    </tr>
    <tr>
      <td style="padding-left: 1em;" {{if $_product->cancelled}}class="cancelled"{{/if}}>
        {{if $_product->code}}
          {{mb_value object=$_product field=code}}
        {{else}}
          &ndash;
        {{/if}}
			</td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_product->_ref_societe->_guid}}')">
          {{$_product->_ref_societe}}
        </span>
      </td>
			<td style="text-align: right;">
				{{$_product->quantity}}
			</td>
      <td>
        {{$_product->item_title|spancate:25}}
      </td>
      <!--<td>{{$_product->packaging}}</td>-->
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>