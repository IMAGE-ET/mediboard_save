<ul>
{{foreach from=$matches item=match}}
  <li>{{$match->$field|replace:$input:"<em>$input</em>"}}</li>
{{foreachelse}}
  <li><span class="informal">Aucun r�sultat</span></li>
{{/foreach}}
</ul>