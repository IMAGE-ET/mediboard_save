<table class="form">
  <tr>
    <th>{{mb_title object=$product field=name}}</th>
    <td>{{mb_value object=$product field=name}}</td>
  </tr>
  <tr>
    <th>{{mb_title object=$product field=description}}</th>
    <td>{{mb_value object=$product field=description}}</td>
  </tr>
  <tr>
    <th>En stock</th>
    <td>{{$product->_ref_stock_group->quantity}} {{include file="inc_vw_bargraph.tpl" stock=$product->_ref_stock_group}}</td>
  </tr>
</table>