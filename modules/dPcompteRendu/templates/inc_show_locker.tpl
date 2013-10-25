<table class="form">
  {{if $compte_rendu->valide || $auto_lock}}
    <tr>
      <th class="title" colspan="2">Verrouill�</th>
    </tr>
    {{if $compte_rendu->valide}}
      <tr>
        <th>Par</th>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser}}</td>
      </tr>
    {{/if}}
    {{if $auto_lock}}
      <tr>
        <td colspan="2">
          Document verrouill� automatiquement
          (veuillez contacter un administrateur pour connaitre la configuration du verrouillage automatique)
        </td>
      </tr>
    {{/if}}
  {{else}}
    <tr>
      <th class="title" colspan="2">Document non verrouill�</th>
    </tr>
  {{/if}}
</table>
