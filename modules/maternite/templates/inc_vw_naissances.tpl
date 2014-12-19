{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{unique_id var=forms_uid}}

<script>
Main.add(function(){
  {{if "forms"|module_active}}
    ExObject.loadExObjects("{{$grossesse->_class}}", "{{$grossesse->_id}}", "list-ex_objects-{{$forms_uid}}", 0.5);
  {{/if}}
});
</script>

<form name="createProvisoire" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() { refreshGrossesse('{{$operation->_id}}'); } });">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="dosql" value="do_dossier_provisoire_aed" />
  <input type="hidden" name="operation_id" value="{{$operation->_id}}"/>
  <input type="hidden" name="sejour_maman_id" value="{{$operation->sejour_id}}"/>
</form>
<table class="tbl">
  <tr>
    <th class="title" colspan="5">
      <button type="button" class="add" style="float: left;" {{if !$grossesse->active}}disabled="disabled"{{/if}}
        onclick="Naissance.edit(0, '{{$operation->_id}}', '{{$operation->sejour_id}}')">Naissance</button>
      {{if $conf.maternite.CGrossesse.manage_provisoire}}
          <button type="button" class="add" style="float: left;" {{if !$grossesse->active}}disabled="disabled"{{/if}}
            onclick="getForm('createProvisoire').onsubmit()">Dossier provisoire</button>
      {{/if}}
      <form name="closeGrossesse" method="post"
        onsubmit="return onSubmitFormAjax(this, {onComplete: function() { refreshGrossesse('{{$operation->_id}}'); } });">
        <input type="hidden" name="m" value="maternite" />
        {{mb_class object=$grossesse}}
        {{mb_key   object=$grossesse}}
        {{if $grossesse->active}}
          <input type="hidden" name="active" value="0" />
          <button type="button" class="tick" onclick="this.form.onsubmit()" style="float: right;">{{tr}}CGrossesse-stop_grossesse{{/tr}}</button>
        {{else}}
          <input type="hidden" name="active" value="1" />
          <button type="button" class="cancel" onclick="this.form.onsubmit()" style="float: right;">{{tr}}CGrossesse-reactive_grossesse{{/tr}}</button>
        {{/if}}
      </form>
      Naissances
    </th>
  </tr>
  <tr>
    <th class="category"></th>
    <th class="category">{{mb_label class=CNaissance field=rang}} / {{mb_label class=CNaissance field=date_time}}</th>
    <th class="category">{{tr}}CPatient{{/tr}}</th>
    <th class="category">{{tr}}CSejour{{/tr}}</th>
  </tr>
  {{foreach from=$operation->_ref_sejour->_ref_grossesse->_ref_naissances item=_naissance}}
    {{assign var=sejour_enfant value=$_naissance->_ref_sejour_enfant}}
    {{assign var=enfant value=$sejour_enfant->_ref_patient}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="Naissance.edit('{{$_naissance->_id}}', '{{$operation->_id}}')"></button>
      </td>
      <td>
        {{if $_naissance->date_time}}
          Le {{$_naissance->date_time|date_format:$conf.date}} � {{$_naissance->date_time|date_format:$conf.time}}
        {{else}}
          {{$_naissance}}
        {{/if}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$enfant->_guid}}')">{{$enfant}}</span>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour_enfant->_guid}}')">{{$sejour_enfant->_shortview}}</span>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">
        {{tr}}CNaissance.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>

{{if "forms"|module_active}}
  <table class="tbl">
    <tr>
      <th class="title">Formulaires</th>
    </tr>
  </table>
  <div id="list-ex_objects-{{$forms_uid}}"></div>
{{/if}}
