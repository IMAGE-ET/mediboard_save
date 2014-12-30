{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if !@$modeles_prat_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_anesth->_id}}
{{/if}}

<script>
  reloadDocumentsAnesth = function () {
    var oForm = getForm("anesthInterv");
    if(oForm) {
      var sAnesth_id = $V(oForm.anesth_id);
    } else {
      var sAnesth_id = $V(getForm("visiteAnesth").prat_anesth_id);
    }
    $$('.documents-CConsultAnesth-{{$consult_anesth->_id}}').each(function(doc){
      Document.refresh(doc, {praticien_id: sAnesth_id });
    });
  }

  refreshAnesthPerops = function(operation_id){
    var url = new Url("salleOp", "httpreq_vw_anesth_perop");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("list_perops_"+operation_id);
  }

  refreshFicheAnesth = function() {
    var url = new Url("cabinet", "print_fiche");

    {{if $consult_anesth->_id}}
      url.addParam("dossier_anesth_id", "{{$consult_anesth->_id}}");
    {{else}}
      url.addParam("operation_id", "{{$selOp->_id}}");
    {{/if}}

    url.addParam("offline", false);
    url.addParam("display", true);
    url.addParam("pdf"    , 0);
    url.requestUpdate("fiche_anesth");
  }

  printIntervAnesth = function(){
    var url = new Url("salleOp", "print_intervention_anesth");
    url.addParam("operation_id", "{{$selOp->_id}}");
    url.popup(800, 600, "Intervention anesth�siste");
  }

  refreshVisite = function(operation_id) {
    var url = new Url("salleOp", "ajax_refresh_visite_pre_anesth");
    url.addParam("operation_id", operation_id);
    url.addParam("callback", "refreshVisite");
    url.requestUpdate("visite_pre_anesth");
  }

  refreshFormsPerop = function(){
    ExObject.loadExObjects("{{$selOp->_class}}", "{{$selOp->_id}}", "forms_perop", 0);
  }

  Main.add(function(){
    if ($('anesth_tab_group')){
      Control.Tabs.create('anesth_tab_group');
    }

    // Refresh tab perop
    if($("tab_perop").visible()){
      refreshAnesthPerops('{{$selOp->_id}}');
    }

    {{if "dPprescription"|module_active}}
      if($('perop').visible()){
        Prescription.updatePerop('{{$selOp->sejour_id}}');
      }
    {{/if}}
  });
</script>

{{if $dialog}}
  {{assign var=onSubmit value="return onSubmitFormAjax(this, {onComplete: reloadAnesth})"}}
{{else}}
  {{assign var=onSubmit value="return checkForm(this)"}}
{{/if}}

<ul id="anesth_tab_group" class="control_tabs small">
  <li><a href="#anesth">Info. Anesth�sie</a>
  <li onmousedown="refreshAnesthPerops('{{$selOp->_id}}');"><a href="#tab_perop">Evenements per-op�ratoires</a></li>
  <li onmousedown="if(window.Prescription){ Prescription.updatePerop('{{$selOp->sejour_id}}'); }"><a href="#perop">Administrations per-op�ratoires</a></li>
  <li onmousedown="refreshFicheAnesth();"><a href="#fiche_anesth" {{if !$consult_anesth->_id}}class="wrong"{{/if}}>Fiche d'anesth�sie</a></li>
  <li><a href="#tab_preanesth" {{if !$selOp->date_visite_anesth}}class="wrong"{{/if}}>Pr�-anesth�sie</a></li>
  <li><a href="#document_anesth">Documents</a></li>
  {{if "forms"|module_installed}}
    <li onmousedown="refreshFormsPerop()"><a href="#forms_perop">Formulaires</a></li>
  {{/if}}
</ul>

<div id="anesth">
  {{mb_include module=salleOp template=inc_vw_anesth}}
</div>

<div id="fiche_anesth"></div>

<div id="document_anesth">
  {{if $consult_anesth->_id}}
  <table class="form">
    <tr>
      <td class="halfPane">
        <fieldset>
          {{mb_script module="files" script="file"}}
          <legend>{{tr}}CFile{{/tr}} - {{tr}}CConsultAnesth{{/tr}}</legend>
          <div id="files-anesth">
            <script>
              File.register('{{$consult_anesth->consultation_id}}','CConsultation', 'files-anesth');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        {{mb_script module="compteRendu" script="document"}}
        {{mb_script module="compteRendu" script="modele_selector"}}
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultAnesth{{/tr}}</legend>
          <div id="documents-anesth">
            <script type="text/javascript">
              Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class}}','{{$modeles_prat_id}}','documents-anesth');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  </table>
  {{else}}
  <div class="small-info">L'intervention n'est pas reli�e � une consultation d'anesth�sie, vous ne pouvez donc pas cr�er de documents</div>
  {{/if}}

  <div style="text-align: center;">
    {{if 'lifeline'|module_active}}
      {{mb_include module=lifeline template=inc_get_lifeline_record patient_id=$selOp->_ref_patient->_id}}
    {{/if}}
  </div>
</div>

<div id="tab_preanesth" style="display: none;">
  {{mb_include module=salleOp template=inc_vw_visite_pre_anesth}}
</div>

<div id="tab_perop" style="display: none;">
  <table class="form">
    <tr>
      <th class="title" colspan="3">Per-operatoire</th>
    </tr>
    <tr>
      <th class="category">Evenements</th>
      <th class="category">Incidents</th>
      <th class="category"></th>
    </tr>
    <tr>
      <td style="width: 30%">
        {{mb_include module="salleOp" template="inc_form_evenement_perop"}}
      </td>
      <td style="width: 30%">
        {{mb_include module="salleOp" template="inc_form_evenement_perop" incident=1}}
      </td>
      <td id="list_perops_{{$selOp->_id}}">
      </td>
    </tr>
  </table>
</div>

{{if "forms"|module_installed}}
  <div id="forms_perop"></div>
{{/if}}

<div id="perop"></div>