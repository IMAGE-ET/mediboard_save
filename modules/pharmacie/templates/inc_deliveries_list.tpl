{{if $mode == "global"}}
<button type="button" class="print" onclick="printPreparePlan()">Imprimer plan de cueillette</button>
<table class="tbl">
  <!-- Affichage des delivrances globales -->
  <tr>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
      <th>Stock pharmacie</th>
    {{/if}}
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>Stock service</th>
    <th></th>
  </tr>
  {{foreach from=$deliveries_global item=curr_delivery_global}}
    {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$curr_delivery_global}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
  $$('a[href=#list-globales] small').first().update('({{$deliveries_global|@count}})');
</script>

{{elseif $mode == "nominatif"}}
<button type="button" class="print" onclick="printPreparePlan(true)">Imprimer plan de cueillette</button>
<table class="tbl">
  <!-- Affichage des delivrances nominatives -->
  <tr>
    <th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
      <th>Stock pharmacie</th>
    {{/if}}
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>Stock service</th>
    <th></th>
  </tr>
  {{foreach from=$deliveries_nominatif item=curr_delivery_nominatif}}
    {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$curr_delivery_nominatif}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.nominatif.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
<script type="text/javascript">
  $$('a[href=#list-nominatives] small').first().update('({{$deliveries_nominatif|@count}})');
</script>
{{/if}}