{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="prescription_editor"}}
{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url();
  url.setModuleAction("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim", { 	waitingText : null } );
}


function verifNonEmpty(oElement){
  var notWhitespace = /\S/;
  if(notWhitespace.test(oElement.value)){
    return true;
  }
  return false;
}

function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_select_docs");
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
  return;
}

function showAll(patient_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_resume");
  url.addParam("dialog", 1);
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}

function pasteText(formName) {
  var oForm = document.editFrmExams;
  var aide = eval("oForm._aide_" + formName);
  var area = eval("oForm." + formName);
  insertAt(area, aide.value + '\n')
  aide.value = 0;
}

function submitAll() {
  var oForm = document.editFrmExams;
  submitFormAjax(oForm, 'systemMsg');
}

function updateList() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");

  url.addParam("selConsult", "{{$consult->consultation_id}}");
  url.addParam("prat_id", "{{$userSel->user_id}}");
  url.addParam("date", "{{$date}}");
  url.addParam("vue2", "{{$vue}}");
  url.addParam("current_m", "{{$current_m}}");

  url.periodicalUpdate('listConsult', { frequency: 90 });
}

function pageMain() {
  updateList();
  
  {{if $consult->_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {{/if}}
  
  {{if $current_m == "dPurgences" && $consult->_id}}
    regFieldCalendar("editRPU", "sortie", true);
  {{/if}}
  
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
    Try.these(document.editAddictFrm.type.onchange);
  } 
   
  {{if $consult->_id}}
  
  // Chargement des antecedents, traitements, addictions, diagnostics du patients
  reloadDossierMedicalPatient();
  
  var oAccord = new Rico.Accordion( $('accordionConsult'), { 
    panelHeight: ViewPort.SetAccordHeight('accordionConsult' ,{ iBottomMargin : 10 } ),
    showDelay:50, 
    showSteps:5
  } );
  
  {{/if}}

}
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    <td class="greedyPane">
			{{include file="../../dPpatients/templates/inc_intermax.tpl"}}
			
      {{if $consult->_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      <div id="finishBanner">
      {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
      </div>
      {{include file="../../dPcabinet/templates/inc_patient_infos_accord_consult.tpl"}}
      {{include file="../../dPcabinet/templates/acc_consultation.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>
