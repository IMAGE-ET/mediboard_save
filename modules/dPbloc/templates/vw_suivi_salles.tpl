{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=bloc script=edit_planning}}

<script>
  togglePlayPause = function(button) {
    button.toggleClassName("play");
    button.toggleClassName("pause");
    if (!(window.autoRefreshSuivi)) {
      window.autoRefreshSuivi = setInterval(function(){
        updateSuiviSalle();
      }, ({{math equation="a*1000" a=$conf.dPbloc.CPlageOp.time_autorefresh}}));
    }
    else {
      clearTimeout(window.autoRefreshSuivi);
    }
  };

  updateSuiviSalle = function() {
    var oform   = getForm('changeDate');
    var date    = $V(oform.date);
    var bloc_id = $V(oform.bloc_id);

    if (bloc_id) {
      var url = new Url("dPbloc", "ajax_vw_suivi_salle");
      url.addParam('bloc_id', bloc_id);
      url.addParam('date', date);
      var str = DateFormat.format(Date.fromDATE(date), " dd/MM/yyyy");
      if (date == '{{$dnow}}') {
        str= str+" (Aujourd'hui)";
      }
      $('dateSuiviSalle').update(str);
      url.requestUpdate("result_suivi");
    }
  };

  printFicheBloc = function(interv_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", interv_id);
  url.popup(700, 600, 'FeuilleBloc');
  };

  printAnapath = function() {
    var form = getForm('changeDate');
    var url = new Url("dPbloc", "print_anapath");
    url.addParam("date"   , $V(form.date));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.popup(700, 600, 'Anapath');
  };

  printBacterio = function() {
    var form = getForm('changeDate');
    var url = new Url("dPbloc", "print_bacterio");
    url.addParam("date"   , $V(form.date));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.popup(700, 600, 'Bacterio');
  };

  modePresentation = function() {
    var form = getForm('changeDate');
    var url = new Url("dPbloc", "vw_suivi_salles_presentation");
    url.addParam("date"   , $V(form.date));
    url.addParam("bloc_id", $V(form.bloc_id));
    url.popup("100%", "100%", 'Mode pr�sentation');
  };

  showLegend = function() {
    new Url('bloc', 'legende').requestModal()
  };

  Main.add(function () {
    {{if $blocs|@count}}
      Calendar.regField(getForm("changeDate").date, null, {noView: true});
      updateSuiviSalle();
      if (Preferences.startAutoRefreshAtStartup == 1) {
        togglePlayPause($('autorefreshSuiviSalleButton'));
      }
    {{/if}}
  });
</script>

{{if $blocs|@count}}
  <table class="main not-printable">
    <tr>
      <td>
        <button id="autorefreshSuiviSalleButton" style="float: left;" class="play" title="Rechargement automatique de la page ({{tr}}config-dPbloc-CPlageOp-time_autorefresh-{{$conf.dPbloc.CPlageOp.time_autorefresh}}{{/tr}})" onclick="togglePlayPause(this);">
          Rech. Auto
        </button>

        <span style="float: right;">
          <button type="button" class="print" onclick="printBacterio();">{{tr}}COperation-labo{{/tr}}</button>
          <button type="button" class="print" onclick="printAnapath();">{{tr}}COperation-anapath{{/tr}}</button>
          <button type="button" class="print" onclick="$('suivi-salles').print();">{{tr}}Print{{/tr}}</button>
          <button type="button" class="search" onclick="showLegend()">L�gende</button>
          <button type="button" class="lookup" onclick="modePresentation();">Mode pr�sentation</button>
        </span>

        <form action="?" name="changeDate" method="get">
          <label> Bloc :
            <select name="bloc_id" onchange="updateSuiviSalle();">
              {{foreach from=$blocs item=curr_bloc}}
                <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc_id}}selected{{/if}}>
                  {{$curr_bloc->nom}}
                </option>
              {{/foreach}}
            </select>
          </label>
          <label>Date :
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="updateSuiviSalle();" /><span id="dateSuiviSalle"></span>
          </label>
        </form>
      </td>
    </tr>
  </table>
{{/if}}

<div id="result_suivi">
  {{if !$blocs|@count}}
    <div class="small-warning">
      {{tr}}dPbloc-msg-no_bloc{{/tr}}
    </div>
  {{/if}}
</div>