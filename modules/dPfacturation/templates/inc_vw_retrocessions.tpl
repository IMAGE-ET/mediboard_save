<script>
  function printRetrocession() {
    var url = new Url("facturation", "ajax_vw_retrocessions");
    url.addParam("print", 1);
    url.popup(900, 600, "Retrocession");
  }
</script>
{{if isset($factures|smarty:nodefaults)}}
  {{if $print}}
    <h2 style="text-align: center;">
      R�trocession pour la p�riode du {{$filter->_date_min|date_format:"%d/%m/%Y"}} au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
      {{if $prat->_id}}
        <br/> pour le praticien {{$prat->_view}}
      {{/if}}
    </h2>
  {{/if}}
  <table class="tbl">
    {{if !$print}}
      <tr>
        <th colspan="6" class="title">
          R�trocession pour la p�riode du {{$filter->_date_min|date_format:"%d/%m/%Y"}} au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        </th>
        <th class="title">
          <button type="button" class="print" onclick="printRetrocession();">Imprimer</button>
        </th>
      </tr>
    {{/if}}
    <tr>
      <th class="narrow">Facture</th>
      <th class="narrow">Date de cloture</th>
      <th class="narrow">Praticien</th>
      <th>Patient</th>
      <th>Montant total</th>
      <th>R�trocession</th>
      <th class="narrow">R�sultat</th>
    </tr>
    {{foreach from=$factures item=facture}}
      <tr>
        <td>
          {{if !$print}}
            <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_guid}}')">
            {{$facture->_view}}
            </a>
          {{else}}
            {{$facture->_view}}
          {{/if}}
        </td>
        <td style="text-align: center;">{{$facture->cloture|date_format:"%d/%m/%Y"}}</td>
        <td>
          {{if !$print}}
            <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_praticien->_guid}}')">
              {{$facture->_ref_praticien->_view}}
            </a>
          {{else}}
            {{$facture->_ref_praticien->_view}}
          {{/if}}
        </td>
        <td>
          {{if !$print}}
            <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">
              {{$facture->_ref_patient->_view}}
            </a>
          {{else}}
            {{$facture->_ref_patient->_view}}
          {{/if}}
        </td>
        <td style="text-align: right;">{{$facture->_montant_avec_remise|string_format:"%0.2f"|currency}}</td>
        <td style="text-align: right;">{{$facture->_montant_retrocession|string_format:"%0.2f"|currency}}</td>
        <td></td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="7" class="empty">{{tr}}CFactureCabinet.none{{/tr}}</td>
      </tr>
    {{/foreach}}

    {{if count($factures)}}
      <tr style="text-align: right;">
        <td colspan="4"><strong>Total</strong></td>
        <td><strong>{{$total_montant|string_format:"%0.2f"|currency}}</strong></td>
        <td><strong>{{$total_retrocession|string_format:"%0.2f"|currency}}</strong></td>
        <td><strong>{{$total_montant-$total_retrocession|string_format:"%0.2f"|currency}}</strong></td>
      </tr>
    {{/if}}
  </table>
{{/if}}