<tr>
  <td>
    {{if $curr_delivery->patient_id}}
      {{$curr_delivery->_ref_patient->_view}}
    {{else}}
      {{$curr_delivery->_ref_service->_view}}
    {{/if}}
  </td>
  <td>
    <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
    <div class="tooltip-trigger" 
         onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$curr_delivery->_id}}'} })">
      {{$curr_delivery->_ref_stock->_view}}
    </div>
  </td>
  <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
  <td>{{mb_value object=$curr_delivery->_ref_stock field=quantity}}</td>
  <td>{{mb_value object=$curr_delivery field=quantity}}</td>
  <td>
    {{assign var=id value=$curr_delivery->_id}}
    {{assign var=stock value=$stocks_service.$id}}
    {{$stock->quantity}}
  </td>
  <td>
  {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
    {{$trace->date_delivery|@date_format:"%d/%m/%Y"}} - <b>{{$trace->quantity}} �l�ments</b> - [{{$trace->code}}] 
    <form name="delivery-trace-{{$trace->_id}}-cancel" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
      <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
      <input type="hidden" name="_undeliver" value="1" />
      <button type="submit" class="cancel notext">Anuler</button>
    </form>
    <br />
  {{foreachelse}}
  Aucune d�livrance effectu�e pour cette dispensation<br />
  {{/foreach}}
    <script type="text/javascript">
      prepareForm("delivery-trace-{{$curr_delivery->_id}}-new");
    </script>
    <form name="delivery-trace-{{$curr_delivery->_id}}-new" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      <input type="hidden" name="date_delivery" value="now" />
      {{mb_field object=$curr_delivery field=quantity increment=1 form=delivery-trace-$id-new size=3}}
      <input type="text" name="code" value="" />
      <button type="submit" class="tick">D�livrer</button>
    </form>
  </td>
</tr>