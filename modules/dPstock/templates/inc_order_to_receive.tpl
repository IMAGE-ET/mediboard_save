{{* $Id: inc_order.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
closeOrder = function(form) {
  if (confirm('Etes-vous s�r de vouloir cl�turer cette commande ?\nCeci aura pour effet de marquer cette commande comme re�ue, sans recevoir les articles non encore re�us.')) {
    onSubmitFormAjax(form, {onComplete: function(){window.close()}});
  }
  return false;
}

receiveOrderItems = function(form, container) {
  if (!confirm('Etes-vous s�r de vouloir effectuer la r�ception de toute cette commande ?')) {
    return false;
  }
  
  var data = [];
  var forms = $(container).select("form");

  forms.each(function(f){
    var d = $(f).serialize(true);
    data.push(d);
  });
  
  $V(form._order_items, Object.toJSON(data));
  
  return onSubmitFormAjax(form);
}
</script>

{{mb_include module=system template=CMbObject_view object=$order}}

<hr />

<form name="closeOrder-{{$order->_id}}" method="post" action="?" onsubmit="return closeOrder(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_aed" />
  <input type="hidden" name="received" value="1" />
  {{mb_key object=$order}}
  
  <button type="submit" class="cancel" style="float: left;">
    Cl�turer la r�ception de cette commande
  </button>
</form>

<form name="receiveOrder-{{$order->_id}}" method="post" action="?" onsubmit="return receiveOrderItems(this, 'order-items-{{$order->_id}}')">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_receive_aed" />
  <input type="hidden" name="_order_items" value="" />
  <input type="hidden" name="callback" value="location.reload" />
  {{mb_key object=$order}}
  
  <button type="submit" class="tick" style="float: right;">
    Recevoir toute cette commande
  </button>
</form>

<table class="tbl" id="order-items-{{$order->_id}}">
  <tr>
    <th style="width: 50%;"></th>
    <th style="text-align: center;" class="narrow">
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      {{mb_title class=CProductOrderItem field=_unit_quantity}}
    {{else}}
      {{mb_title class=CProductOrderItem field=quantity}}
    {{/if}}
    </th>
    <th style="text-align: center;" class="narrow">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th style="text-align: center;" class="narrow">{{mb_title class=CProductOrderItem field=_price}}</th>
    <th style="text-align: center;" class="narrow">D�j� re�u</th>
    <th style="text-align: right;" class="narrow"></th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}" class="hoverable">
    {{include file="inc_order_to_receive_item.tpl"}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
