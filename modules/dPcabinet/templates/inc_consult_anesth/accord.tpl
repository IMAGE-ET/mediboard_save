{{assign var="chir_id" value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

function reloadPrescription(prescription_id){
  Prescription.reloadPrescSejour(prescription_id, '');
}

Main.add(function () {
  var tabsConsultAnesth = Control.Tabs.create('tab-consult-anesth', false);
  {{if $app->user_prefs.ccam_consultation == 1}}
  var tabsActes = Control.Tabs.create('tab-actes', false);
  {{/if}}
  if($('prescription_sejour')){
    Prescription.reloadPrescSejour('', DossierMedical.sejour_id);
  }
});
</script>

<!-- Tab titles -->
<ul id="tab-consult-anesth" class="control_tabs">
  {{if $consult->sejour_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  <li><a href="#rpuConsult">
    RPU 
    {{if $consult->_ref_sejour->_num_dossier}}
      [{{$consult->_ref_sejour->_num_dossier}}]
    {{/if}}
  </a></li>
  {{/if}}
  
  <li><a href="#AntTrait">Ant�c�dents</a></li>
  
  {{if $isPrescriptionInstalled}}
  <li><a href="#prescription_sejour">Traitement et prescription</li>
  {{/if}}
  <li><a href="#Exams">Exam. Clinique</a></li>
  <li><a href="#Intub">Intubation</a></li>
  
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Actes</a></li>
  {{/if}}
  
  <li><a href="#ExamsComp">Exam. Comp.</a></li>
  <li><a href="#InfoAnesth">Infos. Anesth.</a></li>
  <li><a href="#fdrConsult">Docs. et R�glements</a></li>
</ul>
<hr class="control_tabs" />


<!-- Tabs -->
{{if $consult->sejour_id}}
<div id="rpuConsult" style="display: none;">{{include file="../../dPurgences/templates/inc_vw_rpu.tpl"}}</div>
{{/if}}

<div id="AntTrait" style="display: none;">{{include file="../../dPcabinet/templates/inc_ant_consult.tpl" sejour_id=$consult->_ref_consult_anesth->_ref_sejour->_id}}</div>
{{if $isPrescriptionInstalled}}
<div id="prescription_sejour" style="display: none"></div>
{{/if}}
<div id="Exams" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_examens_clinique.tpl"}}</div>
<div id="Intub" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/intubation.tpl"}}</div>

{{if $app->user_prefs.ccam_consultation == 1}}
<div id="Actes" style="display: none;">
  <ul id="tab-actes" class="control_tabs">
    <li><a href="#ccam">Actes CCAM</a></li>
    <li><a href="#ngap">Actes NGAP</a></li>
    {{if $consult->sejour_id}}
    <li><a href="#cim">Diagnostics</a></li>
    {{/if}}
  </ul>
  <hr class="control_tabs"/>
  
  <div id="ccam" style="display: none;">
    {{assign var="module" value="dPcabinet"}}
    {{assign var="subject" value=$consult}}
    {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
  </div>
  
  <div id="ngap" style="display: none;">
    <div id="listActesNGAP">
      {{assign var="_object_class" value="CConsultation"}}
      {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
    </div>
  </div>
  
  {{if $consult->sejour_id}}
  <div id="cim" style="display: none;">
    {{assign var="sejour" value=$consult->_ref_sejour}}
    {{include file="../../dPsalleOp/templates/inc_diagnostic_principal.tpl" modeDAS="1"}}
  </div>
  {{/if}}
</div>
{{/if}}

<div id="ExamsComp" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_examens_complementaire.tpl"}}</div>
<div id="InfoAnesth" style="display: none;">{{include file="../../dPcabinet/templates/inc_consult_anesth/acc_infos_anesth.tpl"}}</div>
<div id="fdrConsult" style="display: none;">{{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}</div>
