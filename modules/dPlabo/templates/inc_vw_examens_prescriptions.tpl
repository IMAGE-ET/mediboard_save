{{if $prescription->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$prescription->_class_name}}', {{$prescription->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$prescription->_view}}
    </th>
  </tr>
  <tr>
    <th>Examen</th>
    <th>Type</th>
    <th>Unit�</th>
    <th>Min</th>
    <th>Max</th>
  </tr>
  {{foreach from=$prescription->_ref_prescription_labo_examens item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr id="PrescriptionItem-{{$curr_item->_id}}">
    <td>
      <form name="delPrescriptionExamen-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_prescription_examen_aed" />
        <input type="hidden" name="prescription_labo_id" value="{{$prescription->_id}}" />
        <input type="hidden" name="prescription_labo_examen_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" style="float: right;" onclick="Prescription.Examen.del(this.form)" >{{tr}}Delete{{/tr}}</button>
      </form>
       
      <button type="button" class="edit notext" style="float: left;" onclick="Prescription.Examen.edit({{$curr_item->_id}})">button</button>

      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->_view}}
      </a>
    </td>
    <td>
      {{$curr_examen->type}}
    </td>
    <td>
      {{$curr_examen->unite}}
    </td>
    <td>
      {{$curr_examen->min}} {{$curr_examen->unite}}
    </td>
    <td>
      {{$curr_examen->max}} {{$curr_examen->unite}}
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}