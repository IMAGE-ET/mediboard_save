<script>
  // Notification de l'arriv�e du patient
  if (!window.Consultations) {
    Consultations = {
      start: function() {
      window.location.reload();
      }
    };
  }

  putArrivee = function(oForm) {
    var today = new Date();
    oForm.arrivee.value = today.toDATETIME(true);
    onSubmitFormAjax(oForm, Consultations.start);
  };

  printPlage = function(plage_id) {
    var url = new Url("cabinet", "print_plages");
    url.addParam("plage_id", plage_id);
    url.addParam("_telephone", 1);
    url.popup(700, 550, "Planning");
  };
</script>

{{if !$board}}
  {{if $canCabinet->read}}
    <script>
      Main.add(function() {
        Calendar.regField(getForm("changeView").date, null, {noView: true});
      });
    </script>
  {{/if}}
  <form name="changeView" method="get">
    <input type="hidden" name="m" value="{{$current_m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <table class="form">
      <tr>
        <td colspan="10" style="text-align: left; width: 100%; font-weight: bold; height: 20px;">
          <div style="float: right;">
            {{if $current_date}}
              {{$current_date|date_format:$conf.date}}
            {{/if}}
            {{$hour|date_format:$conf.time}}
          </div>
          {{$date|date_format:$conf.longdate}}
          {{if $canCabinet->read}}
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          {{/if}}
        </td>
      </tr>
      <tr>
        {{if $canCabinet->read}}
        <th><label for="vue2" title="Type de vue du planning">Type de vue</label></th>
        <td colspan="10">
          <select name="vue2" onchange="this.form.submit()">
            <option value="0" {{if $vue == "0"}}selected{{/if}}>Tout afficher</option>
            <option value="1" {{if $vue == "1"}}selected{{/if}}>Cacher les termin�es</option>
          </select>
        </td>
        {{/if}}
      </tr>
    </table>
  </form>
{{/if}}

<table class="tbl" style="{{if !@$offline}}font-size: 9px;{{/if}} {{if @$fixed_width|default:0}}width: 250px{{/if}}">
  <tr>
    <th id="inc_list_consult_th_consult" class="title" colspan="10">
      Consultations
    {{if $board && isset($withClosed|smarty:nodefaults)}}
      <label style="float: right;font-size: 10px;margin-left: -125px;color:black;text-shadow:none;">
        Masquer les termin�es
        <input name="withClosed" type="checkbox" {{if !$withClosed}}checked="checked"{{/if}} onchange="updateListConsults(this.checked?0:1);"/>
      </label>
    {{/if}}
    </th>
  </tr>

  <tr>
    <th style="width: 50px; ">Heure</th>
    <th colspan="10">Patient / Motif</th>
  </tr>
  
  {{foreach from=$listPlage item=_plage}}
    <tr>
      <th colspan="10" class="section" style="overflow: hidden">
        {{if $current_m == "dPurgences"}}
          <span style="float: right;">
            <button class="print notext" onclick="printPlage({{$_plage->_id}})">
              {{tr}}Print{{/tr}}
            </button>
          </span>
        {{/if}}
        {{mb_include module=system template=inc_object_notes object=$_plage}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_plage->_guid}}');">
          {{$_plage->debut|date_format:$conf.time}}
          - {{$_plage->fin|date_format:$conf.time}}
          {{if $_plage->libelle}}: {{$_plage->libelle|truncate:30:"..."}}{{/if}}
        </span>
      </th>
    </tr>
    {{foreach from=$_plage->_ref_consultations item=_consult}}
      {{mb_include module=cabinet template=inc_detail_consult}}
    {{foreachelse}}
      <tr>
        <td colspan="10" class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>