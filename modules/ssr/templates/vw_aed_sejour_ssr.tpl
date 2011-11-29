{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPpatients" script="pat_selector"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPplanningOp" script="protocole_selector"}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="ssr" script="cotation_rhs"}}
{{mb_script module="dPcabinet" script="file"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}

{{mb_include module=ssr template=inc_form_sejour_ssr}}

{{if $sejour->_id && $can->read}}
<script type="text/javascript">
  
Main.add(function() {
  var tabs = Control.Tabs.create('tab-sejour', true);
  (tabs.activeLink.onmousedown || Prototype.emptyFunction)();
});

var constantesMedicalesDrawn = false;
refreshConstantesMedicales = function (force) {
  if (!constantesMedicalesDrawn || force) {
    var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("patient_id", {{$sejour->_ref_patient->_id}});
    url.addParam("context_guid", "{{$sejour->_guid}}");
    url.addParam("selection[]", ["poids", "taille"]);
    url.requestUpdate("constantes");
    constantesMedicalesDrawn = true;
  }
};

refreshSejoursSSR = function(sejour_id){
  var url = new Url("ssr", "ajax_vw_sejours_patient");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("sejours_ssr");
}

loadDocuments = function(sejour_id) {
  var url = new Url("dPhospi", "httpreq_documents_sejour");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate("docs");
}

</script>

<ul id="tab-sejour" class="control_tabs">
  {{if $can_view_dossier_medical}}
  <li>
    <a href="#autonomie">
      {{tr}}CFicheAutonomie{{/tr}}
    </a>
  </li>
  
    {{if !$sejour->annule}}
    <li>
      <a href="#constantes" onmousedown="refreshConstantesMedicales();">
        {{tr}}CPatient.surveillance{{/tr}}
      </a>
    </li>

    <li style="display: none;">
      <a href="#antecedents">
        {{tr}}CAntecedent{{/tr}}s &amp; {{tr}}CTraitement{{/tr}}s
      </a>
    </li>  

    <li>
      <a href="#bilan" onmousedown="refreshSejoursSSR('{{$sejour->_id}}');">
        {{tr}}CPrescription{{/tr}} &amp; {{tr}}CBilanSSR{{/tr}}
      </a>
    </li>

    <li>
      <a {{if $bilan->_id && !$bilan->planification}} class="empty" {{/if}}
        href="#planification" onmousedown="Planification.refresh('{{$sejour->_id}}')">
        Planification
      </a>
    </li>
    <li>
      <a {{if $bilan->_id && !$bilan->planification}} class="empty" {{/if}}
        href="#cotation-rhs" onmousedown="CotationRHS.refresh('{{$sejour->_id}}')">
        Cotation
      </a>
    </li>
    <li>
      <a href="#docs" onmousedown="loadDocuments('{{$sejour->_id}}')"$>
        Documents
      </a>
    </li>
    {{/if}}

  {{/if}}
</ul>

<hr class="control_tabs" />  

<div id="autonomie" style="display: none;">
  {{mb_include template=inc_form_fiche_autonomie}}
</div>

{{if $can_view_dossier_medical}}
<script type="text/javascript">
function loadAntecedents(sejour_id){
  var url = new Url("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('antecedents')
}

// Main.add(loadAntecedents.curry({{$sejour->_id}}));
</script>

<div id="antecedents" style="display: none;">
</div>
{{/if}}

<div id="bilan" style="display: none;">
  {{mb_include template=inc_form_bilan_ssr}}
</div>

<div id="planification" style="display: none;">
  {{mb_include template=inc_planification}}
</div>

<div id="cotation-rhs" style="display: none;">
</div>

<div id="constantes" style="display: none;">
</div>

<div id="docs" style="display: none;">
</div>
{{/if}}