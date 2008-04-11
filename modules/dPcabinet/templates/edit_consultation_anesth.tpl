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
  url.requestUpdate("cim", { waitingText : null } );
}

var tabSejour = {{$tabSejour|@json}};

function verifNonEmpty(oElement){
  var notWhitespace = /\S/;
  return notWhitespace.test(oElement.value);
}

function view_history_consult(id){
  url = new Url();
  url.setModuleAction("dPcabinet", "vw_history");
  url.addParam("consultation_id", id);
  url.popup(600, 500, "consult_history");
}

function submitForm(oForm) {
  submitFormAjax(oForm, 'systemMsg');
}

function submitOpConsult() {
  var oForm = document.addOpFrm;
  submitFormAjax(oForm, 'systemMsg', { onComplete: reloadConsultAnesth } ); 
}

function submitAll() {
  var oForm = document.editAnesthPatFrm;
  submitFormAjax(oForm, 'systemMsg');
  var oForm1 = document.editFrmIntubation;
  submitFormAjax(oForm1, 'systemMsg');
  var oForm2 = document.editExamCompFrm;
  submitFormAjax(oForm2, 'systemMsg');  
}

function showAll(patient_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "vw_resume");
  url.addParam("dialog", 1);
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}

function printFiche() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printFiche");
  return;
}

function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_select_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
  return;
}

function updateList() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
  url.addParam("selConsult", "{{$consult->consultation_id}}");
  url.addParam("prat_id", "{{$userSel->user_id}}");
  url.addParam("date", "{{$date}}");
  url.addParam("vue2", "{{$vue}}");
  url.periodicalUpdate('listConsult', { frequency: 90 });
}

function reloadConsultAnesth() {
  // Mise a jour du champ _sejour_id pour la creation d'antecedent, de traitement et d'addiction
  document.editTrmtFrm._sejour_id.value   = tabSejour[document.addOpFrm.operation_id.value];
  document.editAntFrm._sejour_id.value    = tabSejour[document.addOpFrm.operation_id.value];
  document.editAddictFrm._sejour_id.value = tabSejour[document.addOpFrm.operation_id.value];
  
  // refresh de la liste des antecedents du sejour
  reloadDossierMedicalSejour();
  reloadDossierMedicalPatient();
   
  // Reload Intervention
  var consultUrl = new Url;
  consultUrl.setModuleAction("dPcabinet", "httpreq_vw_consult_anesth");
  consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  consultUrl.requestUpdate('consultAnesth', { waitingText: null } );
  
  // Reload Infos Anesth
  var infosAnesthUrl = new Url;
  infosAnesthUrl.setModuleAction("dPcabinet", "httpreq_vw_choix_anesth");
  infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  infosAnesthUrl.requestUpdate('InfoAnesthContent', { waitingText: null } );

  var urlPrescription = new Url;
  urlPrescription.setModuleAction("dPcabinet", "httpreq_vw_prescription_exam_comp");
  urlPrescription.addParam("sejour_id", document.editAntFrm._sejour_id.value);
  urlPrescription.requestUpdate('viewPrescriptionSejour', { waitingText: null } );
}

function pageMain() {

  //rafraichissement de la liste des consultations
  updateList();  
  
  // Chargement pour le sejour
  reloadDossierMedicalSejour();
  
  {{if $consult->consultation_id}}
  new PairEffect("listConsult", { sEffect : "appear", bStartVisible : true });
  regFieldCalendar("editAntFrm", "date");
  regFieldCalendar("editTrmtFrm", "debut");
  regFieldCalendar("editTrmtFrm", "fin");
  {{/if}}

  if (document.editAntFrm) {
    document.editAntFrm.type.onchange();
    Try.these(document.editAddictFrm.type.onchange);
  }
  
  {{if $consult->_id}}
  // Chargement des antecedents, traitements, addictions, diagnostics du patients
  reloadDossierMedicalPatient();
  {{/if}}
}

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 200px; vertical-align: top;" />
    <td class="greedyPane" id="tdConsultation">
    
    {{include file="../../dPpatients/templates/inc_intermax.tpl"}}
    
    {{if $consult->consultation_id}}
      {{assign var="patient" value=$consult->_ref_patient}}
      {{include file="../../dPcabinet/templates/inc_consult_anesth/patient_infos.tpl"}}      
      {{include file="../../dPcabinet/templates/inc_consult_anesth/accord.tpl"}}
      <div id="finishBanner">
      {{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
      </div>
    {{/if}}

    </td>
  </tr>
</table>