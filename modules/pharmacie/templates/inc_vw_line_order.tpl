{{* $Id: inc_vw_line_delivrance.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=id value=$curr_delivery->_id}}

<tr {{if $curr_delivery->isDelivered()}}style="opacity: 0.4; display: none;" class="done"{{/if}}>
  <td>
    {{if $curr_delivery->patient_id}}
      {{$curr_delivery->_ref_patient->_view}}
    {{/if}}
  </td>
  <td style="text-align: center;">{{mb_ditto name=date value=$curr_delivery->date_dispensation|date_format:$conf.date}}</td>
  <td style="text-align: center;">{{mb_ditto name=time value=$curr_delivery->date_dispensation|date_format:$conf.time}}</td>
  <td>
    {{if $curr_delivery->_ref_stock->_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_guid}}')">
        {{$curr_delivery->_ref_stock->_view}}
      </span>
    {{else}}
    
    {{* 
     <script type="text/javascript">
      var oFormDispensation = getForm('dispensation-stock-{{$curr_delivery->_id}}');
    
      var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
      url.addParam("produit_max", 40);
      url.autoComplete(oFormDispensation.produit, $(oFormDispensation.produit).next(), {
        minChars: 3,
        select: "libelle",
        afterUpdateElement: function(input, selected) {
          $V(input.form._code, selected.down(".code-cip").innerHTML);
        },
        callback: function(input, queryString){
          return queryString + "&inLivret=1&search_by_cis=0";
        }
      });
     </script>
    *}}
    
    <form name="dispensation-stock-{{$curr_delivery->_id}}" onsubmit="return false" action="?" method="post">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      
      {{* 
      <input type="hidden" name="_code" value="" />
      Produit: 
      <input type="text" name="produit" value="" autocomplete="off" class="autocomplete" />
      <div style="display: none; text-align: left;" class="autocomplete"></div> 
      *}}
      
      {{mb_field object=$curr_delivery->_ref_stock field=product_id form=dispensation-stock-$id autocomplete="true,1,50,false,true"}}
      {{mb_field object=$curr_delivery field=quantity increment=1 form=dispensation-stock-$id size=3}}

      <button type="submit" class="tick notext" onclick="onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Dispenser">Dispenser</button>
      <button type="submit" class="cancel notext" onclick="$V(this.form.del, 1); $V(this.form.stock_id, ''); this.form.product_id.className = ''; onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Refuser">Refuser</button>
    </form>
    {{/if}}
  </td>
  <td class="text">{{$curr_delivery->comments}}</td>
  {{if !$conf.dPstock.CProductStockGroup.infinite_quantity}}
  <td>
    <table class="layout">
      <tr>
        <td style="width: 6em;">{{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$curr_delivery->_ref_stock}}</td>
        <td>
          {{mb_value object=$curr_delivery->_ref_stock field=quantity}}
        </td>
      </tr>
    </table>
  </td>
  {{/if}}
  
  {{* 
  {{if !$conf.dPstock.CProductStockService.infinite_quantity}}
    <td style="text-align: center;">
      {{assign var=stock value=$curr_delivery->_ref_stock}}
      {{$stock->quantity}}
    </td>
  {{/if}}
  *}}
  
  <td title="Quantit� d'origine: {{$curr_delivery->quantity}}">
    {{if $curr_delivery->_ref_stock->_id}}
    <form name="dispensation-validate-{{$curr_delivery->_id}}" onsubmit="return false" 
          action="?" method="post" class="{{if !$curr_delivery->isDelivered()}}dispensation{{/if}}">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="order" value="0" />
      <!-- check do_validate_dispensation_lines !! -->
      {{mb_field object=$curr_delivery field=quantity increment=1 form=dispensation-validate-$id size=3 value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
      <button type="submit" class="tick notext" onclick="onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Dispenser">Dispenser</button>
      <button type="submit" class="cancel notext" onclick="$V(this.form.del, 1); onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Refuser">Refuser</button>
    </form>
    {{else}}
    
    {{/if}}
  </td>
  <td>
    {{if $curr_delivery->_ref_stock->_id}}
      {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
    {{/if}}
  </td>
</tr>