<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Type de donn�es</th>
          <th>Quantit�</th>
        </tr>
        {{foreach from=$result item=curr_result}}
        <tr>
          <td>{{$curr_result.descr}}</td>
          <td>{{$curr_result.nombre}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>