<table class="tbl">
  <tr>
    <th>Produit</th>
    <th>Quantit�</th>
    <th>PU</th>
    <th>Prix</th>
    {{if $order->date_ordered}}
    <th>Re�u</th>
    {{/if}}
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}">
    {{include file="inc_order_item.tpl"}}
    </tbody>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
    <span style="float: right;">Total : {{mb_value object=$order field=_total}}</span>
    </td>
  </tr>
</table>
