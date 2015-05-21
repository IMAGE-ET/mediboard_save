{{if "dPfacturation"|module_active && $conf.dPplanningOp.CFactureEtablissement.use_facture_etab}}
  {{mb_script module=facturation script=rapport ajax=true}}
  {{mb_script module=cabinet script=reglement ajax=true}}
{{/if}}
<script>
function submitActeCCAM(oForm, acte_ccam_id, sField){
  if(oForm[sField].value == 1) {
    $V(oForm[sField], 0);
  } else {
    $V(oForm[sField], 1);
  }
  $(sField + '-' + acte_ccam_id).toggleClassName('cancel').toggleClassName('tick');
  return onSubmitFormAjax(oForm, {onComplete: function() { reloadActeCCAM(acte_ccam_id) } });
}

function reloadActeCCAM(acte_ccam_id) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_reglement_ccam");
  url.addParam("acte_ccam_id", acte_ccam_id);
  url.requestUpdate('divreglement-'+acte_ccam_id);
}

function viewCCAM(codeacte) {
  var url = new Url;
  url.setModuleAction("dPccam", "vw_full_code");
  url.addParam("_codes_ccam", codeacte);
  url.popup(800, 600, "Code CCAM");
}
function viewTarmed(codeacte) {
  var url = new Url;
  url.setModuleAction("tarmed", "vw_tarmed");
  url.addParam("code_tarmed", codeacte);
  url.addParam("dialog", 1);
  url.popup(800, 600, "Code Tarmed");
}
</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a href="#" onclick="window.print()">
        Rapport des actes cod�s
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="main">
        {{if $bloc->_id}}
          <tr>
            <td><strong>{{tr}}CBlocOperatoire{{/tr}}: {{$bloc}}</strong></td>
          </tr>
        {{/if}}
        <tr>
          <td>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
          </td>
        </tr>
        <tr>
          <td>du {{$_date_min|date_format:$conf.longdate}}</td>
        </tr>
        <tr>
          <td>au {{$_date_max|date_format:$conf.longdate}}</td>
        </tr>
      </table>
    </td>
    <td style="width: 50%;">
      <table class="main tbl" style="float: right;">
        <tr>
          <th>Nombre de s�jours</th>
          <td style="text-align: center;">{{$nbActes|@count}}</td>
        </tr>
        <tr>
          <th>Nombre d'actes</th>
          <td style="text-align: center;">{{$totalActes}}</td>
        </tr>
        <tr>
          <th>Total Base</th>
          <td style="text-align: right;">{{$montantTotalActes.base|currency}}</td>
        </tr>
        <tr>
          <th>Total DH</th>
          <td style="text-align: right;">{{$montantTotalActes.dh|currency}}</td>
        </tr>
        <tr>
          <th>Total</th>
          <td style="text-align: right;">{{$montantTotalActes.total|currency}}</td>
        </tr>
      </table>
    </td>
  </tr>

  {{if $typeVue == 1}}
    {{foreach from=$sejours key="key" item="jour"}}
      <tr>
        <td colspan="2">
          <table>
            <tr>
              <td>
                <strong>Sortie r�elle le {{$key|date_format:$conf.longdate}}</strong>
              </td>
            </tr>
          </table>
          <table class="tbl">
            <tr>
              <th style="width: 20%">{{mb_title class=CFactureEtablissement field=patient_id}}</th>
              <th style="width: 05%">Total S�jour</th>
              <th style="width: 20%">{{mb_title class=CActeCCAM field=object_class}}</th>
              <th style="width: 05%">{{mb_title class=CActeCCAM field=code_acte}}</th>
              <th style="width: 05%">Act.</th>
              <th style="width: 05%">{{mb_label class=CActeCCAM field=code_phase}}</th>
              <th style="width: 05%">Mod</th>
              <th style="width: 05%">ANP</th>
              <th style="width: 05%">{{mb_title class=CActeCCAM field=montant_base}}</th>
              <th style="width: 05%">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
              <th style="width: 05%">{{mb_title class=CActeCCAM field=_montant_facture}}</th>
              {{if "dPfacturation"|module_active && $conf.dPplanningOp.CFactureEtablissement.use_facture_etab}}
                <th style="width: 05%">D� �tablissement</th>
              {{/if}}
            </tr>

            <!-- Parcours des sejours -->
            {{foreach from=$jour item="sejour"}}
            {{assign var="sejour_id" value=$sejour->_id}}
            {{assign var=facture value=$sejour->_ref_last_facture}}
            <tbody class="hoverable" {{if $facture && $facture->_id}}id="line_{{$facture->_guid}}"{{/if}}>
            <tr>
              <td rowspan="{{$nbActes.$sejour_id}}">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_ref_patient->_guid}}')">
                {{$sejour->_ref_patient->_view}} {{if $sejour->_ref_patient->_age}}({{$sejour->_ref_patient->_age}}){{/if}}
              </span>
              </td>
              <td rowspan="{{$nbActes.$sejour_id}}">
                {{$montantSejour.$sejour_id|currency}}
              </td>
              <td class="text" rowspan="{{$nbActes.$sejour_id}}">
                {{if $sejour->_ref_actes|@count}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                  Sejour du {{mb_value object=$sejour field=_entree}}
                    au {{mb_value object=$sejour field=_sortie}}
                  </span>
                {{/if}}
                {{foreach from=$sejour->_ref_operations item=operation}}
                  {{if $operation->_ref_actes|@count}}
                    <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}')">
                      <br/>Intervention du {{mb_value object=$operation field=_datetime_best}}
                      {{if $operation->libelle}}<br /> {{$operation->libelle}}{{/if}}
                    </span>
                  {{/if}}
                {{/foreach}}
                {{foreach from=$sejour->_ref_consultations item=consult}}
                  {{if $consult->_ref_actes|@count}}
                    <span onmouseover="ObjectTooltip.createEx(this, '{{$consult->_guid}}')">
                      <br/>Consultation du {{$consult->_datetime|date_format:"%d %B %Y"}}
                      {{if $consult->motif}}: {{$consult->motif}}{{/if}}
                    </span>
                  {{/if}}
                {{/foreach}}
              </td>

              {{mb_include module=dPplanningOp template=inc_acte_realise codable=$sejour}}
              {{if $sejour->_ref_operations}}
                {{foreach from=$sejour->_ref_operations item=operation}}
                  {{mb_include module=dPplanningOp template=inc_acte_realise codable=$operation}}
                {{/foreach}}
              {{/if}}

              {{if $sejour->_ref_consultations}}
                {{foreach from=$sejour->_ref_consultations item=consult}}
                  {{mb_include module=dPplanningOp template=inc_acte_realise codable=$consult}}
                {{/foreach}}
              {{/if}}
            </tr>
            </tbody>
            {{/foreach}}
          </table>
        </td>
      </tr>
    {{/foreach}}
  {{/if}}
</table>