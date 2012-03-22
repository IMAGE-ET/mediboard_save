<script type="text/javascript">
  var graph = {{$graph|@json}};
  
  Main.add(function(){
    Flotr.draw($('graph'), graph.series, graph.options);
    var form = getForm("filterUSCPO");
    Calendar.regField(form.date_min);
    Calendar.regField(form.date_max);
  });
</script>

<form name="filterUSCPO" method="get" action="?"
  onsubmit="refreshUSCPO($V(this.date_min), $V(this.date_max), $V(this.service_id)); return false;">
  <table class="form">
    <tr>
      <th colspan="3" class="category">Crit�res de filtre</th>
    </tr>
    <tr>
      <td>
        A partir du <input type="hidden" name="date_min" class="date notNull" value="{{$date_min}}" onchange="this.form.onsubmit();"/>
      </td>
      <td>
        Jusqu'au <input type="hidden" name="date_max" class="date notNull" value="{{$date_max}}" onchange="this.form.onsubmit();"/>
      </td>
      <td>
        Service
        <select name="service_id" onchange="this.form.onsubmit();">
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  </table>
</form>
<div style="width: 640px; height: 480px; float: left; margin: 1em;" id="graph"></div>