{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function refreshLists() {
  var form = getForm("filter");
  var urlGlobales = new Url("pharmacie", "httpreq_vw_deliveries_list");
  var urlNominatives = new Url("pharmacie", "httpreq_vw_deliveries_list");

  $A(form.elements).each (function (e) {
    urlGlobales.addParam(e.name, $V(e));
    urlNominatives.addParam(e.name, $V(e));
  });

  // To choose wether we want global or nominative deliveries
  urlGlobales.addParam("mode", "global");
  urlGlobales.requestUpdate("list-global");

  urlNominatives.addParam("mode", "nominatif");
  urlNominatives.requestUpdate("list-nominatif");

  return false;
}

function refreshDeliveryLine(delivery_id) {
  var form = getForm("filter");
  var url = new Url("pharmacie", "httpreq_vw_deliveries_list");
  url.addParam("delivery_id", delivery_id);
  url.requestUpdate("CProductDelivery-"+delivery_id);
}

function printPreparePlan(form) {
  var url = new Url("pharmacie", "print_prepare_plan");
  url.addFormData(form);
  url.pop(800, 600, 'Plan de cueillette');
  return false;
}

function deliverLine(form, dontRefresh, refreshAll) {
  return onSubmitFormAjax(form, {
    onComplete: 
      dontRefresh ? Prototype.emptyFunction : 
        refreshAll ? refreshLists : refreshDeliveryLine.curry($V(form.delivery_id) || $V(form._delivery_id))
  });
}

function deliverAll(container) {
  var i, listForms = [];
  $(container).select("form.deliver").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && $V(f.delivery_id) && $V(f.date_delivery) == 'now' && parseInt($V(f.quantity)) > 0) {
    	listForms.push(f);
    }
  });

  for (i = 0; i < listForms.length; i++) {
	  deliverLine(listForms[i], i != listForms.length-1, true);
  }
}

changeSort = function(order_col, order_way){
  var form = getForm("filter");
  $V(form.order_col, order_col);
  $V(form.order_way, order_way);
  form.onsubmit();
}
</script>

<script type="text/javascript">
var tabs;
Main.add(function () {
  tabs = Control.Tabs.create('tab_delivrances', true);
  refreshLists();
});
</script>

<form name="filter" action="?" method="get" onsubmit="if(window.loadSuivi) loadSuivi($V(this.sejour_id)); return (checkForm(this) && refreshLists())">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="order_col" value="{{$order_col}}" />
  <input type="hidden" name="order_way" value="{{$order_way}}" />
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_datetime_min form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_datetime_max form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <td>
        <label>
          <input type="checkbox" name="display_delivered" onclick="this.form.onsubmit()" /> Afficher les d�livrances complet�es
        </label>
      </td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<div class="small-info">
  L'ergonomie de cet affichage a �t� modifi�e:
  <ul>
    <li>Au survol de la date de pr�paration, il est possible de connaitre <strong>l'intervalle des dates de r�assort</strong></li>
    <li>L'affichage d'un <strong>pilulier</strong> est maintenant disponible pour les nouvelles d�livrances nominatives</li>
  </ul>
</div>

<ul id="tab_delivrances" class="control_tabs">
  <li><a href="#list-global">Globales <small>(0)</small></a></li>
  <li><a href="#list-nominatif">Nominatives <small>(0)</small></a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-global" style="display: none;"></div>
<div id="list-nominatif" style="display: none;"></div>
