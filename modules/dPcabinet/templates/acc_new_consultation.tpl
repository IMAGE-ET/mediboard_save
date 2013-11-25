{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var="chir_id"        value=$consult->_ref_plageconsult->chir_id}}
{{assign var="module"         value="dPcabinet"}}
{{assign var="sejour_id"      value=$consult->sejour_id}}
{{assign var="rpu"            value=""}}
{{assign var="mutation_id"    value=""}}
{{assign var="object"         value=$consult}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}

{{if $consult->sejour_id && $consult->_ref_sejour && $consult->_ref_sejour->_ref_rpu && $consult->_ref_sejour->_ref_rpu->_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  {{assign var="mutation_id" value=$rpu->mutation_sejour_id}}
  {{if $mutation_id == $consult->sejour_id}}
    {{assign var="mutation_id" value=""}}
  {{/if}}
{{/if}}

{{mb_include module=salleOp template=js_codage_ccam}}
{{mb_script module=medicament script=equivalent_selector}}
{{mb_script module=soins script=plan_soins}}
{{mb_script module=planningOp script=cim10_selector}}
<script>
  function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
    if (!sejour_id) {
      return;
    }
    var urlSuivi = new Url("hospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs)) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans)) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const)) {
      urlSuivi.addParam("_show_const", show_const);
    }
    urlSuivi.requestUpdate("dossier_suivi");
  }

  function submitSuivi(oForm) {
    var sejour_id = oForm.sejour_id.value;
    onSubmitFormAjax(oForm, loadSuivi.curry(sejour_id));
  }

  var constantesMedicalesDrawn = false;
  function refreshConstantesMedicales (force) {
    if (!constantesMedicalesDrawn || force) {
      var url = new Url("hospi", "httpreq_vw_constantes_medicales");
      url.addParam("patient_id", {{$consult->_ref_patient->_id}});
      url.addParam("context_guid", "{{$consult->_guid}}");
      url.requestUpdate("Constantes");
      constantesMedicalesDrawn = true;
    }
  }

  function reloadPrescription(prescription_id){
    Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'', null, false);
  }

  function loadResultLabo() {
    var url = new Url("Imeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", '{{$consult->sejour_id}}');
    url.requestUpdate('Imeds');
  }

  function loadAntTrait() {
    var url = new Url("cabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id", "{{$consult->sejour_id}}");
    url.addParam("patient_id", "{{$consult->patient_id}}");
    url.addParam("show_header", 0);
    url.requestUpdate("AntTrait");
  }

  function loadActes() {
    var url = new Url("cabinet", "ajax_vw_actes");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("Actes");
  }

  function loadRPU() {
    var url = new Url("dPurgences", "ajax_vw_rpu");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("rpuConsult");
  }

  Main.add(function() {
    tabsConsult = Control.Tabs.create('tab-consult', false);
    {{if $rpu}}
      loadRPU();
    {{else}}
      loadAntTrait();
    {{/if}}
  });
</script>

