{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=id value=$curr_delivery->_id}}
{{assign var=product value=$curr_delivery->_ref_stock->_ref_product}}

<td {{if $product->_in_order}}class="ok"{{/if}}>
  {{mb_include module=dPstock template=inc_product_in_order product=$product}}
</td>

{{if $curr_delivery->patient_id}}
  <td>{{$curr_delivery->_ref_patient->_view}}</td>
{{/if}}
<td>
  {{if @$line_refresh}}
  <script type="text/javascript">
    if (!$V(getForm("filter").display_delivered) && ({{$curr_delivery->_delivered|ternary:1:0}} || '{{$curr_delivery->date_delivery}}')) {
      $("CProductDelivery-{{$curr_delivery->_id}}").hide();
      Main.add(function(){
        $("CProductDelivery-{{$curr_delivery->_id}}").remove();
      });
    }
  </script>
  {{/if}}
  
  <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
  <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
    {{$curr_delivery->_ref_stock->_view}}
  </span>
  {{if $curr_delivery->comments}}
   - [{{$curr_delivery->comments}}]
  {{/if}}
</td>
<td style="text-align: center;">{{mb_ditto name="date-$service_id" value=$curr_delivery->date_dispensation|date_format:$dPconfig.date}}</td>
<td style="text-align: center;">{{mb_ditto name="time-$service_id" value=$curr_delivery->date_dispensation|date_format:$dPconfig.time}}</td>

{{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
<td style="text-align: center;">
  <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_id={{$curr_delivery->_ref_stock->_id}}" title="{{tr}}CProductStockGroup-title-modify{{/tr}}">
    {{mb_value object=$curr_delivery->_ref_stock field=quantity}}
  </a>
</td>
{{/if}}

<td>
  {{mb_value object=$curr_delivery->_ref_stock->_ref_location field=name}}
</td>

{{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
<td style="text-align: center;">
  {{assign var=stock value=$stocks_service.$id}}
  <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$stock->_id}}" title="{{tr}}CProductStockService-title-modify{{/tr}}">
    {{$stock->quantity}}
  </a>
</td>
{{/if}}

<td>
  {{assign var=remaining value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
  
  {{if $remaining < 1}}
    <button type="button" class="down notext" onclick="$(this).next('table').toggle()"></button> 
    {{$remaining}} restants
  {{/if}}
  
  <table class="layout" {{if $remaining < 1}}style="display: none"{{/if}}>
  {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
    <tr>
      <td class="button narrow">
        {{if !$trace->date_reception}}
          <form name="delivery-trace-{{$trace->_id}}-cancel" action="?" method="post" onsubmit="return deliverLine(this)">
            <input type="hidden" name="m" value="dPstock" /> 
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
            <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
            <input type="hidden" name="_delivery_id" value="{{$curr_delivery->_id}}" /> <!-- used by refreshDeliveryLine -->
            <input type="hidden" name="_undeliver" value="1" />
            <button type="submit" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
          </form>
        {{else}}
          <img src="images/icons/tick.png" title="D�livr�" />
        {{/if}}
      </td>
      <td class="narrow">
        {{$trace->quantity}} �l�ments
      </td>
      <td>
        {{$trace->date_delivery|@date_format:"%d/%m/%Y"}}
      </td>
      <td>
        {{if $trace->code}}
          [{{$trace->code}}] 
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
  
    <tr>
      <td colspan="10" title="Quantit� d'origine: {{mb_value object=$curr_delivery field=quantity}}">
        <form name="delivery-trace-{{$curr_delivery->_id}}-new" action="?" method="post" class="deliver"
              onsubmit="return deliverLine(this)" {{if $curr_delivery->_delivered}}style="display: none;"{{/if}}>
          <input type="hidden" name="m" value="dPstock" /> 
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
          <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
          <input type="hidden" name="date_delivery" value="now" />
          {{mb_field object=$curr_delivery field=quantity increment=1 form=delivery-trace-$id-new size=2 value=$remaining}}
          <input type="text" name="code" value="" size="8" />
          <button type="submit" class="tick notext" title="D�livrer">D�livrer</button>
        </form>
      </td>
    </tr>
  </table>
</td>
<td class="text">
  {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
</td>
<td>
  <form name="delivery-force-{{$curr_delivery->_id}}-receive" action="?" method="post" 
        onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDeliveryLine.curry($V(this.delivery_id))})">
    <input type="hidden" name="m" value="dPstock" /> 
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_delivery_aed" />
    {{mb_key object=$curr_delivery}}
    <input type="hidden" name="date_delivery" value="{{if !$curr_delivery->date_delivery}}now{{/if}}" />
    <button type="submit" class="{{$curr_delivery->date_delivery|ternary:'tick':'cancel'}} notext" title="Marquer comme{{$curr_delivery->date_delivery|ternary:' non':''}} re�u"></button>
  </form>
</td>