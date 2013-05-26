{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{unique_id var=consumption_id}}

<script type="text/javascript">
Main.add(function(){
  var url = new Url("dPstock", "httpreq_vw_product_consumption_graph");
  url.addParam("product_id", {{$object->_id}});
  url.requestUpdate("product-consumption-{{$consumption_id}}");
})
</script>

{{mb_include module=system template=CMbObject_view}}

<div id="product-consumption-{{$consumption_id}}"></div>

{{if $object->_can->edit}}
  <table class="main tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$object->_id}}'">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
    </tr>
  </table>
{{/if}}
