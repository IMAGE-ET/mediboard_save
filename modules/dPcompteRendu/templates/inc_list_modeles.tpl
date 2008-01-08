<form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_pack_aed" />
  {{mb_field object=$pack field="pack_id" hidden=1 prop=""}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="modeles" value="{{$pack->modeles|smarty:nodefaults|JSAttribute}}" />
  <label for="_new" title="Veuillez choisir un mod�le" />
  <select name="_new" class="notNull ref">
    <option value="">&mdash; Choisir un mod�le</option>
    <optgroup label="Mod�les du praticien">
      {{foreach from=$listModelePrat item=curr_modele}}
      <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
    <optgroup label="Mod�les du cabinet">
      {{foreach from=$listModeleFunc item=curr_modele}}
      <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
  </select>
  <button type="submit" class="tick notext">{{tr}}Select{{/tr}}</button>
</form>