{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  onSubmitForm = function(form) {
    if (!checkForm(form)) {
      return false;
    }
    var ok = onSubmitFormAjax(form);
    PlageAstreinte.refreshList();
    Control.Modal.close();
    return ok;
  }
</script>

<form name="editplage" action="" method="post" onsubmit="return onSubmitForm(this); ">
  {{mb_key object=$plageastreinte}}
  <input type="hidden" name="dosql" value="do_plageastreinte_aed" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$a}}" />
  <table class="form">
    {{if $plageastreinte->_id}}
      <tr>
        <th class = "title modify text" colspan="6">
          {{mb_include module=system template=inc_object_notes   object=$plageastreinte}}
          {{mb_include module=system template=inc_object_history object=$plageastreinte}}
          {{tr}}CPlageAstreinte-title-modify{{/tr}} {{$plageastreinte}}
        </th>
      </tr>
    {{else}}
      <tr>
        <th class= "title text" colspan="6">
         {{tr}}CPlageAstreinte-title-create{{/tr}}
        </th>
      </tr>
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$plageastreinte field="user_id"}}
      </th>
      <td>
        <select name="user_id">
          <option value="">{{tr}}CMediusers.all{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$plageastreinte->user_id}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$plageastreinte field="libelle"}}</th>
      <td>{{mb_field object=$plageastreinte field="libelle"}}</td>
    </tr>
      <tr>
        <th>{{mb_label object=$plageastreinte field="type"}}</th>
        <td>{{mb_field object=$plageastreinte field="type"}}</td>
      </tr>
    <tr>
      <th>{{mb_label object=$plageastreinte field="start"}}</th>
      <td>{{mb_field object=$plageastreinte field="start" form="editplage" register="true"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$plageastreinte field="end"}}</th>
      <td>{{mb_field object=$plageastreinte field="end" form="editplage" register="true"}}</td>
    </tr>
      <tr>
        <th>{{mb_label object=$plageastreinte field="phone_astreinte"}}</th>
        <td>{{mb_field object=$plageastreinte field="phone_astreinte" form="editplage"}}</td>
      </tr>
    <tr>
      <td colspan="6" class="button">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
        {{if $plageastreinte->_id}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plageastreinte->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
    {{if @count($plageastreinte->_collisionList)}}
      <div class="small-warning">
        {{foreach from=$plageastreinte->_collisionList item=_collision}}
          {{$_collision}}
        {{/foreach}}
      </div>
    {{/if}}
</form>
