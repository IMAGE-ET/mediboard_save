
<table class="main tbl">
  <col class="narrow" />
  
  <tr>
    <th>Valeur</th>
    <th>Formulaire � d�clencher</th>
    <th class="narrow">
      Coch� par<br />d�faut
    </th>
  </tr>
  
  <tbody>
  {{foreach from=","|explode:"1,0" item=_value}}
    <tr>
      <td>{{tr}}bool.{{$_value}}{{/tr}}</td>
      
      {{if $triggerables|@count}}
        <td>
          <select class="triggered-data-select" onchange="updateTriggerData($V(this), '{{$_value}}')" style="max-width: 20em;">
            <option value=""> &mdash; </option>
            {{foreach from=$triggerables item=_triggerable}}
              {{assign var=_trigger_value value=$_triggerable->_id}}
              <option value="{{$_trigger_value}}" {{if array_key_exists($_value, $context->_triggered_data) && $context->_triggered_data.$_value == $_trigger_value}}selected="selected"{{/if}}>
                {{$_triggerable->name}}
              </option>
            {{/foreach}}
          </select>
        </td>
      {{else}}
        <td class="empty">Aucun formulaire � d�clencher</td>
      {{/if}}
			
			<td style="text-align: center;">
				<label style="display: block;">
					<input type="radio" name="default" value="{{$_value}}" {{if $spec->default == $_value}}checked="checked"{{/if}} />
				</label>
			</td>
    </tr>
  {{/foreach}}
	<tr>
		<td colspan="2">{{tr}}Undefined{{/tr}}</td>
		<td style="text-align: center;">
      <label style="display: block;">
        <input type="radio" name="default" value="" {{if $spec->default == ""}}checked="checked"{{/if}} />
			</label>
		</td>
	</tr>
  </tbody>
</table>

