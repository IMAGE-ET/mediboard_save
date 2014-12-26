{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  mineSalleForDay = function(salle_id, date) {
    var url = new Url('bloc', 'do_mine_salle_day', 'dosql');
    url.addParam('salle_id', salle_id);
    url.addParam('date', date);
    url.requestUpdate("systemMsg", {ajax: true, method: 'post'});
  };
</script>

<table class="main" id="suivi-salles">
  <tr>
    <th colspan="100">
      <h1 class="no-break">{{$date_suivi|date_format:$conf.longdate}}</h1>
    </th>
  </tr>
  <tr class="not-printable">
    <td class="button" colspan="100">
      {{if $page}}
        <div>
          {{foreach from=1|range:$page_count item=i}}
            <span class="circled" {{if $i == $current_page+1}} style="background: orange" {{/if}}>&nbsp;&nbsp;&nbsp;&nbsp;</span>
          {{/foreach}}
        </div>
      {{else}}
        {{foreach from=$bloc->_ref_salles item=_salle}}
          <label><input type="checkbox" onclick="Effect.toggle('salle-{{$_salle->_id}}', 'appear');" checked="checked" /> {{$_salle->nom}}</label>
        {{/foreach}}
        {{if $non_traitees|@count}}
          <label><input type="checkbox" onclick="Effect.toggle('non-traitees', 'appear');" checked="checked" /> {{tr}}CSejour.type.hors_plage{{/tr}}</label>
        {{/if}}
      {{/if}}
    </td>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
      <td id="salle-{{$_salle->_id}}">
        <table class="tbl">
          <tr>
            <th class="title">
              {{if $app->_ref_user->isAdmin()}}
                <button style="float:right;" onclick="mineSalleForDay('{{$_salle->_id}}', '{{$date_suivi}}')" class="change notext"></button>
              {{/if}}
              {{$_salle->nom}}
            </th>
          </tr>
        </table>
        {{mb_include module=salleOp template=inc_details_plages salle=$_salle redirect_tab=1}}
      </td>
    {{foreachelse}}
      <td class="empty">{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}

    {{if $non_traitees|@count}}
      {{assign var=salle value=""}}
      <td id="non-traitees">
        <table class="tbl">
          <tr>
            <th class="title" colspan="5">{{tr}}CSejour.type.hors_plage{{/tr}}</th>
          </tr>
          {{mb_include module="salleOp" template="inc_liste_operations" urgence=1 operations=$non_traitees redirect_tab=1 ajax_salle=1}}
        </table>
      </td>
    {{/if}}
  </tr>
</table>