<ul id="tab-consult" class="control_tabs">
  {{if $rpu}}
  <li>
    <a href="#rpuConsult">
      RPU
      {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$consult->_ref_sejour}}
    </a>
  </li>
  {{/if}}

  <li {{if $rpu}}onmousedown="this.onmousedown = ''; loadAntTrait()" {{/if}}>
    <a id="acc_consultation_a_Atcd" href="#AntTrait">Ant�c�dents</a>
  </li>

  {{if "dPprescription"|module_active &&
       $consult->sejour_id          &&
       $modules.dPprescription->_can->read &&
       !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
  <li {{if !$mutation_id}}onmousedown="Prescription.reloadPrescSejour('', '{{$consult->sejour_id}}','', '', null, null, null,'', null, false);"{{/if}}>
    <a href="#prescription_sejour">
      Prescription
    </a>
  </li>
  <li {{if !$mutation_id}}onmousedown="PlanSoins.loadTraitement('{{$consult->sejour_id}}',null,'','administration');"{{/if}}>
    <a href="#dossier_traitement">
      Suivi de soins
    </a>
  </li>
  {{elseif $rpu}}
  <li {{if !$mutation_id}}onmousedown="loadSuivi('{{$rpu->sejour_id}}')"{{/if}}>
    <a href="#dossier_suivi">Suivi de soins</a>
  </li>
  {{/if}}

  <li onmousedown="refreshConstantesMedicales();">
    <a href="#Constantes">Constantes</a>
  </li>
  <li>
    <a href="#Examens">Examens</a>
  </li>

  {{if @$modules.dPImeds->mod_active && $consult->sejour_id}}
    <li onmousedown="this.onmousedown = ''; loadResultLabo();">
      <a href="#Imeds">Labo</a>
    </li>
  {{/if}}

  {{if $app->user_prefs.ccam_consultation == 1}}
    <li onmousedown="this.onmousedown = ''; loadActes()">
      <a id="acc_consultation_a_Actes" href="#Actes">{{tr}}CCodable-actes{{/tr}}</a>
    </li>
  {{/if}}

  {{if $consult->_is_dentiste}}
    <li>
      <a href="#etat_dentaire">Etat dentaire</a>
    </li>
    <li>
      <a href="#devenir_dentaire">Projet th�rapeutique</a>
    </li>
  {{/if}}

  <li>
    <a href="#fdrConsult">Documents</a>
  </li>
  <li onmousedown="Reglement.reload(true);">
    <a id="a_reglements_consult" href="#reglement">R�glements</a>
  </li>
</ul>

{{if $consult->sejour_id}}
  <div id="rpuConsult" style="display: none;"></div>

  {{if "dPprescription"|module_active &&
       $consult->sejour_id            &&
       $modules.dPprescription->_can->read &&
       !"dPprescription CPrescription prescription_suivi_soins"|conf:"CGroups-$g"}}
    <div id="prescription_sejour" style="display: none;">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a �t� hospitalis�, veuillez vous r�f�rer au dossier de soin de son s�jour.
        </div>
      {{/if}}
    </div>

    <div id="dossier_traitement" style="display: none;">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a �t� hospitalis�, veuillez vous r�f�rer au dossier de soin de son s�jour.
        </div>
      {{/if}}
    </div>

  {{elseif $rpu}}
    <div id="dossier_suivi" style="display:none">
      {{if $mutation_id}}
        <div class="small-info">
          Ce patient a �t� hospitalis�, veuillez vous r�f�rer au dossier de soin de son s�jour.
        </div>
      {{/if}}
    </div>
  {{/if}}
{{/if}}

<div id="AntTrait" style="display: none;"></div>

<div id="Constantes" style="display: none"></div>

<div id="Examens" style="display: none;">
  {{mb_include module=cabinet template=inc_main_consultform}}
</div>

{{if "dPImeds"|module_active && $consult->sejour_id}}
  <div id="Imeds" style="display: none;">
    <div class="small-info">
      Veuillez s�lectionner un s�jour dans la liste de gauche pour pouvoir
      consulter les r�sultats de laboratoire disponibles pour le patient concern�.
    </div>
  </div>
{{/if}}

{{if $app->user_prefs.ccam_consultation == 1}}
  <div id="Actes" style="display: none;">
    {{if $mutation_id}}
      <div class="small-info">
        Ce patient a �t� hospitalis�, veuillez vous r�f�rer au dossier de soin de son s�jour.
      </div>
    {{/if}}
  </div>
{{/if}}

{{if $consult->_is_dentiste}}
  <div id="etat_dentaire">
    {{mb_include module=cabinet template="inc_consult_anesth/intubation"}}
  </div>
  <div id="devenir_dentaire">
    {{mb_include module=cabinet template="inc_devenir_dentaire"}}
  </div>
{{/if}}

<div id="fdrConsult" style="display: none;">
  {{mb_include module=cabinet template=inc_fdr_consult}}
</div>

<!-- Reglement -->
{{mb_script module="cabinet" script="reglement"}}
<script>
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register(false);
</script>
