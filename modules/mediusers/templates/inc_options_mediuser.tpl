{{mb_default var=selected value=null}}
{{mb_default var=disabled value=null}}

{{foreach from=$list item=_mediuser}}
  <option class="mediuser" 
          style="border-color: #{{$_mediuser->_color}};"
          value="{{$_mediuser->_id}}" 
          {{if $selected == $_mediuser->_id}} selected="selected" {{/if}}
          {{if $disabled == $_mediuser->_id}} disabled="disabled" {{/if}}
	>
    {{$_mediuser}}
  </option>
{{foreachelse}}
  {{if @$showEmptyList}}
  <option disabled="disabled">{{tr}}CMediuser.none{{/tr}}</option>
  {{/if}}
{{/foreach}}