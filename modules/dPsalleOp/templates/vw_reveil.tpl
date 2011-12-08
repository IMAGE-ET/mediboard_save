{{mb_script module="bloodSalvage" script="bloodSalvage"}}

{{if $conf.dPsalleOp.CDailyCheckList.active_salle_reveil != '1' || 
     $date < $smarty.now|iso_date || 
     $check_list->_id && $check_list->validator_id}}
     
<script type="text/javascript">

Main.add(function () {
  new Control.Tabs.create('reveil_tabs', true);
  
  var url = new Url("dPsalleOp", "httpreq_reveil");
  
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
  
  // Laisser la variable updater_encours, utile dans inc_edit_check_list.tpl
  url.addParam("type", "preop");
  url.periodicalUpdate("preop", { frequency: 90 });  
  
  url.addParam("type", "encours");
  url.periodicalUpdate("encours", { frequency: 90 });  

  url.addParam("type", "ops");
  url.periodicalUpdate("ops", { frequency: 90 });

  url.addParam("type", "reveil");
  url.requestUpdate("reveil");

  url.addParam("type", "out");
  url.requestUpdate("out");
});

function refreshTabsReveil() {
  var url = new Url("dPsalleOp", "httpreq_reveil");
  
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
  
  url.addParam("type", "preop");
  url.requestUpdate("preop");
  
  url.addParam("type", "encours");
  url.requestUpdate("encours");
  
  url.addParam("type", "ops");
  url.requestUpdate("ops");
  
  url.addParam("type", "reveil");
  url.requestUpdate("reveil");
  
  url.addParam("type", "out");
  url.requestUpdate("out");
}

codageCCAM = function(operation_id){
  var url = new Url("dPsalleOp", "httpreq_codage_actes_reveil");
  url.addParam("operation_id", operation_id);
  url.popup(700,500,"Actes CCAM");
}

</script>

     
  <ul id="reveil_tabs" class="control_tabs">
    <li><a class="empty" href="#preop"  >{{tr}}SSPI.Preop{{/tr}}   <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#encours">{{tr}}SSPI.Encours{{/tr}} <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#ops"    >{{tr}}SSPI.Attente{{/tr}} <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#reveil" >{{tr}}SSPI.Reveil{{/tr}}  <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#out"    >{{tr}}SSPI.Sortie{{/tr}}  <small>(&ndash;)</small></a></li>
    
    <li style="float:right; font-weight: bold;">
      {{mb_include template=inc_filter_reveil}}
    </li>
  </ul>
    
  <hr class="control_tabs" />
  
  <div id="preop"   style="display:none"></div>
  <div id="encours" style="display:none"></div>
  <div id="ops"     style="display:none"></div>
  <div id="reveil"  style="display:none"></div>
  <div id="out"     style="display:none"></div>

{{else}}


  <div style="text-align: center">
    {{mb_include template=inc_filter_reveil}}
  </div>
    
  {{include file=inc_edit_check_list.tpl personnel=$personnels}}
{{/if}}