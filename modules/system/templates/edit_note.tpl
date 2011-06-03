{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return Note.submit(this);">

<input type="hidden" name="dosql" value="do_note_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="m" value="system" />
{{mb_key object=$note}}

{{mb_field object=$note field="object_id" hidden=1}}
{{mb_field object=$note field="object_class" hidden=1}}

<table class="form">
  <tr>
    {{if $note->_id}}
    <th class="title modify" colspan="2">
      Modifier une note &mdash; {{$note->_ref_object->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
      Cr�er une note &mdash; {{$note->_ref_object->_view}}
    </th>
    {{/if}}
  </tr>
  <tr>
    <th>{{mb_label object=$note field="user_id"}}</th>
    <td>
      {{$note->_ref_user->_view}} &mdash; {{$note->_ref_user->_ref_function->_view}}
      {{mb_field object=$note field="user_id" hidden=1}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="date"}}</th>
    <td>{{mb_field object=$note field="date" form=editFrm register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="public"}}</th>
    <td>{{mb_field object=$note field="public"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="degre"}}</th>
    <td>{{mb_field object=$note field="degre" typeEnum=radio}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="libelle"}}</th>
    <td>{{mb_field object=$note field="libelle"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="text"}}</th>
    <td>{{mb_field object=$note field="text"}}</td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      {{if $note->_id}}
      <button type="submit" class="submit">{{tr}}Modify{{/tr}}</button>
      <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$note->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button type="submit" class="submit singleclick">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>

</form>