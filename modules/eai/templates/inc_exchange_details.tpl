{{*
 * View Exchanges XML Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $exchange->_message === null && $exchange->_acquittement === null}}
  <div class="small-info">{{tr}}{{$exchange->_class}}-purge-desc{{/tr}}</div>
{{else}}
<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>
<tr>
  <td>
    <ul id="tabs-contenu" class="control_tabs">
      <li> 
        <a href="#message"> 
          {{mb_title object=$exchange field="_message"}} 
          <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&message=1')"></button>
        </a> 
      </li>
      <li> 
        <a href="#ack" {{if !$exchange->_acquittement}}class="empty"{{/if}}>
          {{mb_title object=$exchange field="_acquittement"}}
          <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&ack=1')"></button>
          </a> 
      </li>
    </ul>
    
    <hr class="control_tabs" />
    
    <div id="message" style="display: none;">
      {{mb_value object=$exchange field="_message"}}  
      {{if $exchange->message_valide != 1 && count($exchange->_doc_errors_msg) > 0}}
      <div class="big-error">
        <strong>{{tr}}CExchange-message-invalide{{/tr}}</strong> <br />
        <ul>
        {{foreach from=$exchange->_doc_errors_msg item=_error}}
          <li>{{$_error}}</li> 
         {{/foreach}} 
        </ul>
      </div>
      {{/if}}
    </div>
    
    
    <div id="ack" style="display: none;">
      {{if $exchange->message_valide == 1 || $exchange->acquittement_valide == 1}}
        {{if $exchange->_acquittement}}
          {{mb_value object=$exchange field="_acquittement"}}
          
          {{mb_include module=$exchange->_ref_module->mod_name template="`$exchange->_class`_observations_inc"}}
        {{else}}
          <div class="big-info">{{tr}}CExchange-no-acquittement{{/tr}}</div>
        {{/if}}
      {{else}}
        {{if count($exchange->_doc_errors_ack) > 0}}
          <div class="big-error">
            <strong>{{tr}}CExchange-acquittement-invalide{{/tr}}</strong> <br />
            <ul>
            {{foreach from=$exchange->_doc_errors_ack item=_error}}
              <li>{{$_error}}</li> 
             {{/foreach}} 
            </ul>
          </div>
        {{else}}
          <div class="big-info">{{tr}}CExchange-no-acquittement{{/tr}}</div>
        {{/if}}
      {{/if}}
    </div>
  </td>
</tr> 
{{/if}}