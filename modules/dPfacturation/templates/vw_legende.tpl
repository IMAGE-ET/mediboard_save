<table class="tbl">
  <tr>
    <th colspan="2">Liste des factures</th>
  </tr>
  {{if $classe == "CFactureEtablissement"}}
    <tr>
      <td style="background-color:#ffcd75;width:40px;"></td>
      <td class="text">Facture non cot�es</td>
    </tr>
  {{/if}}
  {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
    <tr>
      <td style="background-color:#fcc;width:40px;"></td>
      <td class="text">Facture non clotur�e</td>
    </tr>
  {{/if}}
  <tr>
    <td style="background-color:#cfc;width:40px;"></td>
    <td class="text">Facture r�gl�e</td>
  </tr>
  <tr>
    <td class="hatching"></td>
    <td class="text">Facture annul�e</td>
  </tr>
</table>