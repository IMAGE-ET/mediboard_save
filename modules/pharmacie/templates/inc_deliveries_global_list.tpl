{{* $Id: inc_deliveries_list.tpl 7561 2009-12-09 09:58:48Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 7561 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  deliveriesTabs = Control.Tabs.create("global-deliveries-tabs", true);
  $$('a[href=#list-globales] small')[0].update('({{$deliveries|@count}})');
  
	{{if $deliveries_by_service|@count < 25}}
	  var tabs = $("global-deliveries-tabs");
	  Event.observe(window, "scroll", function(){
	    tabs.setStyle({marginTop: document.viewport.getScrollOffsets().top+"px"});
	  });
	{{/if}}
});
</script>

<table class="main layout">
  <tr>
    <td style="white-space: nowrap; width:" class="narrow">
      <form name="print-plan-cueillette-global" action="" method="get" onsubmit="return printPreparePlan(this)">
        <input type="hidden" name="m" value="pharmacie" />
        <input type="hidden" name="a" value="print_prepare_plan" />
        <input type="hidden" name="mode" value="global" />
        
        <button type="submit" class="print" style="font-weight: normal;">Plan cueillette</button>
        <br /><!-- BR required (rendering bug in Chrome) -->
        
        <ul class="control_tabs_vertical small" id="global-deliveries-tabs" style="font-size: 0.9em;">
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <li>
            {{math assign="remaining" equation="x-y" x=$_deliveries|@count y=$delivered_counts.$service_id}}
            
            <input type="checkbox" style="float: right; margin: 3px;" 
                   name="service_id[]" value="{{$service_id}}"
                   title="Cocher pour inclure ce service dans le plan de cueillette" />
                   
            <a href="#global-service-{{$service_id}}"
               style="padding-right: 2em;" 
              {{if $remaining == 0}}class="empty"{{/if}}
              >
              {{$services.$service_id}} 
              
              <small style="min-width: 3em; display: inline-block; text-align: right;">({{$remaining}}{{if $display_delivered}}/{{$_deliveries|@count}}{{/if}})</small>
            </a>
          </li>
        {{/foreach}}
        </ul>
      </form>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th style="width: 16px;"></th>
          <th>
            {{mb_colonne class=CProductStockGroup field=product_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          <th colspan="2">
            {{mb_colonne class=CProductDelivery field=date_dispensation order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$conf.dPstock.CProductStockGroup.infinite_quantity}}
            <th>Stock pharm.</th>
          {{/if}}
          <th>
            {{mb_colonne class=CProductStockGroup field=location_id order_col=$order_col order_way=$order_way function=changeSort}}
          </th>
          {{if !$conf.dPstock.CProductStockService.infinite_quantity}}
            <th>Stock serv.</th>
          {{/if}}
          <th class="narrow"><button type="button" onclick="deliverAll(deliveriesTabs.activeContainer.id)" class="tick">Tout d�livrer</button></th>
          <th>{{mb_title class=CProduct field=_unit_title}}</th>
          <th class="narrow"></th>
        </tr>
        {{foreach from=$deliveries_by_service item=_deliveries key=service_id}}
          <tbody id="global-service-{{$service_id}}" style="display: none;">
            {{foreach from=$_deliveries item=_delivery}}
              <tr id="{{$_delivery->_guid}}">
                {{include file="inc_vw_line_delivrance.tpl" curr_delivery=$_delivery}}
              </tr>
            {{foreachelse}}
              <td colspan="10" class="empty">{{tr}}CProductDelivery.none{{/tr}}</td>
            {{/foreach}}
          </tbody>
        {{foreachelse}}
        <tr>
          <td colspan="10" class="empty">{{tr}}CProductDelivery.global.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
