{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}


<script type="text/javascript">


function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadTiming(oForm.operation_id.value) } });
}

function reloadTiming(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", "systemMsg");
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() { reloadAnesth(oForm.operation_id.value) } });
}

function reloadAnesth(operation_id){
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", "systemMsg", { onComplete: loadActes(operation_id,"{{$selOp->chir_id}}") });
}



function pageMain() {
  PairEffect.initGroup("acteEffect");
  
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->operation_id}}
  new Control.Tabs('main_tab_group');
  {{/if}}
}
</script>

<table class="main">
  <tr>
    <td style="width: 200px;" id="listplages"></td>
    <td class="greedyPane">
      <table class="form">
        {{if $selOp->operation_id}}
        <tr>
          <th class="title" colspan="2">
            {{$selOp->_ref_sejour->_ref_patient->_view}} 
            ({{$selOp->_ref_sejour->_ref_patient->_age}} ans 
            {{if $selOp->_ref_sejour->_ref_patient->_age != "??"}}- 
            {{$selOp->_ref_sejour->_ref_patient->naissance|date_format:"%d/%m/%Y"}}{{/if}})
            &mdash; Dr. {{$selOp->_ref_chir->_view}}
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Timmings</a></li>
              <li><a href="#two">Anesth�sie</a></li>
              <li><a href="#three">Codage</a></li>
            </ul>
          </td>
        </tr>
          
        {{include file="inc_timings_anesth.tpl"}}
      
        <!-- <tbody id = "ccam"> -->
        <tr id="three">
          <th class="category" style="vertical-align: middle">
            Actes<br /><br />
            {{tr}}{{$selOp->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
              <br />
              C�t� {{tr}}COperation.cote.{{$selOp->cote}}{{/tr}}
              <br />
              ({{$selOp->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>
          
          <td>
            <div id="ccam">
            {{assign var="subject" value=$selOp}}
            {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
            </div>
          </td>
        </tr>
        <!-- </tbody> -->
        
        {{if $selOp->materiel}}
        <tr>
          <th class="category">Mat�riel</th>
          <td><strong>{{$selOp->materiel|nl2br}}</strong></td>
        </tr>
        {{/if}}
        {{if $selOp->rques}}
        <tr>
          <th class="category">Remarques</th>
          <td>{{$selOp->rques|nl2br}}</td>
        </tr>
        {{/if}}
        {{else}}
        <tr>
          <th class="title">
            S�lectionnez une op�ration
          </th>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>