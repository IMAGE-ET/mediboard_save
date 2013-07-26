{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=field_name value=_function_secondary_id}}
{{mb_default var=selected value=""}}
{{mb_default var=empty_function_principale value=0}}
{{mb_default var=type_onchange value="consult"}}
{{mb_default var=change_active value="1"}}

{{if $change_active}}
  <script type="text/javascript">
    secondaryChange = function(type, elt) {
      if (!type) {
        return;
      }
      switch(type) {
        case "consult":
          var form = elt.form;
          var facturable = elt.options[elt.selectedIndex].get('facturable');
          form.___facturable.checked = facturable == '1' ? 'checked' : '';
          $V(elt.form._facturable, facturable);
          break;
        case "sejour":
          var form = getForm("editSejour");
          var facturable = elt.options[elt.selectedIndex].get('facturable');
          form.facturable[facturable == '1' ? 0 : 1].checked = "checked";
      }
    };
    {{if $change_active}}
      Main.add(function() {
        var select = $("select_secondary_func");
        if (select) {
          select.onchange();
        }
      });
    {{/if}}
  </script>
{{/if}}

{{if $chir->_id && $chir->isPraticien()}}
  <select {{if $change_active}}id="select_secondary_func"{{/if}}  name="{{$field_name}}" style="width: 15em;"
    {{if $change_active}}onchange="secondaryChange('{{$type_onchange}}', this)"{{/if}}>
      <option
        {{if $empty_function_principale}}
          value=""
        {{else}}
          value="{{$chir->function_id}}"
        {{/if}}
         data-facturable="{{$chir->_ref_function->facturable}}">{{$chir->_ref_function}} ({{$chir->_ref_function->_ref_group}})</option>
    {{foreach from=$_functions item=_function}}
      <option value="{{$_function->function_id}}" data-facturable="{{$_function->_ref_function->facturable}}"
        {{if $_function->function_id == $selected}}selected{{/if}}>{{$_function}} ({{$_function->_ref_function->_ref_group}})</option>
    {{/foreach}}
  </select>
{{else}}
  <div class="small-info">
    {{tr}}CConsultation-choose_prat{{/tr}}
  </div>
{{/if}}