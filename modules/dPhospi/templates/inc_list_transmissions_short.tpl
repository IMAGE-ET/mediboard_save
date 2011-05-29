<table class="tbl">
  <tr>
    <th class="category">
      {{tr}}CTransmissionMedicale-user_id{{/tr}}
    </th>
    <th class="category">
      {{tr}}CTransmissionMedicale-date{{/tr}}
    </th>
    <th class="category">
      {{tr}}CTransmissionMedicale._heure{{/tr}}
    </th>
    <th class="category">
      {{tr}}CTransmissionMedicale-text{{/tr}}
    </th>
  </tr>
  <tbody>
    {{foreach from=$transmissions item=_transmission}}
      <tr>
        <td>
          {{$_transmission->_ref_user}}
        </td>
        <td>
          {{mb_ditto name=date value=$_transmission->date|date_format:$conf.date}}
        </td>
        <td>
          {{$_transmission->date|date_format:$conf.time}}
        </td>
        <td class="text {{if $_transmission->type}}trans-{{$_transmission->type}}{{/if}} libelle_trans" {{if $_transmission->degre == "high"}} style="background-color: #faa" {{/if}}>
          {{mb_value object=$_transmission field=text}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="4" class="empty">
          {{tr}}CTransmissionMedicale.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </tbody>
</table>

