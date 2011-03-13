{{mb_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">

Main.add(function () {
  Control.Tabs.create('tab_edit_mediuser', true).activeLink.onmouseup();
});
</script>

<ul id="tab_edit_mediuser" class="control_tabs">
  <li>
  	<a href="#edit-mediuser" onmouseup="">
  		{{tr}}Account{{/tr}}
		</a>
	</li>
		
  <li>
		<script type="text/javascript">
		Preferences.refresh = function () {
		  var url = new Url("admin", "edit_prefs");
		  url.addParam("user_id", "{{$user->_id}}");
		  url.requestUpdate("edit-preferences");
		};
		</script>
  	<a href="#edit-preferences" onmouseup="Preferences.refresh()">
  		{{tr}}Preferences{{/tr}}
		</a>
	</li>
	
	{{if @$modules.dPpersonnel->_can->read}}
  <li>
    {{mb_script module="dPpersonnel" script="plage"}}
    <script type="text/javascript">
    	PlageConge.refresh = function() {
	      PlageConge.content();
	      PlageConge.loadUser('{{$user->_id}}', '');
	      PlageConge.edit('','{{$user->_id}}');
			}
	  </script>
  	<a href="#edit-holidays" onmouseup="PlageConge.refresh()">
  		{{tr}}Holidays{{/tr}}
		</a>
	</li>
	{{/if}}
	
</ul>

<hr class="control_tabs" />

<div id="edit-mediuser" style="display: block;">
<table class="main">
  <tr>
    <td class="halfPane">
      {{mb_include template=inc_info_mediuser}}
      {{mb_include template=inc_info_exchange_source}}

    </td>
    
    <td class="halfPane">
      {{mb_include template=inc_info_function}}
    </td>
  </tr>
</table>
</div>

<div id="edit-preferences" style="display: none;">
</div>

{{if @$modules.dPpersonnel->_can->read}}
<div id="edit-holidays" style="display: none;">
	<table class="main">
	  <tr>
	    <td class="halfPane" id = "vw_user">
	    </td> 
	    <td class="halfPane" id = "edit_plage">
	    </td>
	  </tr>
	  <tr>
	    <td colspan="2" id="planningconge"></td>
	  </tr>
	</table>
</div>
{{/if}}