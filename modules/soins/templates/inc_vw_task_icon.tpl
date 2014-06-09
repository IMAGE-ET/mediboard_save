<div style="display: none;" id="consult_tache_{{$line->_id}}">
  <button class="new"
       onclick="Control.Modal.close(); PlanSoins.editRDV('{{$prescription->_ref_object->patient_id}}', '{{$prescription->object_id}}', '{{$line->_id}}');">
    Prendre un rendez-vous
  </button>
  <button class="new"
          onclick="Control.Modal.close(); PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');">
    Cr�er une t�che
  </button>
  <div style="text-align: center">
    <button class="cancel" style="text-align: center;" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
  </div>
</div>

<a href="#1" style="float: right">
  {{if $line->_ref_task->_id}}
    {{if $line->_ref_task->realise}}
      <img src="images/icons/phone_green.png" title="RDV r�alis�" onclick="PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');" />
    {{else}}
      <img src="images/icons/phone_orange.png" title="RDV pris" onclick="PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');" />
    {{/if}}
  {{elseif !$line->date_arret && !$line->time_arret}}
    <img src="images/icons/phone_red.png" title="RDV � prendre" onclick="Modal.open('consult_tache_{{$line->_id}}')" />
  {{/if}}
</a>