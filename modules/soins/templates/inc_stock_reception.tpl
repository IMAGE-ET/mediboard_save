{{* $Id: inc_restockages_service_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
printReceptionReport = function(service_id){
  var url = new Url("soins", "httpreq_vw_stock_reception");
  url.addParam("service_id", service_id);
  url.addParam("mode", "print");
  url.popup(800, 600, "Rapport des commandes");
}

changeReceptionPage = function(start) {
  $V(getForm('filter-reception').start, start); 
}
</script>

<!-- #help -->
<div class="big-info">
  <strong>Une fois votre commande re�ue</strong> avec le bouton <button type="button" class="tick notext"></button>, 
  vous devez la marquer comme termin�e avec le bouton <button type="button" class="cancel notext"></button> 
  sur la colonne de droite pour l'enlever de cet �cran.<br />
  
  Ce m�me bouton agit <strong>aussi sur les lignes non re�ues</strong>, qui par exemple ne seront jamais � recevoir.<br />
  
  Notez que vous pouvez aussi effectuer cette action de <strong>"Terminer la ligne" par lot</strong> en cliquant sur le m�me bouton en haut de la colonne de droite.
</div>

<div style="float: left;">
  <button class="print" onclick="printReceptionReport({{$service_id}})">{{tr}}Print{{/tr}}</button>
  <button class="change" onclick="getForm('filter-reception').onsubmit()">{{tr}}Refresh{{/tr}}</button>
</div>

{{mb_include module=system template=inc_pagination change_page="changeReceptionPage" 
    total=$deliveries_count current=$start step=30}}
    
<form name="filter-reception" action="?" method="get" onsubmit="return (checkForm(this) && refreshReceptions())">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="start" value="{{$start}}" onchange="refreshReceptions()" />
</form>

<table class="tbl">
  <tr>
    <th>{{*tr}}CProductDelivery-service_id{{/tr*}}Pour</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th colspan="2">Date commande</th>
    <th>Demandeur</th>
    <th style="white-space: normal;">{{tr}}CProductDelivery-_initial_quantity-court{{/tr}}</th>
    <th>{{tr}}CProductDelivery-quantity-court{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>
      <button type="button" onclick="receiveAll('list-reception')" class="tick compact">Tout recevoir</button>
    </th>
    <th class="narrow">
      <button type="button" onclick="terminateAll('list-reception')" class="cancel notext compact">Tout marquer comme termin� (ne met pas � jour le stock du service)</button>
    </th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    <tr>
      <td>
        {{if $curr_delivery->patient_id}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_ref_sejour->_guid}}')">
            {{$curr_delivery->_ref_patient->_view}}
          </span>
        {{else}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_ref_service->_guid}}')">
            {{$curr_delivery->_ref_service->_view}}
          </span>
        {{/if}}
      </td>
      <td>
        {{if $curr_delivery->stock_id}}
          {{if $curr_delivery->comments || $curr_delivery->comments_deliver}}
            <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">
              {{if $curr_delivery->comments}}
                <fieldset>
                  <legend>Commentaires</legend>
                  {{$curr_delivery->comments|nl2br}}
                </fieldset>
              {{/if}}
              {{if $curr_delivery->comments_deliver}}
                <fieldset>
                  <legend>Commentaires validateur</legend>
                  {{$curr_delivery->comments_deliver|nl2br}}
                </fieldset>
              {{/if}}
            </div>
            
            <img style="float: right;" src="style/mediboard/images/buttons/comment.png" onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')" />
          {{/if}}
          
          {{if $curr_delivery->_ref_stock->canEdit()}}
          <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$curr_delivery->_ref_stock->_id}}">
          {{/if}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_ref_stock->_ref_product->_guid}}')">{{$curr_delivery->_ref_stock}}</span>
          {{if $curr_delivery->_ref_stock->canEdit()}}
          </a>
          {{/if}}
        {{else}}
          {{mb_value object=$curr_delivery field=comments}}
        {{/if}}
      </td>
      <td style="text-align: center;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_guid}}')">
          {{mb_ditto name=date value=$curr_delivery->date_dispensation|date_format:$conf.date}}
        </span>
      </td>
      <td style="text-align: center;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_guid}}')">
          {{mb_ditto name=time value=$curr_delivery->date_dispensation|date_format:$conf.time}}
        </span>
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_delivery->_ref_preparateur}}
      </td>
      <td style="text-align: right">
        {{if $curr_delivery->_initial_quantity != $curr_delivery->quantity}}
          {{mb_value object=$curr_delivery field=_initial_quantity}}
        {{/if}}
      </td>
      <td style="text-align: right">{{mb_value object=$curr_delivery field=quantity}}</td>
      <td>{{mb_value object=$curr_delivery->_ref_stock->_ref_product field=_unit_title}}</td>
      <td style="padding: 0;">
        <table class="layout">
        {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
          <tr>
            <td>
              {{assign var=id value=$trace->_id}}
              <form name="delivery-trace-{{$id}}-receive" action="?" method="post" onsubmit="return receiveLine(this)">
                <input type="hidden" name="m" value="dPstock" /> 
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
                <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
                {{if !$trace->date_reception}}
                  {{mb_field object=$trace field=quantity increment=1 form=delivery-trace-$id-receive size=2}}
                  {{mb_field object=$trace field=code size=10 title="Code"}}
                  <input type="hidden" name="date_reception" value="now" />
                  <button type="submit" class="tick notext compact">Recevoir</button>
                {{else}}
                  <button type="submit" class="cancel notext compact">Annuler</button>
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$trace->_guid}}')">
                    {{mb_value object=$trace field=date_reception}} - 
                    <strong>{{mb_value object=$trace field=quantity}} �l�ments</strong>
                    {{if $trace->code}}
                      [{{mb_value object=$trace field=code}}]
                    {{/if}}
                  </span>
                  <input type="hidden" name="_unreceive" value="1" />
                {{/if}}
              </form>
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td class="empty">Pas encore sorti de la pharmacie</td>
          </tr>
        {{/foreach}}
        </table>
      </td>
      <td>
        <form name="delivery-force-{{$curr_delivery->_id}}-receive" action="?" method="post" 
              class="force {{if $curr_delivery->isReceived()}}valid{{/if}}"
              onsubmit="return onSubmitFormAjax(this, {onComplete: refreshReceptions})">
          <input type="hidden" name="m" value="dPstock" /> 
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_delivery_aed" />
          {{mb_key object=$curr_delivery}}
          <input type="hidden" name="date_delivery" value="now" />
          <button type="submit" class="cancel notext compact">Marquer comme termin�</button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>

<script type="text/javascript">
  $$('a[href=#list-reception] small').first().update('({{$deliveries_count}})');
</script>