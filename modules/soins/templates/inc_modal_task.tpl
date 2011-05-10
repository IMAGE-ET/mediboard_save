<script type="text/javascript">

Main.add(function(){
	var oForm = getForm("addTask-{{$sejour_id}}");
	           
	new AideSaisie.AutoComplete(oForm.description, {
	  objectClass: "CSejourTask"
	});
	   
	new AideSaisie.AutoComplete(oForm.resultat, {
	  objectClass: "CSejourTask"
	});   
});	
	
</script>

<!-- Modale de creation / modification d'une activite --> 
<form name="addTask-{{$sejour_id}}" action="?" method="post" 
      onsubmit="{{if $task_element}}
			return onSubmitFormAjax(this, { onComplete: function(){ PlanSoins.refreshTask('{{$task->prescription_line_element_id}}'); Control.Modal.close(); } })
			{{else}}
			return submitActivite(this);
			{{/if}}">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="dosql" value="do_sejour_task_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
  <input type="hidden" name="sejour_task_id" value="{{$task->_id}}" />
	<input type="hidden" name="realise" value="{{$task->realise}}" />
	<input type="hidden" name="prescription_line_element_id" value="{{$task->prescription_line_element_id}}" />
  			
	<table class="form">
		<tr>
			<th class="title {{if $task->_id}} modify{{/if}}">
				{{if $task->_id}}
	        {{tr}}CSejourTask-title-modify{{/tr}}
	      {{else}}
	        {{tr}}CSejourTask-title-create{{/tr}}
	      {{/if}}
			</th>
		</tr>
	  <tr>
	    <td colspan="4">
	      <fieldset style="float: left; width: 45%;">
	       	<legend>
	      	  {{mb_title object=$task field="description"}}
	      	</legend>
				{{mb_field object=$task field="description"}}
	      </fieldset>
				<fieldset style="float: right; width: 45%">
	         <legend>
	           {{mb_title object=$task field="resultat"}}
	         </legend>
	        {{mb_field object=$task field="resultat"}}
	      </fieldset>
			</td>
		</tr>
		<tr>	
	    <td colspan="4">  
		    <div style="text-align: center">
		      <button class="submit">{{tr}}Save{{/tr}}</button>
					{{if $task->realise}}
					  <button class="cancel" onclick="$V(this.form.realise, '0');">Annuler la r�alisation</button>
					{{else}}
					  <button class="tick" onclick="$V(this.form.realise, '1');">R�aliser</button>
					{{/if}}
					{{if $task->_id}}
					<button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,objName:'{{$task->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
					{{/if}}
		    </div>
	    </td>
	  </tr>
	</table>
</form>