{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="element_selector"}}
  {{mb_script module="prescription" script="prescription"}}
{{/if}}

{{mb_script module="planningOp" script="cim10_selector"}}
{{mb_script module="compteRendu" script="modele_selector"}}

<script>
  printFicheAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", "print_fiche");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.popup(700, 500, "printFiche");
  }

  submitAnesth = function(oForm) {
    submitFormAjax(oForm, 'systemMsg', {
      onComplete: function() {
        reloadAnesth(oForm.operation_id.value)
      }
    });
  }

  reloadPrescription = function(prescription_id){
   �Prescription.reloadPrescSejour(prescription_id, '', null, null, null, null, null);
  }

  reloadAnesth = function(operation_id){
    window.opener.location.reload(true);
    window.location.reload(true);
  }

  var constantesMedicalesDrawn = false;
  refreshConstantesHack = function(sejour_id) {
    (function(){
      if (constantesMedicalesDrawn == false && $('constantes-medicales').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id);
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  }

  refreshConstantesMedicales = function(context_guid) {
    if(context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
    }
  }

  Main.add(function () {
    // Initialisation des onglets
    if ($('main_tab_group')){
      Control.Tabs.create('main_tab_group', true);
    }

    if($('antecedents')){
      var url = new Url("cabinet", "httpreq_vw_antecedents");
      url.addParam("sejour_id","{{$operation->sejour_id}}");
      url.requestUpdate("antecedents");
    }

    if($('constantes-medicales')){
      constantesMedicalesDrawn = false;
      refreshConstantesHack('{{$operation->sejour_id}}');
    }

    {{if $isPrescriptionInstalled}}
    if($('prescription_sejour')){
      Prescription.reloadPrescSejour('','{{$operation->_ref_sejour->_id}}', null, null, '{{$operation->_id}}', null, null);
    }
    {{/if}}

    if($('Imeds_tab')){
      var url = new Url("Imeds", "httpreq_vw_sejour_results");
      url.addParam("sejour_id", {{$operation->_ref_sejour->_id}});
      url.requestUpdate('Imeds_tab');
    }
  });
</script>

{{assign var="selOp" value=$operation}}
{{assign var="sejour" value=$operation->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}

{{if $selOp->prat_visite_anesth_id}}
  {{assign var="modeles_prat_id" value=$selOp->prat_visite_anesth_id}}
{{elseif $selOp->_ref_consult_anesth->_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_consult_anesth->_ref_consultation->_ref_chir->_id}}
{{/if}}

<h1>
  {{$selOp}}
</h1>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#anesth_tab">Anesth.</a></li>
  <li><a href="#antecedents">Atcd.</a></li>
  <li onmousedown="refreshConstantesHack('{{$operation->sejour_id}}');"><a href="#constantes-medicales">Constantes</a></li>
  {{if $isPrescriptionInstalled && "dPcabinet CPrescription view_prescription"|conf:"CGroups-$g"}}
    <li><a href="#prescription_sejour_tab">Prescription</a></li>
  {{/if}}
  {{if $isImedsInstalled}}
    <li><a href="#Imeds_tab">Labo</a></li>
  {{/if}}
</ul>

{{assign var=onSubmit value="return onSubmitFormAjax(this, {onComplete: function(){ reloadAnesth(); } })"}}

<!-- Anesthesie -->
<div id="anesth_tab" style="display:none;">
  <div id="anesth">
    {{mb_include module=salleOp template=inc_vw_visite_pre_anesth}}
  </div>
</div>

<!-- Ant�c�dents -->
<div id="antecedents" style="display:none;"></div>

<!-- Constantes -->
<div id="constantes-medicales" style="display: none;"></div>

<!-- Prescription -->
{{if $isPrescriptionInstalled}}
  <div id="prescription_sejour_tab" style="display:none;">
    <div id="prescription_sejour"></div>
  </div>
{{/if}}

<!-- R�sultats labo -->
{{if $isImedsInstalled}}
  <div id="Imeds_tab" style="display:none"></div>
{{/if}}