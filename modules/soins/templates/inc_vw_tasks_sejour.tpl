<script type="text/javascript">

submitActivite = function(oForm){
  return onSubmitFormAjax(oForm, { onComplete: function(){
    updateTasks('{{$sejour->_id}}');
    url.modalObject.close(); 
  } } );
}

editTask = function(task_id){
  url = new Url("soins", "ajax_modal_task");
  url.addParam("task_id", task_id);
	url.addParam("sejour_id", "{{$sejour->_id}}");
	url.requestModal(600, 200);
}

</script>

{{mb_default var=offline value=0}}

<div id="modal-task-{{$sejour->_id}}" style="display: none; width: 70%;">
</div>

<table class="tbl print_tasks">
  <thead>
    <tr>
      <th class="title" colspan="3">
        {{$sejour->_view}}
        {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA}}
      </th>
    </tr>
  </thead>
  <tr>
    <th colspan="4" class="title">
    	{{if !$mode_realisation && !$readonly}}
    	  <button type="button" class="add notext" onclick="editTask('0');" style="float: right;"></button>
			{{/if}}
    	T�ches {{if $sejour->_ref_tasks}}({{$sejour->_ref_tasks|@count}}){{/if}}
		</th>
	</tr>		
	<tr>
    <th colspan="2">{{mb_title class="CSejourTask" field="description"}}</th>
		<th>{{mb_title class="CSejourTask" field="resultat"}}</th>
		{{if !$readonly}}
      <th></th>
    {{/if}}
	</tr>
	{{foreach from=$sejour->_ref_tasks item=_task}}
	  <tr>
	  	<td class="narrow"><input type="checkbox" disabled="disabled" {{if $_task->realise}}checked="checked"{{/if}} /></td>
	    <td {{if $_task->realise}}style="text-decoration: line-through"{{/if}}>{{mb_value object=$_task field="description"}}
			  {{if $_task->prescription_line_element_id}}
				  <strong>{{$_task->_ref_prescription_line_element->_view}}</strong>
				{{/if}}
			</td>
	    <td>{{mb_value object=$_task field="resultat"}}</td>
      {{if !$readonly}}
  			<td class="narrow">
  				{{if $mode_realisation}}
  				  <form name="closeTask-{{$_task->_id}}" action="?" method="post" 
  					      onsubmit="return onSubmitFormAjax(this, { onComplete: function(){
  								             refreshLineSejour('{{$sejour->_id}}');
  								             $('tooltip-content-tasks-{{$sejour->_id}}').up('.tooltip').remove();
  													} });">
  				  	<input type="hidden" name="m" value="soins" />
  						<input type="hidden" name="dosql" value="do_sejour_task_aed" />
  						<input type="hidden" name="del" value="" />
  						<input type="hidden" name="sejour_task_id" value="{{$_task->_id}}" />
  						<input type="hidden" name="realise" value="1" />
  				    <button type="submit" class="tick notext"></button>
  					</form> 
        	{{else}}
  				  <button type="button" class="edit notext" onclick="editTask('{{$_task->_id}}');"></button>
  				{{/if}}
  			</td>
      {{/if}}
	  </tr>
	{{foreachelse}}
	  <tr>
	  	<td colspan="4" class="empty">
	  		{{tr}}CSejourTask.none{{/tr}}
	  	</td>
	  </tr>
	{{/foreach}}
</table>