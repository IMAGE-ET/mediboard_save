{{if $mode == "global"}}
<table class="tbl">
  <!-- Affichage des delivrances globales -->
  <tr>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>R�ception</th>
  </tr>
  {{foreach from=$deliveries_global item=curr_delivery_global}}
    {{include file="inc_restockages_service_line.tpl" curr_delivery=$curr_delivery_global}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
$('list-globales-count').update({{$deliveries_global|@count}});
</script>
  
{{elseif $mode == "nominatif"}}
<table class="tbl">
  <!-- Affichage des delivrances nominatives -->
  <tr>
    <th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>R�ception</th>
  </tr>
  {{foreach from=$deliveries_nominatif item=curr_delivery_nominatif}}
    {{include file="inc_restockages_service_line.tpl" curr_delivery=$curr_delivery_nominatif}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.nominatif.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
$('list-nominatives-count').update({{$deliveries_nominatif|@count}});
</script>
{{/if}}
