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

{{assign var="chir_id"        value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{assign var="module"         value="cabinet"}}
{{assign var="object"         value=$consult}}
{{mb_include module=salleOp template=js_codage_ccam}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="element_selector"}}
  {{mb_script module="prescription" script="prescription"}}
{{/if}}

{{mb_script module="cabinet" script="reglement"}}

<script>
  {{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
    function reloadPrescription(prescription_id) {
      Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'', null, false);
    }
  {{/if}}

  var constantesMedicalesDrawn = false;
  function refreshConstantesMedicales(force) {
    if (!constantesMedicalesDrawn || force) {
      var url = new Url("hospi", "httpreq_vw_constantes_medicales");
      url.addParam("patient_id", {{$consult->_ref_patient->_id}});
      url.addParam("context_guid", "{{$consult->_guid}}");
      url.requestUpdate("Constantes");
      constantesMedicalesDrawn = true;
    }
  }

  function loadAntTrait() {
    var url = new Url("cabinet", "httpreq_vw_antecedents");
    url.addParam("sejour_id", "{{$consult->sejour_id}}");
    url.addParam("patient_id", "{{$consult->patient_id}}");
    url.addParam("show_header", 0);
    url.requestUpdate("AntTrait");
  }

  function refreshFacteursRisque() {
    var url = new Url("cabinet", "httpreq_vw_facteurs_risque");
    url.addParam("consultation_id", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
    url.requestUpdate("facteursRisque");
  }

  function loadActes() {
    var url = new Url("cabinet", "ajax_vw_actes");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("Actes");
  }

  function loadIntervention() {
    var consultUrl = new Url("cabinet", "httpreq_vw_consult_anesth");
    consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
    consultUrl.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
    consultUrl.requestUpdate('consultAnesth');
  }

  function loadInfosAnesth() {
    var infosAnesthUrl = new Url("cabinet", "httpreq_vw_choix_anesth");
    infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
    infosAnesthUrl.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
    infosAnesthUrl.requestUpdate('InfoAnesth');
  }

  Main.add(function () {
    tabsConsultAnesth = Control.Tabs.create('tab-consult-anesth', false);
    loadAntTrait();
    loadIntervention();
  });
</script>

<!-- Tab titles -->
<ul id="tab-consult-anesth" class="control_tabs">
  <li><a id="acc_consultation_a_Atcd" href="#AntTrait">Ant�c�dents</a></li>
  <li onmousedown="refreshConstantesMedicales();"><a href="#Constantes">Constantes</a></li>
  <li><a href="#Exams">Exam. Clinique</a></li>
  <li><a href="#Intub">Intubation</a></li>
  <li><a href="#ExamsComp">Exam. Comp.</a></li>
  <li onmousedown="this.onmousedown = ''; loadInfosAnesth()">
    <a href="#InfoAnesth">Infos. Anesth.</a>
  </li>
  {{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
    <li onmousedown="this.onmousedown = ''; Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);">
      <a href="#prescription_sejour">Prescription</a>
    </li>
  {{/if}}
  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <li onmousedown="refreshFacteursRisque();"><a href="#facteursRisque">Facteurs de risque</a></li>
  {{/if}}
  {{if $app->user_prefs.ccam_consultation == 1}}
    <li onmousedown="this.onmousedown = ''; loadActes()">
      <a id="acc_consultation_a_Actes" href="#Actes">{{tr}}CCodable-actes{{/tr}}</a>
    </li>
  {{/if}}
  <li><a href="#fdrConsult">Documents</a></li>
  <li onmousedown="Reglement.reload(true);">
    <a id="a_reglements_consult"  href="#reglement">R�glements</a>
  </li>
</ul>

<!-- Tabs -->
<div id="AntTrait" style="display: none;"></div>

<div id="Constantes" style="display: none;">
  <!-- We put a fake form for the ExamCompFrm form, before we insert the real one -->
  <form name="edit-constantes-medicales" action="?" method="post" onsubmit="return false">
    <input type="hidden" name="_last_poids" value="{{$consult->_ref_patient->_ref_constantes_medicales->poids}}" />
    <input type="hidden" name="_last__vst" value="{{$consult->_ref_patient->_ref_constantes_medicales->_vst}}" />
  </form>
</div>

<div id="Exams" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_examens_clinique}}
</div>
<div id="Intub" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/intubation}}
</div>
<div id="ExamsComp" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_examens_complementaire}}
</div>
<div id="InfoAnesth" style="display: none;"></div>

{{if $isPrescriptionInstalled && $conf.dPcabinet.CPrescription.view_prescription}}
  <div id="prescription_sejour" style="display: none"></div>
{{/if}}

{{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
  <div id="facteursRisque" style="display: none;"></div>
{{/if}}

{{if $app->user_prefs.ccam_consultation == 1}}
  <div id="Actes" style="display: none;"></div>
{{/if}}

<div id="fdrConsult" style="display: none;">
  {{mb_include module=cabinet template=inc_fdr_consult}}
</div>

<!-- Reglement -->
<script type="text/javascript">
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register(false);
</script>