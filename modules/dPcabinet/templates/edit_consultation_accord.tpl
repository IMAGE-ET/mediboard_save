{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
  {{mb_script module="dPprescription" script="prescription_editor"}}
  {{mb_script module="dPprescription" script="element_selector"}}
{{/if}}

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}

{{mb_script module="dPcabinet" script="edit_consultation"}}

<script type="text/javascript">
{{if !$consult->_canEdit}}
  App.readonly = true;
{{/if}}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
}

function printAllDocs() {
  var url = new Url("dPcabinet", "print_select_docs");
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
}

function submitAll() {
  return onSubmitFormAjax(getForm("editFrmExams"));
}

Main.add(function () {
  ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}");
      
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
  } 
   
  {{if $consult->_id}}
  // Chargement des antecedents, traitements, diagnostics du patients
  DossierMedical.reloadDossierPatient();
  {{/if}}
});
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>
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
