<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("selection").date, null, {noView: true});
});
showLegend = function() {
  new Url("dPbloc", "legende").popup(500, 150, "L�gende");
}
</script>

<form action="?" name="selection" method="get">
	<input type="hidden" name="m" value="{{$m}}" />
	<input type="hidden" name="op" value="0" />
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">
	      {{$date|date_format:$conf.longdate}}
	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	    </th>
	  </tr>
	  <tr>
	    <th>
	      <label for="salle" title="Salle d'op�ration">Salle</label><br />
	      <button type="button" onclick="showLegend()" class="search" style="float: left;">L�gende</button>
	    </th>
	    <td>
	      <select name="salle" onchange="this.form.submit()">
	        <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
	        {{foreach from=$listBlocs item=curr_bloc}}
	        <optgroup label="{{$curr_bloc->nom}}">
	          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
	          <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $salle->_id}}selected="selected"{{/if}}>
	            {{$curr_salle->nom}}
	          </option>
	          {{foreachelse}}
	          <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
	          {{/foreach}}
	        </optgroup>
	        {{/foreach}}
	      </select><br />
	      <input type="hidden" name="hide_finished" value="{{$hide_finished}}" onchange="this.form.submit()" />
	      <label>
	        <input type="checkbox" name="_hide_finished" {{if $hide_finished}}checked="checked"{{/if}} onclick="$V(this.form.hide_finished, this.checked ? 1 : 0)" />
	        Cacher les interv. termin�es 
	      </label>
	    </td>
	  </tr>
	</table>
</form>
      
{{include file="inc_details_plages.tpl"}}