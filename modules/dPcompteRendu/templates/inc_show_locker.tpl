<table class="form">
  <tr>
    <th class="title" colspan="2">Verrouill�</th>
  </tr>
  {{if $compte_rendu->valide}}
    <tr>
      <th>Par</th>
      <td>{{mb_include module=mediusers template=inc_vw_mediuser}}</td>
    </tr>
    <tr>
      <th>Le</th>
      <td>{{mb_value object=$last_log field=date}}</td>
    </tr>
  {{else}}
    <tr>
      <td colspan="2">
        Archiv� car non modifi� depuis plus de {{$days}} jours
      </td>
    </tr>
  {{/if}}
</table>
