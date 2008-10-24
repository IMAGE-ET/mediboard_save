{{if $alertesInteractions|@count}}
<table class="tbl">
  <tr>
    <th colspan="6">{{$alertesInteractions|@count}} interaction(s)</th>
  </tr>
  <tr>
    <th>Niveau</th>
    <th>Gravit�</th>
    <th>Produit</th>
    <th>Inter�agit avec</th>
    <th>M�canisme</th>
    <th>Conduite � tenir</th>
  </tr>
  {{foreach from=$alertesInteractions item=curr_alerte}}
  <tr>
    <td class="text">{{$curr_alerte->Niveau}}</td>
    <td class="text">{{$curr_alerte->Gravite}}</td>
    <td class="text">{{$curr_alerte->Nom1}} ({{$curr_alerte->strClasse1}})</td>
    <td class="text">{{$curr_alerte->Nom2}} ({{$curr_alerte->strClasse2}})</td>
    <td class="text">{{$curr_alerte->Type}} : {{$curr_alerte->Message}}</td>
    <td class="text">{{$curr_alerte->strConduite}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}
{{if $alertesProfil|@count}}
<table class="tbl">
  <tr>
    <th colspan="3">{{$alertesProfil|@count}} contre-indication(s) / pr�caution(s) d'emploi</th>
  </tr>
  <tr>
    <th>Niveau</th>
    <th>Produit</th>
    <th>CI/PE</th>
  </tr>
  {{foreach from=$alertesProfil item=curr_alerte}}
  <tr>
    <td class="text">{{$curr_alerte->Niveau}}</td>
    <td class="text">{{$curr_alerte->Libelle}}</td>
    <td class="text">{{$curr_alerte->LibelleMot}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}
{{if $alertesIPC|@count}}
<table class="tbl">
  <tr>
    <th>{{$alertesIPC|@count}} incompatibilit�s pysico-chimiques</th>
  </tr>
</table>
{{/if}}
{{if $alertesAllergies|@count}}
<table class="tbl">
  <tr>
    <th colspan="2">{{$alertesAllergies|@count}} hypersensibilit�s</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>Allergie</th>
  </tr>
  {{foreach from=$alertesAllergies item=curr_alerte}}
  <tr>
    <td class="text">{{$curr_alerte->Libelle}}</td>
    <td class="text">{{$curr_alerte->LibelleAllergie}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}