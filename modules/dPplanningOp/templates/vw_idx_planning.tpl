<!-- $Id$ -->
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}
{{mb_include_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">

function updateListOperations(date) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("pratSel" , "{{$selPrat}}");
  url.addParam("date"    , date);

  url.requestUpdate('operations');
}

Main.add(function () {
  updateListOperations("{{$date}}");
});

</script>
<table class="main">
  
  <tr>
    <td style="height: 16px;">
      <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="selPrat">Praticien</label>
      <select name="selPrat" onchange="this.form.submit()" style="max-width: 150px;">
        <option value="-1">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $selPrat}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
      </form>
    </td>
    <td rowspan="3" class="greedyPane" style="vertical-align:top;">
      <div id="operations">
        <div class="small-info">
          Cette vue affiche la liste des interventions pour le jour s�lectionn�.
        </div>
      </div>
    </td>
  </tr>
  
  <tr>
    <th style="height: 16px;">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
  
  <tr>
    <td>
      
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage</th>
          <th colspan="2">Nb. Op�r.</th>
        </tr>
        {{foreach from=$listDays key=curr_date item=curr_day}}
        <tbody class="hoverable">
        <tr>
          <td align="right" rowspan="{{$curr_day|@count}}">
            <a href="#nothing" onclick="updateListOperations('{{$curr_date|date_format:"%Y-%m-%d"}}')">
              {{$curr_date|date_format:"%a %d"}}
            </a>
          </td>
          {{foreach from=$curr_day item=curr_plage}}
          {{if $curr_plage.plageop_id}}
          <td {{if $curr_plage.unique_chir}}class="user"{{else}}class="function"{{/if}}>
            {{$curr_plage.debut|date_format:$conf.time}} � {{$curr_plage.fin|date_format:$conf.time}}
          </td>
          {{else}}
          <td>
            Hors plage
          </td>
          {{/if}}
          <td align="center">{{$curr_plage.total}}</td>
          <td align="center" {{if $curr_plage.plageop_id && $curr_plage.spec_id}} style="background-color: #{{$curr_plage.color_function}}"{{/if}}>
            {{if $curr_plage.plageop_id && $curr_plage.spec_id}}
              <label title="{{$curr_plage.nom_function}}">{{$curr_plage.duree|date_format:$conf.time}}</label>
            {{else}}
              {{$curr_plage.duree|date_format:$conf.time}}
            {{/if}}
          </td>
        </tr>
        <tr>
          {{/foreach}}
        </tr>
        </tbody>
        {{/foreach}}
      </table>
      
      
    </td>
  </tr>
</table>