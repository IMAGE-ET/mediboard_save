<select name="sejour_id">
  <option value="0">$mdash Selectionner un s�jour existant</option>
  {{foreach from=$sejours value=curr_sejour}}
  <option value="{{$curr_sejour->sejour_id}}">{{$curr_sejour->_view}}</option>
  {{/foreach}}
</select>