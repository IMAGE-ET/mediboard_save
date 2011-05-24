<table class="tbl">
  <tr>
    <th>{{mb_colonne class="CFunctions" field="text" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}</th>
    <th>{{mb_colonne class="CFunctions" field="type" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}</th>
    <th>{{tr}}CFunctions-back-users{{/tr}}</th>
  </tr>
  {{foreach from=$functions item=_function}}
  <tr {{if $_function->_id == $function->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="#{{$_function->_guid}}" onclick="showFunction('{{$_function->_id}}', this)">
        {{$_function->text}}
      </a>
    </td>
    <td>
      {{tr}}CFunctions.type.{{$_function->type}}{{/tr}}
    </td>
    <td style="background: #{{$_function->color}}">
      <a href="#" onclick="showFunction('{{$_function->_id}}', this)">
        {{$_function->_ref_users|@count}}
        (+ {{$_function->_back.secondary_functions|@count}})
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>