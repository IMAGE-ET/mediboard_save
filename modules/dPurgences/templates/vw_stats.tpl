{{* $Id: vw_stats.tpl 8454 2010-04-02 10:47:24Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8454 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var filterForm,
    options = {
      legend: {container: "graph-legend"},
      yaxis: {tickFormatter: yTickFormatter},
      y2axis: {min: null}
    },
    data;
    
var profiles = {
  lines: {
    lines: {show: true},
    bars: {show: false},
    mouse: {track: true},
    grid: {verticalLines: true},
    markers: {show: true, labelFormatter: function(obj) {
      var y = (Math.round(obj.y*100)/100);
      return y === 0 ? "" : y;
    }}
  },
  bars: {
    lines: {show: false},
    bars: {show: true},
    mouse: {track: false},
    grid: {verticalLines: false},
    markers: {show: false, labelFormatter: Flotr.defaultMarkerFormatter}
  }
};

function yTickFormatter(y) {
	return parseInt(y).toString();
}

function timeLabelFormatter(obj) {
  return obj.y+ "min";
}

function drawGraphs(data) {
  window.data = data || window.data;
  
  var mode = $V(getForm("stats-filter").mode);
  var container = $("graphs").update("");
  
  $H(data).each(function(pair) {
    var d = pair.value;
    if (!d || !d.series) return;
    
    var graph = new Element("div", {id:"stats-"+pair.key, style: "width: 600px; height: 400px; margin-right: 0; margin-left: auto;"});
    container.insert(graph);
    
    d.options = Object.merge(options, d.options);
    d.options = Object.merge(profiles[mode], d.options);
    
    var series = d.series;
    
    series.each(function(s){
      if (s.mouse && Object.isString(s.mouse.trackFormatter))
        s.mouse.trackFormatter = window[s.mouse.trackFormatter];
    });
    
    if (mode != "bars") {
      series = d.series.clone();
      series.pop();
    }
    
    $("graph-legend").update();
    Flotr.draw($('stats-'+pair.key), series, d.options);
  });
}

function updateGraphs(form){
  WaitingMessage.cover($("graphs"));
  
  var url = new Url("dPurgences", "ajax_json_stats");
  url.addFormData(form);
  url.requestJSON(drawGraphs);
  return false;
}

Main.add(function () {
  var form = getForm('stats-filter');
  updateGraphs(form);
  $(form.count).addSpinner({min: 0});
});
</script>

<form name="stats-filter" action="?" method="get" onsubmit="return updateGraphs(this)">
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="m" value="dPurgences" />
  
  <table class="main form">
    <tr>
      <th class="category">Affichage</th>
      <th class="category">Stats</th>
      <th class="category">Stats compl�mentaires</th>
    </tr>
    <tr>
      <td>
        <table class="main form">
          <tr>
            <th>Date</th>
            <td>{{mb_field object=$filter field=entree register=true form="stats-filter" prop="date"}}</td>
          </tr>
          <tr>
            <th>Sur</th>
            <td>
              <input type="text" name="count" value="{{$count}}" size="2" />
              <select name="period" onchange="this.form.onsubmit()">
                <option value="DAY">jours</option>
                <option value="WEEK">semaines</option>
                <option value="MONTH">mois</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Mode</th>
            <td>
              <label>
                <input type="radio" name="mode" value="bars" onchange="drawGraphs(window.data, $V(this))" checked="checked" /> Barres
              </label>
              <label>
                <input type="radio" name="mode" value="lines" onchange="drawGraphs(window.data, $V(this))" /> Lignes
              </label>
            </td>
          </tr>
        </table>
      </td>
      <td style="white-space: normal;">
        {{foreach from=$axes key=_axis item=_label}}
          <label style="width: 16em; display: inline-block;">
            <input type="radio" name="axe" value="{{$_axis}}" {{if $_axis == $axe}}checked="checked"{{/if}} onchange="this.form.onsubmit()" /> {{$_label}}
          </label>
        {{/foreach}}
      </td>
      <td style="white-space: normal;">
        {{foreach from=$axes_other key=_axis item=_label}}
          <label style="width: 16em; display: inline-block;">
            <input type="radio" name="axe" value="{{$_axis}}" {{if $_axis == $axe}}checked="checked"{{/if}} onchange="this.form.onsubmit()" /> {{$_label}}
          </label>
        {{/foreach}}
      </td>
    </tr>
    
    <tr>
      <td colspan="3" class="button">
        <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<table class="main">
  <tr>
    <td id="graphs"></td>
    <td id="graph-legend" style="width: 25%;"></td>
  </tr>
</table>
