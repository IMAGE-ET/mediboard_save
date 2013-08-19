{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    Grossesse.formFrom = getForm("bindFormGrossesse");
    // Apr�s cr�ation d'une grossesse, si l'objet concern� n'est reli� � aucune grossesse,
    // alors
    {{if !$object->grossesse_id && $grossesses|@count == 1}}
      Grossesse.formFrom.unique_grossesse_id.checked = true;
    {{/if}}
  });
</script>
<form name="bindFormGrossesse" method="get">
  <table class="tbl">
    <tr>
      <th colspan="3" class="category">
        Liste des grossesses
      </th>
    </tr>
    {{foreach from=$grossesses item=_grossesse}}
      <tr>
        <td class="narrow">
          <input type="radio" name="unique_grossesse_id" data-active="{{$_grossesse->active}}"
          {{if $_grossesse->_id == $object->grossesse_id}}checked="checked"{{/if}} value="{{$_grossesse->_id}}"
          data-view_grossesse="{{$_grossesse}}" />
        <td>
          <a href="#1" onclick="Grossesse.editGrossesse('{{$_grossesse->_id}}')">{{$_grossesse}}</a>
        </td>
        <td class="compact">
          {{if $_grossesse->_count.sejours}}
            <div>
              {{$_grossesse->_count.sejours}} {{tr}}CGrossesse-back-sejours{{/tr}}
            </div>
          {{/if}}
          {{if $_grossesse->_count.consultations}}
            <div>
              {{$_grossesse->_count.consultations}} {{tr}}CGrossesse-back-sejours{{/tr}}
            </div>
          {{/if}}
          {{if $_grossesse->_count.naissances}}
            <div>
              {{$_grossesse->_count.naissances}} {{tr}}CGrossesse-back-consultations{{/tr}}
            </div>
          {{/if}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty">{{tr}}CGrossesse.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>