{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("changeDate").date_suivi, null, {noView: true});
});

function printFicheBloc(oper_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function showLegend() {
  var url = new Url("dPbloc", "legende");
  url.popup(500, 150, "Legend");
}
</script>

<table class="main" id="suivi-salles">
  <tr class="only-printable">
    <th colspan="100">
      <h1>{{$date_suivi|date_format:$conf.longdate}}</h1>
    </th>
  </tr>
  <tr class="not-printable">
    <th colspan="0">
      <button type="button" onclick="showLegend()" class="search" style="float: left;">L�gende</button>
      <button type="button" onclick="$('suivi-salles').print();" class="print" style="float: right;">{{tr}}Print{{/tr}}</button>
      
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        
        <select name="bloc_id" onchange="this.form.submit()">
        {{foreach from=$listBlocs item=curr_bloc}}
          <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
            {{$curr_bloc->nom}}
          </option>
        {{/foreach}}
        </select>
        {{$date_suivi|date_format:$conf.longdate}}
        <input type="hidden" name="date_suivi" class="date" value="{{$date_suivi}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr class="not-printable">
    <td class="button" colspan="0">
      {{foreach from=$bloc->_ref_salles item=_salle}}
        <label><input type="checkbox" onclick="Effect.toggle('salle-{{$_salle->_id}}', 'appear');" checked="checked" /> {{$_salle->nom}}</label>
      {{/foreach}}
    </td>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
    <td id="salle-{{$_salle->_id}}">
      <table class="tbl">
        <tr>
          <th class="title">{{$_salle->nom}}</th>
        </tr>
      </table>
      {{mb_include module=dPsalleOp template=inc_details_plages salle=$_salle}}
    </td>
    {{foreachelse}}
    <td class="empty">{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
  </tr>
</table>