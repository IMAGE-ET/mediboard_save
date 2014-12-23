{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("editSubgroup");
  form.elements.title.select();
  ExFieldPredicate.initAutocomplete(form, '{{$ex_group->ex_class_id}}');
});
</script>

<form name="editSubgroup" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_group->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  {{mb_key object=$ex_subgroup}}
  {{mb_class object=$ex_subgroup}}
  {{mb_field object=$ex_subgroup field=parent_class hidden=true}}
  {{mb_field object=$ex_subgroup field=parent_id hidden=true}}

  <input type="hidden" name="_ex_group_id" value="{{$ex_group->_id}}" />
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_subgroup colspan="4"}}
    
    <tr>
      <th>{{mb_label object=$ex_subgroup field=title}}</th>
      <td colspan="3">{{mb_field object=$ex_subgroup field=title size=50}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$ex_subgroup field=predicate_id}}</th>
      <td colspan="3">
        <input type="text" name="predicate_id_autocomplete_view" size="70" value="{{$ex_subgroup->_ref_predicate->_view}}" placeholder=" -- Toujours afficher -- " />
        {{mb_field object=$ex_subgroup field=predicate_id hidden=true}}
        <button class="new notext" onclick="ExFieldPredicate.create(null, null, this.form)" type="button">
          {{tr}}New{{/tr}}
        </button>
      </td>
    </tr>
    
    {{if $ex_group->_ref_ex_class->pixel_positionning}}
    <tr>
      <th class="narrow">{{mb_label object=$ex_subgroup field=coord_left}}</th>
      <td class="narrow">{{mb_field object=$ex_subgroup field=coord_left increment=true form=editSubgroup}}</td>
      <th class="narrow">{{mb_label object=$ex_subgroup field=coord_top}}</th>
      <td>{{mb_field object=$ex_subgroup field=coord_top increment=true form=editSubgroup}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ex_subgroup field=coord_width}}</th>
      <td>{{mb_field object=$ex_subgroup field=coord_width increment=true form=editSubgroup}}</td>
      <th>{{mb_label object=$ex_subgroup field=coord_height}}</th>
      <td>{{mb_field object=$ex_subgroup field=coord_height increment=true form=editSubgroup}}</td>
    </tr>
    {{/if}}
    
    <tr>
      <th></th>
      <td colspan="3">
        <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>

        {{if $ex_subgroup->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le sous groupe ',objName:'{{$ex_subgroup->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{mb_include module=forms template=inc_list_entity_properties object=$ex_subgroup}}