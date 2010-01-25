{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=product_selector}}

<script type="text/javascript">
function refreshList(){
  var url = new Url("dPstock", "httpreq_vw_stocks_service_list");
  url.addFormData("filter-stocks");
  url.requestUpdate("list-stocks-service");
  return false;
}

function changePage(page){
  $V(getForm("filter-stocks").start, page);
}

Main.add(refreshList);

ProductSelector.init = function(){
  this.sForm = "edit_stock";
  this.sId   = "product_id";
  this.sView = "product_name";
  this.sUnit = "_unit_title";
  this.pop({{$stock->product_id}});
}
</script>

<table class="main">
  <tr>
    <td rowspan="3">
      <form name="filter-stocks" action="?" method="post" onsubmit="return refreshList()">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CProductCategory.all{{/tr}}</option>
          {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        
        <select name="service_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CService.all{{/tr}}</option>
          {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->_view}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start,0)" />
        
        <button type="button" class="search notext" onclick="this.form.onsubmit()">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit()"></button>
      </form>
  
      <div id="list-stocks-service"></div>
    </td>

    <td style="width: 1%;">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_service_id=0">{{tr}}CProductStockService.create{{/tr}}</a>
      
      <form name="edit_stock" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_stock_service_aed" />
        <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
        <input type="hidden" name="service_id" value="{{$stock->service_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            {{if $stock->_id}}
            <th class="title modify" colspan="2">{{$stock->_view|truncate:60}}</th>
            {{else}}
            <th class="title" colspan="2">{{tr}}CProductStockService.create{{/tr}}</th>
            {{/if}}
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="quantity"}}</th>
            <td>
              {{mb_field object=$stock field="quantity" form="edit_stock" size=4 increment=true min=0}}
              <input type="text" name="_unit_title" readonly="readonly" disabled="disabled" value="{{$stock->_ref_product->_unit_title}}" size="30" style="border: none; background: transparent; color: inherit;" />
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="product_id"}}</th>
            <td>
              {{mb_field object=$stock field="product_id" hidden=true}}
              <input type="text" name="product_name" value="{{$stock->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
              <button class="search notext" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
              <button class="edit notext" type="button" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id='+this.form.product_id.value">{{tr}}Edit{{/tr}}</button>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="common"}}</th>
            <td>{{mb_field object=$stock field="common"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="service_id"}}</th>
            <td>
              <select name="service_id">
                <option value="0">&mdash; {{tr}}CService.select{{/tr}} &mdash;</option>
                {{foreach from=$list_services item=curr_service}}
                <option value="{{$curr_service->_id}}" {{if $stock->service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="order_threshold_critical"}}</th>
            <td>{{mb_field object=$stock field="order_threshold_critical" form="edit_stock" size=4 increment=true min=0}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="order_threshold_min"}}</th>
            <td>{{mb_field object=$stock field="order_threshold_min" form="edit_stock" size=4 increment=true min=0}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="order_threshold_optimum"}}</th>
            <td>{{mb_field object=$stock field="order_threshold_optimum" form="edit_stock" size=4 increment=true min=0}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$stock field="order_threshold_max"}}</th>
            <td>{{mb_field object=$stock field="order_threshold_max" form="edit_stock" size=4 increment=true min=0}}</td>
          </tr>
          <tr>
            <td class="button" colspan="4">
              {{if $stock->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$stock->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>