{{* $Id: view_messages.tpl 7622 2009-12-16 09:08:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Plan horaire</h1>

<table class="tbl">
  <tr>
    <th colspan="2">
      {{mb_title class=CViewSender field=name}} /
      {{mb_title class=CViewSender field=description}}
    </th>
    <th colspan="2">
      {{mb_title class=CViewSender field=params}}
    </th>
    <th class="narrow" colspan="2">
      {{tr}}CViewSender-back-sources_link{{/tr}}
    </th>
    <th class="narrow">
      {{mb_title class=CViewSender field=period}}
    </th>
    <th class="narrow">
      {{mb_title class=CViewSender field=offset}}
    </th>
    <th colspan="60">
      {{tr}}CViewSender-_hour_plan{{/tr}}
    </th>
  </tr>

  {{foreach from=$senders item=_sender name=senders}}
    <!-- Bilan horaire -->
    {{if $smarty.foreach.senders.iteration % 20 == 1 || $smarty.foreach.senders.last}}
      <tr style="height: 2px; border-top: 2px solid #888;"></tr>
      <tr>
        <td colspan="8" style="text-align: right;">
          <strong>Bilan horaire: {{$hour_total|percent}}</strong>
        </td>
        {{foreach from=$hour_sum key=min item=sum}}

          {{assign var=status value=""}}
          {{if $sum  > 0}}{{assign var=status value=ok     }}{{/if}}
          {{if $sum >= 1}}{{assign var=status value=warning}}{{/if}}
          {{if $sum >= 2}}{{assign var=status value=error  }}{{/if}}

          {{assign var=active value=""}}
          {{if $sum && $min == $minute}}{{assign var=active value=active}}{{/if}}

          <td class="hour-plan {{$status}} {{$active}}" title="{{$sum|percent}} @ {{$min}}" style="height: 2em;"></td>
        {{/foreach}}
      </tr>
    {{/if}}

    {{assign var=senders_source value=$_sender->_ref_senders_source}}
    <tr {{if !$_sender->active}} class="hatching" {{/if}}>
      <td class="narrow">
        <button class="edit notext" onclick="ViewSender.edit('{{$_sender->_id}}');">
          {{tr}}Edit{{/tr}}
        </button> 
      </td>
      <td class="text">
        <div {{if ($_sender->_active)}} style="font-weight: bold;"{{/if}}>
          {{mb_value object=$_sender field=name}}
        </div>
        <div class="compact">
          {{mb_value object=$_sender field=description}}
        </div>
        <div class="compact">
          {{$_sender->last_duration|string_format:"%.3f"}}s /
          {{$_sender->last_size|decabinary}}
        </div>
      </td>
      <td class="narrow">
        <script type="text/javascript">
          ViewSender.senders['{{$_sender->_id}}'] = {{$_sender->_params|@json}};
        </script>
        <button class="search notext" onclick="ViewSender.show('{{$_sender->_id}}');">
          {{tr}}View{{/tr}}
        </button>
      </td>
      <td class="text compact">
        {{foreach from=$_sender->_params key=_param item=_value name=params}}
          {{if $smarty.foreach.params.iteration < 4}}
          <div>
            {{$_param}} = {{$_value}}
            {{if $smarty.foreach.params.iteration == 3 && count($_sender->_params) > 3 }}
            ...
            {{/if}}
          </div>
          {{/if}}
        {{/foreach}}
      </td>
      <td>          
        <button class="add notext" onclick="SourceToViewSender.edit('{{$_sender->_id}}');">
          {{tr}}Add{{/tr}}
        </button> 
      </td>
      <td>
        {{foreach from=$senders_source item=_sender_source}}
        {{assign var=source value=$_sender_source->_ref_source}}

        <div>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sender_source->_guid}}');">
            {{$source}}
          </span>
        </div>
        {{foreachelse}}
        <div class="empty">{{tr}}CViewSender-back-sources_link.empty{{/tr}}</div>
        {{/foreach}}
      </td>
      <td class="text" style="text-align: right; padding-right: 0.5em;">
        {{mb_value object=$_sender field=period}}
        {{if $_sender->every > 1}}
          {{mb_label object=$_sender field=every}}
          {{mb_value object=$_sender field=every}}
        {{/if}}
      </td>
      <td style="text-align: right; padding-right: 0.5em;">
        {{mb_value object=$_sender field=offset}}mn
      </td>

      {{assign var=status value=$_sender->active|ternary:"ok":"off"}}

      {{foreach from=$_sender->_hour_plan key=min item=plan}}
        {{assign var=status value=off}}
        {{if $plan  > 0.0}}{{assign var=status value=ok     }}{{/if}}
        {{if $plan >= 0.5}}{{assign var=status value=warning}}{{/if}}
        {{if $plan >= 1.0}}{{assign var=status value=error  }}{{/if}}

        {{assign var=active value=""}}
        {{if ($min == $minute && $_sender->_active)}}{{assign var=active value=active}}{{/if}}

        {{assign var=partial value=""}}
        {{if $_sender->every > 1}}{{assign var=partial value=partial}}{{/if}}

        <td class="hour-plan min-{{$min}} {{$status}} {{$active}} {{$partial}}" title="{{$plan|percent}} @ {{$min}}"></td>
      {{/foreach}}
    </tr>

  {{foreachelse}}
    <tr>
      <td class="empty" colspan="65">{{tr}}CViewSender.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
