{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 7769 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination current=$start change_page=refreshReceptionsList}}

<table class="main tbl">
  <tr>
    <th class="narrow">{{mb_title class=CProductReception field="reference"}}</th>
    <th>{{mb_title class=CProductReception field="societe_id"}}</th>
    <th>{{mb_title class=CProductReception field="date"}}</th>
    <th>{{mb_title class=CProductReception field="bill_number"}}</th>
    <th>{{mb_title class=CProductReception field="bill_date"}}</th>
    <th>Nb �l�ments</th>
    <th></th>
  </tr>
  {{foreach from=$receptions item=_reception}}
  <tr>
    <td>
      <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reception->_guid}}')">
        {{mb_value object=$_reception field="reference"}}
      </strong>
    </td>
    <td>{{mb_value object=$_reception field="societe_id"}}</td>
    <td>{{mb_value object=$_reception field="date"}}</td>
    <td>{{mb_value object=$_reception field="bill_number"}}</td>
    <td>{{mb_value object=$_reception field="bill_date"}}</td>
    <td>{{$_reception->_count_reception_items}}</td>
    <td class="narrow">
      <button type="button" class="edit notext" {{if $_reception->locked}}disabled="disabled"{{/if}} onclick="editReception({{$_reception->_id}})">{{tr}}Edit{{/tr}}</button>
      
      <form name="reception-{{if $_reception->locked}}un{{/if}}lock-{{$_reception->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshReceptionsList})">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_product_reception_aed" />
        {{mb_key object=$_reception}}
        <input type="hidden" name="locked" value="{{$_reception->locked|ternary:0:1}}" />
        <button type="submit" class="{{if $_reception->locked}}un{{/if}}lock notext" >{{if $_reception->locked}}D�verrouiller{{else}}V�rrouiller{{/if}}</button>
      </form>
      
      <button type="button" class="print" onclick="printReception('{{$_reception->_id}}');">Bon de r�ception</button>
      <button type="button" class="barcode" onclick="printBarcodeGrid('{{$_reception->_id}}');">Codes barre</button>
    </td>
  </tr>
  {{/foreach}}
</table>

<!-- The receptions count -->
<script type="text/javascript">
  tab = $$('a[href="#list-receptions"]')[0];
  counter = tab.down("small");
  count = {{$total}};
  
  if (count > 0)
    tab.removeClassName("empty");
  else
    tab.addClassName("empty");
    
  counter.update("("+count+")");
</script>