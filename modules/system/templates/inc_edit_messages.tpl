{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_message_aed" />
<input type="hidden" name="message_id" value="{{$message->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">

<tr>
  {{if $message->_id}}
  <th class="title modify" colspan="2">
    {{mb_include module=system template=inc_object_idsante400 object=$message}}
    {{mb_include module=system template=inc_object_history object=$message}}

    {{tr}}CMessage-title-modify{{/tr}} '{{$message}}'
  {{else}}
  <th class="title" colspan="2">
    {{tr}}CMessage-title-create{{/tr}}
  {{/if}}
  </th>
</tr>

<tr>
  <th class="narrow">{{mb_label object=$message field="deb"}}</th>
  <td>{{mb_field object=$message field="deb" form="editFrm" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="fin"}}</th>
  <td>{{mb_field object=$message field="fin" form="editFrm" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="module_id"}}</th>
  <td>
    <select name="module_id">
      <option value="">&mdash; {{tr}}All{{/tr}}</option>
      {{foreach from=$modules item=mod}}
        <option value="{{$mod->_id}}" {{if $mod->_id == $message->module_id}}selected="selected"{{/if}}>{{$mod}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$message field="group_id"}}</th>
  <td>
    <select name="group_id">
      <option value="">&mdash; {{tr}}All{{/tr}}</option>
      {{foreach from=$groups item=_group}}
        <option value="{{$_group->_id}}" {{if $_group->_id == $message->group_id}}selected="selected"{{/if}}>{{$_group}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$message field="urgence"}}</th>
  <td>{{mb_field object=$message field="urgence"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="titre"}}</th>
  <td>{{mb_field object=$message field="titre"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="corps"}}</th>
  <td>{{mb_field object=$message field="corps"}}</td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{if $message->_id}}
    <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'message',objName:'{{$message->_view|smarty:nodefaults|JSAttribute}}'})">
      {{tr}}Delete{{/tr}}
    </button>
    {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
</tr>

</table>

</form>
