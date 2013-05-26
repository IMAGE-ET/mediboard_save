{{* $Id: vw_idx_stock_location.tpl 8024 2010-02-08 09:20:55Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 8024 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(print);
</script>

<h2>{{$endowment}}</h2>

<table class="main tbl">
  {{foreach from=$endowment->_back.endowment_items item=_item}}
    {{if !$_item->cancelled}}
    <tr>
      <td>{{mb_value object=$_item field=quantity}}</td>
      <td>{{mb_value object=$_item field=product_id}}</td>
    </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">{{tr}}CProductEndowment.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>