<table class="tbl">
  <tr>
    <th class="category">
      Plages vides qui seront supprim�es
    </th>
  </tr>
  {{foreach from=$plages item=_plage}}
    <tr>
      <td>{{$_plage}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CPlageOp.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>
