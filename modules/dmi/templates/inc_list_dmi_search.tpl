<table class="main tbl">
  <tr>
    <th>{{tr}}CDMI{{/tr}}</th>
    <th>{{tr}}CDMI-code{{/tr}}</th>
  </tr>
  
  {{foreach from=$products item=_product}}
    <tr>
      <td>{{$_product}}</td>
      <td>{{$_product->code}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">Aucun DMI retrouv�</td>
    </tr>
  {{/foreach}}
  
</table>