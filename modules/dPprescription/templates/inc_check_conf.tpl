{{if $etat == "vide" || $etat == "ko"}}
  <div class='small-warning'>
  {{if $etat == "vide"}}Configurations absentes de la m�moire partag�.{{/if}}
  {{if $etat == "ko"}}Configurations pas � jour.{{/if}}
    <button class="tick" type="button" onclick="checkSHM('{{$name}}','create');">Mettre � jour</button>
  </div>
{{/if}}

{{if $etat == "ok"}}
<div class='small-info'>Configurations � jour.</div>
{{/if}}