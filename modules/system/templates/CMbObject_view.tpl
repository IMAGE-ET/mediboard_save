<table class="tbl tooltip">
  <tr>
    <th>
      {{$object->_view}}
    </th>
  </tr>

  <tr>
    <td>
     {{foreach from=$props key="name" item="value"}}
      <strong>{{mb_label object=$object field=$name}}</strong> :
      {{mb_value object=$object field=$name}}
      <br/>
     {{/foreach}}
    </td>
  </tr>
</table>