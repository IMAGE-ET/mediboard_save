<table class="tbl">
  <tr>
    <th class="title">
      Lignes sans t�ches associ�es ({{$lines|@count}})
    </th>
  </tr>   
  {{foreach from=$lines item=_line}}
    <tr>
      <td>
        {{$_line->_view}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CPrescriptionLineElement.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>