<script type="text/javascript">

Main.add( function(){
  sejour_collision = {{$sejour_collision|@json}};
  var oForm = document.editOp;
  preselectSejour(oForm._date.value, sejour_collision);
} );

</script>

<select name="sejour_id" onchange="reloadSejour();">
  <option value="" selected="selected">
    &mdash; Selectionner un s�jour existant
  </option>
  {{foreach from=$sejours item=curr_sejour}}
  <option value="{{$curr_sejour->sejour_id}}">
    {{$curr_sejour->_view}}
  </option>
  {{/foreach}}
</select>