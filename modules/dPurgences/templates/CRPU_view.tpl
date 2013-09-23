{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include template=CMbObject_view}}

<script type="text/javascript">
  printDossier = function(id) {
    var url = new Url("dPurgences", "print_dossier");
    url.addParam("rpu_id", id);
    url.popup(700, 550, "RPU");
  }
</script>
  
<table class="tbl tooltip">
  <tr>
    <td class="button">
      <button type="button" class="print" onclick="printDossier({{$object->_id}})">
        {{tr}}Print{{/tr}} dossier
      </button>
    </td>
  </tr>
</table>