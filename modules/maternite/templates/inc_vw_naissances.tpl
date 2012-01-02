<table class="tbl">
  <tr>
    <th class="title" colspan="5">
      <button type="button" class="add" style="float: left;" {{if !$grossesse->active}}disabled="disabled"{{/if}}
        onclick="Naissance.edit(0, '{{$operation->_id}}')">Naissance</button>
      Naissances
    </th>
  </tr>
  <tr>
    <th class="category"></th>
    <th class="category">{{mb_label class=CNaissance field=rang}} / {{mb_label class=CNaissance field=heure}}</th>
    <th class="category">{{tr}}CPatient{{/tr}}</th>
    <th class="category">{{tr}}CSejour{{/tr}}</th>
  </tr>
  {{foreach from=$operation->_ref_naissances item=_naissance}}
    {{assign var=sejour_enfant value=$_naissance->_ref_sejour_enfant}}
    {{assign var=enfant value=$sejour_enfant->_ref_patient}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="Naissance.edit('{{$_naissance->_id}}')"></button>
      </td>
      <td>
        Rang {{mb_value object=$_naissance field=rang}}
        le {{$enfant->naissance|date_format:$conf.date}} � {{mb_value object=$_naissance field=heure}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$enfant->_guid}}')">{{$enfant}}</span>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour_enfant->_guid}}')">{{$sejour_enfant->_shortview}}</span>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">
        {{tr}}CNaissance.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>