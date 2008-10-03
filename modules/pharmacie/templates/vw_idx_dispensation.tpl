<script type="text/javascript">
function refreshLists() {
  var form = getForm("filter");
  
  urlGlobales = new Url;
  urlGlobales.setModuleAction("pharmacie", "httpreq_vw_dispensations_list");
  
  urlNominatives = new Url;
  urlNominatives.setModuleAction("pharmacie", "httpreq_vw_dispensations_nominative_list");

  $A(form.elements).each (function (e) {
    urlGlobales.addParam(e.name, $V(e));
    urlNominatives.addParam(e.name, $V(e));
  });

  urlGlobales.requestUpdate("list-globales", { waitingText: null } );
  urlNominatives.requestUpdate("list-nominatives", { waitingText: null } );

  refreshStocks();
  
  return false;
}

var oFormDispensation;

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $V(oFormDispensation._code, dn[0].firstChild.nodeValue);
  $("produit_view").update(dn[1].firstChild.nodeValue);
  $V(oFormDispensation.produit, "");
}

// Autocomplete des medicaments
Main.add(function () {
  oFormDispensation = getForm('dispensation-urgence', true);

  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.addParam("produit_max", 40);

  urlAuto.autoComplete(oFormDispensation.produit, "produit_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsMedicament,
    callback: 
      function(input, queryString){
        return queryString + "&inLivret=1";
      }
  });

  refreshStocks();
});

function updateDispensationUrgence(formUrgence) {
  var formFilter = getForm("filter");
  $V(formUrgence.service_id, $V(formFilter.service_id));
  $V(formUrgence.patient_id, $V(formFilter.patient_id));
}

function refreshStocks() {
  var formFilter = getForm("filter");
  url = new Url;
  url.setModuleAction("pharmacie", "httpreq_vw_stocks_service_list");
  url.addParam("service_id", $V(formFilter.service_id));
  url.requestUpdate("list-stocks", { waitingText: null } );
}
</script>

{{include file=inc_filter_delivrances.tpl}}

<ul id="tab_delivrances" class="control_tabs">
  <li style="float: right;">
    <form title="Dispensation d'urgence" name="dispensation-urgence" action="?" method="post" onsubmit="updateDispensationUrgence(this); return (checkForm(this) && onSubmitFormAjax(this, {onComplete: refreshLists}))">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      {{mb_field object=$delivrance field=service_id hidden=true}}
      {{mb_field object=$delivrance field=patient_id hidden=true}}
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="_code" value="" class="notNull" />
      Quantit�: {{mb_field object=$delivrance field=quantity size="4" increment=true form="dispensation-urgence"}}
      Produit: <input type="text" name="produit" value="" autocomplete="off" />
      <span id="produit_view"></span>
      <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
      <button class="tick">Dispenser</button>
    </form>
  </li>
  <li><a href="#list-globales">Globales (<span id="list-globales-count">0</span>)</a></li>
  <li><a href="#list-nominatives">Nominatives (<span id="list-nominatives-count">0</span>)</a></li>
  <li><a href="#list-stocks">{{tr}}CProductStockService.more{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-globales" style="display: none;"></div>
<div id="list-nominatives" style="display: none;"></div>
<div id="list-stocks" style="display: none;"></div>