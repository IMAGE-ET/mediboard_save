{{foreach from=$nodes item=_node}}
  {{$_node|smarty:nodefaults}}
{{foreachelse}}
  <div class="small-info">Aucun r�sultat</div>
{{/foreach}}
