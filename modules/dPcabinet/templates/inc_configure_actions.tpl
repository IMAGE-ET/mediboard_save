<table class="tbl">
  <tr>
    <th class="category">{{tr}}CPlageconsult{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="button">
      <script>
        PlageConsult = {
          transfert: function() {
            var url = new Url();
            url.setModuleAction("dPcabinet", "transfert_plageconsult");
            url.popup(500, 600, "transfert");
          }
        }
      </script>
      <button class="modify" type="button" onclick="PlageConsult.transfert();">
        {{tr}}mod-dPcabinet-tab-transfert_plageconsult{{/tr}}
      </button> 
    </td>
  </tr>
  <tr>
    <td class="button">
      <script>
        correctionPlagesConsult = function(resolve) {
          var url = new Url("cabinet", "ajax_move_consult_plage");
          url.addParam("resolve", resolve);
          url.requestModal(500);
        };
      </script>
      <button class="modify" type="button" onclick="correctionPlagesConsult(0);">
        D�placer des consultations
      </button>
    </td>
  </tr>

  <tr>
    <th class="category">{{tr}}CConsultation{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="button">
      <script>
        Main.add(function() {
          Calendar.regField(getForm("MacroStats").date);
        });
      </script>
      
      <form name="MacroStats" method="get">
        <input type="hidden" name="date" value="{{$date}}" />
        <select name="period">
          <option value="day"  >{{tr}}Day  {{/tr}}</option>
          <option value="week" >{{tr}}Week {{/tr}}</option>
          <option value="month">{{tr}}Month{{/tr}}</option>
          <option value="year" >{{tr}}Year {{/tr}}</option>
        </select>
        <select name="type">
          <option value="RDV">RDV</option>
          <option value="consult">{{tr}}CConsultation{{/tr}}</option>
          <option value="FSE" style="display: none;">{{tr}}CFSE{{/tr}}</option>
        </select>
        <button class="modify" type="button" onclick="Consultation.macroStats(this);">
          {{tr}}mod-cabinet-tab-user_stats{{/tr}}
        </button> 
      </form>
    </td>
  </tr>

  <tr>
    <td class="button">
      <button class="search" type="button" onclick="Consultation.checkParams();">
        {{tr}}mod-cabinet-tab-check_params{{/tr}}
      </button> 
    </td>
  </tr>
</table>

<h2>Actions de maintenances</h2>

<script>
  createConsultAnesth = function() {
    var url = new Url("cabinet", "ajax_create_missing_consult_anesth");
    url.addParam("anesth_id", $V($("anesth_id")));
    url.requestUpdate("result-create_consult_anesth", { onComplete: function() {
      repeatActions("createConsultAnesth");
    }});
  };
  
   repeatActions = function (func) {
    if ($V($("check_repeat_actions"))) {      
      window[func]();
    }
  };

  cleanDoublonsConsultAnesth = function() {
    var url = new Url("cabinet", "ajax_delete_doublons_consult_anesth");
    url.requestUpdate("result_doublons_consult_anesth");
  }
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td class="narrow">
      <button class="search" onclick="createConsultAnesth()">
        Cr�er dossiers d'anesth�sie pour des consultations
      </button> <br />
      <select name="anesth_id" id="anesth_id">
        {{foreach from=$anesths item=_anesth}}
          <option value="{{$_anesth->_id}}">{{$_anesth->_view}}</option>
        {{/foreach}}
      </select><br />
      <input type="checkbox" name="repeat_actions" id="check_repeat_actions"/> Relancer automatiquement 
    </td>
    <td id="result-create_consult_anesth"></td>
  </tr>
  <tr>
    <td class="narrow">
      <button class="trash" onclick="cleanDoublonsConsultAnesth()">
      Supression des dossiers d'anesth�sie en double
    </td>
    <td id="result_doublons_consult_anesth"></td>
  </tr>
</table>