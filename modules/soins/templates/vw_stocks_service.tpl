{{* $Id: vw_idx_restockage_service.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function refreshLists() {
  refreshOrders();
  refreshReceptions();

  // TODO verifier que cet onglet fonctionne
  /*var url = new Url("soins", "httpreq_vw_stock_inventory");
  url.addFormData(getForm("filter"));
  url.requestUpdate("list-inventory");*/
  
  return false;
}

function refreshOrders(endowment_item_id, stock_id){
  var url = new Url("soins", "httpreq_vw_stock_order");
  url.addFormData(getForm("filter"));
  
  var filterOrder = getForm("filter-order");
  if (filterOrder)
    url.addFormData(filterOrder);
    
  if (endowment_item_id && stock_id) {
    url.addParam("endowment_item_id", endowment_item_id);
    url.requestUpdate("stock-"+stock_id);
    refreshReceptions();
  }
  else {
    url.requestUpdate("list-order");
  }
}

function refreshReceptions(){
  var url = new Url("soins", "httpreq_vw_stock_reception");
  url.addFormData(getForm("filter"));
  
  var filterReception = getForm("filter-reception");
  if (filterReception)
    url.addFormData(filterReception);
    
  url.requestUpdate("list-reception");
}

function receiveLine(form, dontRefresh) {
  return onSubmitFormAjax(form, {onComplete: dontRefresh ? Prototype.emptyFunction : refreshLists});
}

function receiveAll(container) {
  var listForms = [];
  $(container).select("form").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && $V(f.delivery_trace_id) && $V(f.date_reception) == 'now') {
      listForms.push(f);
    }
  });

  for (i = 0; i < listForms.length; i++) {
    receiveLine(listForms[i], i != listForms.length-1);
  }
}

function terminateAll(container) {
  var ids = $(container).select("form.force.valid input[name=delivery_id]").pluck("value");
  if (confirm("Confirmez-vous l'annulation de ces "+ids.length+" r�ceptions ?\n"+
              "Notez que ce n'est pas une suppression au sens strict, mais un marquage comme \"Termin�\".\n"+
              "Cette action n'a d'effet que sur les lignes re�ues compl�tement, vous devrez valider les autres une par une.")) {
    var url = new Url();
    url.addParam("m", "dPstock");
    url.addParam("dosql", "do_validate_delivery_lines");
    url.addParam("list", ids.join('-'));
    url.requestUpdate("systemMsg", {method: "post", onComplete: refreshReceptions});
  }
}

var tabs;
Main.add(function () {
  refreshLists();
  tabs = Control.Tabs.create('tab_stocks_soins', true);
});

</script>

<ul id="tab_stocks_soins" class="control_tabs">
  <li><a href="#list-order">Commandes</a></li>
  <li><a href="#list-reception">R�ceptions <small>(0)</small></a></li>
  {{*<li><a href="#list-inventory">Inventaire</a></li>*}}
  <li>
    <form name="filter" action="?" method="get" onsubmit="return (checkForm(this) && refreshLists())">
      <input type="hidden" name="m" value="soins" />
    
      {{if $list_services|@count > 1}}
        <select name="service_id" onchange="this.form.onsubmit()" style="margin-top: -2px;">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
      {{elseif $list_services|@count == 1}}
        {{assign var=_service value=$list_services|@reset}}
        <strong>{{$_service}}</strong>
        <input type="hidden" name="service_id" value="{{$_service->_id}}" />
      {{/if}}
    </form>
  </li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-order" style="display: none;"></div>
<div id="list-reception" style="display: none;"></div>
<div id="list-inventory" style="display: none;"></div>
