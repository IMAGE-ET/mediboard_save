{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 9174 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
resetValue = function(object_id, field_name) {
  var oForm = getForm('editObjectConfig-'+object_id);
  $V(oForm[field_name], "");
}
  
onSubmitObjectConfigs = function(oForm, object_instance_id, object_guid) {
  return onSubmitFormAjax(oForm, { onComplete: (window.refreshConfigObjectValues || InteropActor.refreshConfigObjectValues).curry(object_instance_id, object_guid) });
}
</script>

<script type="text/javascript">

importConfig = function(object_config_guid) {
  var url = new Url("system", "import_config");
  url.addParam("object_config_guid", object_config_guid);
  url.popup(800, 600, "Import config XML");
  
  return false;
}

</script>

{{if $object->_id}}
<a class="button download" target="_blank" href="?m=system&amp;a=export_config&amp;suppressHeaders=1&object_guid={{$object->_guid}}">
  {{tr}}Export{{/tr}}
</a>

<button class="upload" onclick="importConfig('{{$object->_guid}}');">
  {{tr}}Import{{/tr}}
</button>
{{/if}}

<form name="editObjectConfig-{{$object->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitObjectConfigs(this, '{{$object->object_id}}', '{{$object->_guid}}') ">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="del" value="0" /> 
  
  <input type="hidden" name="@class" value="{{$object->_class}}" /> 
  {{mb_key object=$object}}
  
  <input type="hidden" name="object_id" value="{{$object->object_id}}" /> 
  <table class="form">
    <tr>
      <th class="category">{{tr}}Name{{/tr}}</th>
      <th class="category" colspan="2">{{tr}}Value{{/tr}}</th>
      <th class="category">{{tr}}Default{{/tr}}</th>
    </tr>
    {{foreach from=$categories key=cat_name item=_fields}}
      {{if $cat_name}}
        <tr>
          <th colspan="4" class="section" style="text-align: center;">{{$cat_name}}</th>
        </tr>
      {{/if}}
      
      {{foreach from=$_fields item=_field_name}}
      <tr>
        <th>{{mb_label object=$object field=$_field_name}}</th>
        <td><button class="notext cancel" type="button" onclick="resetValue('{{$object->_id}}', '{{$_field_name}}');">{{tr}}Delete{{/tr}}</button></td>
        <td>
          {{if $object->_specs.$_field_name instanceof CEnumSpec || $object->_specs.$_field_name instanceof CBoolSpec}}
          {{mb_field object=$object field=$_field_name typeEnum=select emptyLabel="Undefined"}}
          {{else}}
          {{mb_field object=$object field=$_field_name}}
          {{/if}}
        </td>
        <td {{if $object->$_field_name !== null}}class="arretee"{{/if}}>
          {{if $object->_default_specs_values}}
            {{mb_value object=$default_config field=$_field_name}}
          {{else}}
            {{mb_value object=$default field=$_field_name}}
          {{/if}}
        </td>
      </tr>
      {{/foreach}}
    {{/foreach}}
    <tr>
      <td class="button" colspan="4">
        {{if $object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}',ajax:true})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
           <button class="submit singleclick" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>