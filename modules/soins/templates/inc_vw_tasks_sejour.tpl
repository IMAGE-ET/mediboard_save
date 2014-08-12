{{mb_default var=offline value=0}}
{{mb_default var=header  value=1}}

<script>
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

{{if ($sejour->_count_tasks !== null)}}
  <script>
    if ($('tasks')) {
      Control.Tabs.setTabCount('tasks', {{$sejour->_count_pending_tasks}}, {{$sejour->_count_tasks}});
    }
  </script>
{{/if}}

<div id="modal-task-{{$sejour->_id}}" style="display: none; width: 70%;"></div>

{{if !$mode_realisation && !$readonly}}
  <button type="button" class="add" onclick="editTask('0');">
    {{tr}}CSejourTask-title-create{{/tr}}
  </button>
{{/if}}

<table class="tbl print_tasks">
  {{if $header}} 
    <thead>
      <tr>
        <th class="title" colspan="5">
          {{$sejour}}
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        </th>
      </tr>
    </thead>
  {{/if}}
  <tr>
    <th colspan="2">Utilisateur / Date</th>
    <th>{{mb_title class="CSejourTask" field="description"}}</th>
    <th>Utilisateur / Date</th>
    <th>{{mb_title class="CSejourTask" field="resultat"}}</th>
    {{if !$readonly}}
      <th></th>
    {{/if}}
  </tr>
  {{foreach from=$sejour->_ref_tasks item=_task}}
    <tr>
      <td class="narrow"><input type="checkbox" disabled {{if $_task->realise}}checked{{/if}} /></td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_task->_ref_author->_ref_mediuser}}
        <br/>
        {{mb_value object=$_task field=date}}
      </td>
      <td style="width: 50%; {{if $_task->realise}}text-decoration: line-through; color: #888;{{/if}}">
        {{mb_value object=$_task field="description"}}
        {{if $_task->prescription_line_element_id}}
          <strong>
            {{$_task->_ref_prescription_line_element}}
            {{if $_task->_ref_prescription_line_element->date_arret && $_task->_ref_prescription_line_element->time_arret}}
              <br />
              Prescription arr�t�e le {{mb_value object=$_task->_ref_prescription_line_element field=date_arret}} � {{mb_value object=$_task->_ref_prescription_line_element field=time_arret}}
            {{/if}}
          </strong>
        {{/if}}
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_task->_ref_author_realise->_ref_mediuser}}
        <br/>
        {{mb_value object=$_task field=date_realise}}
      </td>
      <td style="width: 50%;">{{mb_value object=$_task field="resultat"}}</td>
      {{if !$readonly}}
        <td class="narrow">
          {{if $mode_realisation}}
            <form name="closeTask-{{$_task->_id}}" action="?" method="post" 
                  onsubmit="return onSubmitFormAjax(this, function() {
                               refreshLineSejour('{{$sejour->_id}}');
                               $('tooltip-content-tasks-{{$sejour->_id}}').up('.tooltip').remove();
                             });">
              <input type="hidden" name="m" value="soins" />
              <input type="hidden" name="dosql" value="do_sejour_task_aed" />
              <input type="hidden" name="del" value="" />
              {{mb_key object=$_task}}
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
      <td colspan="6" class="empty">
        {{tr}}CSejourTask.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>