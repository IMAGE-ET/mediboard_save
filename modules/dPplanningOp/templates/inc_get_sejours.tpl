<script type="text/javascript">

Main.add( function(){
  var oForm = document.editOp;
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  Sejour.preselectSejour(oForm._date.value);
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