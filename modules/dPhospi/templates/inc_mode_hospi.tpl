<td>
  <form name="chgMode" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <label for="mode" title="Veuillez choisir un type de vue">Type de vue</label>
  <select name="mode" onchange="submit()">
    <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>Vue instantan�e</option>
    <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>Vue de la journ�e</option>
  </select>
  </form>
</td>
