{{if $suppr}}
  <div class="small-success">{{$suppr}} sejours supprim�s</div>
{{/if}}
{{if $error}}
  <div class="small-error">{{$error}} sejours non supprim�s</div>
{{/if}}
<div class="small-info">{{$nb_sejours}} sejours dans la base</div>

<div class="big-info">
  {{$resultsMsg|smarty:nodefaults}}
</div>