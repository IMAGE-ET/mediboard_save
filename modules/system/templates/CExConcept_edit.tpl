{{if $object->_id}}
  <button type="button" class="new" onclick="MbObject.edit('{{$object->_class}}-0')">
    {{tr}}{{$object->_class}}-title-create{{/tr}}
  </button>
{{/if}}

{{mb_script module=forms script=ex_class_editor ajax=true}}

<script type="text/javascript">
switchType = function(select) {
  $$(".switch-type").each(function(type){
    type.hide().disableInputs();
  });

  $("switch-type-"+$V(select)).show().enableInputs();

  var form = getForm("edit-{{$object->_guid}}");
  ExConceptSpec.edit(form);
}

selectList = function(input) {
  if (!$V(input.form.elements.name)) {
    $V(input.form.elements.name, input.form.ex_list_id_autocomplete_view.value);
  }

  ExConceptSpec.edit(input.form);
}

Main.add(function(){
  var form = getForm("edit-{{$object->_guid}}");
  ExConceptSpec.edit(form);
  Control.Tabs.create("ex-concept-tabs", true);
  switchType(form._concept_type);

  {{if $conf.forms.CExConcept.native_field}}
    var url = new Url("forms", "ajax_autocomplete_native_fields");
    url.autoComplete(form.elements._native_field_view, null, {
      minChars: 2,
      method: "get",
      dropdown: true,
      //width: "550px",
      afterUpdateElement: function(field, selected){
        $V(field.form.elements.native_field, selected.get("value"));
        $V(field.form.elements._native_field_view, selected.down(".view").getText().replace(/\s+/g, ' ').strip());
      }
    });
  {{/if}}
});
</script>

<form name="edit-{{$object->_guid}}" data-object_guid="{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}

  <input type="hidden" name="callback" value="MbObject.editCallback" />
  
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header css_class="text" colspan=2}}
    
    {{if $object->_id}}
      {{mb_include module=system template=inc_tag_binder colspan=2}}
    {{/if}}
    
    <tr>
      <th class="narrow">{{mb_label object=$object field=name}}</th>
      <td>{{mb_field object=$object field=name size=40}}</td>
    </tr>

    {{if $conf.forms.CExConcept.native_field}}
      <tr>
        <th>
          {{mb_label object=$object field=native_field}}
        </th>
        <td>
          {{*if !$object->_id*}}
          {{mb_field object=$object field=native_field hidden=true}}
          <input type="text" name="_native_field_view" value="{{$object->_native_field_view}}" size="60" />
          <button class="cancel notext" type="button" onclick="$V(this.form._native_field_view,'');$V(this.form.native_field,'');">{{tr}}Empty{{/tr}}</button>
          {{*else}}
            {{mb_value object=$object field=native_field}}
            {{mb_field object=$object field=native_field hidden=true}}
          {{/if*}}
        </td>
      </tr>
    {{/if}}

    <tr>
      <th>
        {{assign var=concept_type value="custom"}}

        {{if $object->ex_list_id}}
          {{assign var=concept_type value="list"}}
        {{/if}}

        {{if !$object->_id}}
          <select onchange="switchType(this)" name="_concept_type">
            <option value="list"   {{if $concept_type == "list"}}   selected {{/if}}>{{tr}}CExConcept-ex_list_id{{/tr}}</option>
            <option value="custom" {{if $concept_type == "custom"}} selected {{/if}}>Type</option>
          </select>
        {{else}}
          <label>
            {{if $concept_type == "list"}}{{tr}}CExConcept-ex_list_id{{/tr}}{{/if}}
            {{if $concept_type == "custom"}}Type{{/if}}
          </label>
        {{/if}}
      </td>

      <td>
        {{* LIST *}}
        <div class="switch-type" id="switch-type-list" {{if $concept_type != "list"}} style="display: none;" {{/if}}>
          {{if !$object->_id}}
            {{assign var=_prop value=$object->_props.ex_list_id}}
            {{mb_label object=$object field=ex_list_id style="display: none;"}}
            {{mb_field object=$object field=ex_list_id prop="$_prop notNull" form="edit-`$object->_guid`" autocomplete="true,1,50,true,true" onchange="selectList(this)"}}
            <button class="new" onclick="ExList.createInModal()" type="button">{{tr}}CExList-title-create{{/tr}}</button>
            <label>
              <input type="checkbox" name="_multiple" value="1" onclick="ExConceptSpec.edit(this.form)" /> Choix multiple
            </label>
          {{else}}
            {{mb_value object=$object field=ex_list_id}}
            {{mb_field object=$object field=ex_list_id hidden=true}}
          {{/if}}
        </div>

        {{* CUSTOM *}}
        <div class="switch-type" id="switch-type-custom" {{if $concept_type != "custom"}} style="display: none;" {{/if}}>
          {{assign var=spec_type value=$object->_concept_spec->getSpecType()}}

          {{if !$object->_id}}
            <select name="_spec_type" onchange="ExConceptSpec.edit(this.form)">
              {{foreach from="CExClassField::getTypes"|static_call:null key=_key item=_class}}
                {{if !$conf.forms.CExConcept.force_list || ($_key != "enum" && $_key != "set")}}
                  <option value="{{$_key}}" {{if $_key == $spec_type && !$object->ex_list_id}}selected="selected"{{/if}}>
                    {{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}
                  </option>
                {{/if}}
              {{/foreach}}
            </select>
          {{else}}
            <input type="hidden" name="_spec_type" value="{{$spec_type}}" />

            {{if !$object->ex_list_id}}
              {{tr}}CMbFieldSpec.type.{{$spec_type}}{{/tr}}
            {{/if}}
          {{/if}}
        </div>
      </th>
    </tr>

    <tr>
      <th></th>
      <td>
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

        {{if $object->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>

    <tr {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}>
      <th></th>
      <td>
        {{mb_field object=$object field=prop readonly=true size=80}}
      </td>
    </tr>
  </table>
</form>

<ul id="ex-concept-tabs" class="control_tabs">
  <li>
    <a href="#ExConcept-spec-editor">Paramètres</a>
  </li>
  {{if $object->_id}}
  <li>
    <a href="#ex-back-class_fields" {{if $object->_back.class_fields|@count == 0}} class="empty" {{/if}}>
      {{tr}}CExConcept-back-class_fields{{/tr}} <small>({{$object->_back.class_fields|@count}})</small>
    </a>
  </li>
  {{/if}}
</ul>
<hr class="control_tabs" />

<div id="ExConcept-spec-editor" style="display: none;"></div>

{{if $object->_id}}
<div id="ex-back-class_fields" style="display: none;">
  <table class="main tbl">
    <tr>
      <th>{{tr}}CExClass{{/tr}}</th>
      <th>{{tr}}CExClassFieldGroup{{/tr}}</th>
      <th>{{tr}}CExClassField{{/tr}}</th>
    </tr>
    
    {{foreach from=$object->_back.class_fields item=_field}}
      <tr>
        <td>
          {{mb_value object=$_field->_ref_ex_group->_ref_ex_class field=name}}
        </td>
        <td>
          {{mb_value object=$_field->_ref_ex_group field=name}}
        </td>
        <td>
          {{mb_value object=$_field field=_locale}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="3">{{tr}}CExClassField.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>
{{/if}}
