{{if $suppr}}
  <div class="small-success">{{$suppr}} patients supprim�s</div>
{{/if}}
{{if $error}}
  <div class="small-error">{{$error}} patients non supprim�s</div>
{{/if}}
<div class="small-info">{{$nb_patients}} patients dans la base</div>