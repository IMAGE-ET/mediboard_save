    <select name="sejour_id" onchange="reloadSejour()">
      <option value="" selected="selected">
        &mdash; Selectionner un s�jour existant
      </option>
      {{foreach from=$sejours item=curr_sejour}}
      <option value="{{$curr_sejour->sejour_id}}">
        {{$curr_sejour->_view}}
      </option>
      {{/foreach}}
    </select>