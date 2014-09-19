{{mb_script module="pmsi" script="reception" ajax=true}}

<script>
  sortBy = function(order_col, order_way) {
    var form = getForm("selType");
    $V(form.order_col, order_col);
    $V(form.order_way, order_way);
    Reception.reloadListSejours();
  }

  Main.add(function() {
    Reception.form = 'selType';
    Calendar.regField(getForm("changeDateCompletion").date, null, {noView: true});
  });
</script>

{{if $period}}
  <div class="small-info">
    Vue partielle limit�e au <strong>{{$period}}</strong>. Veuillez changer le filtre pour afficher toute la journ�e.
  </div>
{{/if}}

<table class="tbl" id="completion">
  <tr>
    <th class="title" colspan="10">
      <a href="#1" style="display: inline" onclick="$V(getForm('selType').date, '{{$hier}}'); Reception.reloadAll()">&lt;&lt;&lt;</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateCompletion" action="?" method="get">
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('selType').date, this.value); Reception.reloadAll()" />
      </form>
      <a href="#1" style="display: inline" onclick="$V(getForm('selType').date, '{{$demain}}'); Reception.reloadAll()">&gt;&gt;&gt;</a>
      <br />

      <em style="float: left; font-weight: normal;">
        {{$sejours|@count}} sortie(s) ce jour
      </em>

      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="$V(getForm('selType').filterFunction, this.value); Reception.reloadListSejours();">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filterFunction}}
      </select>
    </th>
  </tr>

  <tr>
    <th style="width:15%;">
      {{mb_colonne class="CSejour" field="sortie_reelle" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th style="width:15%;">
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Reception.filter(this, 'completion')" id="filter-patient-name" />
    </th>
    <th style="width:15%;">
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way function=sortBy}}
    </th>
    <th style="width:15%;">{{tr}}CSejour{{/tr}}</th>
    <th>{{mb_label class=CSejour field=reception_sortie}}</th>
    <th>{{mb_label class=CSejour field=completion_sortie}}</th>
  </tr>

  {{foreach from=$sejours item=_sejour}}
    <tr class="sejour sejour-type-default sejour-type-{{$_sejour->type}}" id="{{$_sejour->_guid}}">
      {{mb_include module=pmsi template="inc_vw_recept_sejour"}}
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}None{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>