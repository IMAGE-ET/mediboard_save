{{mb_script module=salleOp script=check_list}}

<script>
Main.add(Control.Tabs.create.curry("list_type_tabs", true));
</script>

<table class="main layout">
  <tr>
    <td style="width: 50%;">
      <ul class="control_tabs" id="list_type_tabs">
        {{foreach from=$by_type key=_type item=_list_by_object}}
          <li>
            <a href="#tab-{{$_type}}">
              {{tr}}CDailyCheckListType.type.{{$_type}}{{/tr}}
            </a>
          </li>
        {{/foreach}}
      </ul>

      {{foreach from=$by_type key=_type item=_lists}}
        <div id="tab-{{$_type}}" style="display: none;">
          <a class="button new" href="?m=salleOp&amp;tab=vw_daily_check_list_type&amp;list_type_id=0&amp;type={{$_type}}&amp;dialog={{$dialog}}">
            {{tr}}CDailyCheckListType-title-create{{/tr}}
          </a>

          <table class="main tbl">
            <tr>
              <th>{{mb_title class=CDailyCheckListType field=title}}</th>
              <th>{{mb_title class=CDailyCheckListType field=description}}</th>
              <th>{{mb_title class=CDailyCheckListType field=_links}}</th>
              <th>{{tr}}CDailyCheckListType-back-daily_check_list_categories{{/tr}}</th>
            </tr>

            {{foreach from=$_lists item=_list}}
              <tr {{if $_list->_id == $list_type->_id}} class="selected" {{/if}}>
                <td>
                  <a href="?m=salleOp&amp;tab=vw_daily_check_list_type&amp;list_type_id={{$_list->_id}}&amp;dialog={{$dialog}}">
                    {{mb_value object=$_list field=title}}
                  </a>
                </td>
                <td class="compact">{{mb_value object=$_list field=description}}</td>
                <td class="compact">
                  {{foreach from=$_list->_ref_type_links item=_link}}
                    {{if $_link->object_id}}
                      {{$_link->_ref_object}}
                    {{else}}
                      <em> &ndash; {{tr}}All{{/tr}}</em>
                    {{/if}}
                    <br />
                  {{/foreach}}
                </td>
                <td>{{$_list->_count.daily_check_list_categories}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="4" class="empty">
                  {{tr}}CDailyCheckListType.none{{/tr}}
                </td>
              </tr>
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>

    <td>
      <form name="edit-CDailyCheckListType" action="?m=salleOp&amp;tab=vw_daily_check_list_type&amp;dialog={{$dialog}}" method="post" onsubmit="return checkForm(this)">
        {{mb_class object=$list_type}}
        {{mb_key   object=$list_type}}
        {{mb_field object=$list_type field=group_id hidden=true}}
        <input type=hidden name="_duplicate" value="0"/>

        <table class="main form">
        {{mb_include module=system template=inc_form_table_header object=$list_type}}
          <tr>
            <th class="narrow">{{mb_label object=$list_type field="title"}}</th>
            <td>{{mb_field object=$list_type field="title"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$list_type field="type"}}</th>
            <td>
              <select name="type" class="str notNull" onchange="$$('.object_id-list').invoke('hide'); $('type_view-'+$V(this)).show();">
                {{foreach from=$targets key=_type item=_targets}}
                  <option value="{{$_type}}" {{if $_type == $list_type->type}} selected {{/if}}>
                    {{tr}}CDailyCheckListType.type.{{$_type}}{{/tr}}
                  </option>
                {{/foreach}}
              </select>

              <input type="hidden" name="_links[dummy]" value="dummy-dummy" />

              {{foreach from=$targets key=_type item=_targets}}
                <table clas="main layout object_id-list" id="type_view-{{$_type}}" {{if $_type != $list_type->type}} style="display: none;" {{/if}}>
                  {{foreach from=$_targets key=_id item=_target}}
                    <tr>
                      <td>
                        <label>
                          <input type="checkbox" name="_links[{{$_target->_guid}}]" value="{{$_target->_guid}}"
                            {{if array_key_exists($_target->_guid,$list_type->_links)}} checked {{/if}}/>
                          {{if $_id == 0}}
                            {{tr}}All{{/tr}}
                          {{else}}
                            {{$_target}}
                          {{/if}}
                        </label>
                      </td>
                      <td>
                        {{if $_target->_id}}
                          <button type="button" class="compact lookup notext" onclick="CheckList.preview('{{$_target->_class}}', '{{$_target->_id}}')">
                            {{tr}}Preview{{/tr}}
                          </button>
                        {{/if}}
                      </td>
                    </tr>
                  {{/foreach}}
                </table>
              {{/foreach}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$list_type field="type_validateur"}}</th>
            <td>{{mb_field object=$list_type field="type_validateur"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$list_type field="description"}}</th>
            <td>{{mb_field object=$list_type field="description"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>

              {{if $list_type->_id}}
                <button type="button" class="trash" onclick="confirmDeletion(
                  this.form,
                  {ajax: true, typeName:'',objName:'{{$list_type->_view|smarty:nodefaults|JSAttribute}}'},
                  {onComplete: Control.Modal.close}
                )">
                  {{tr}}Delete{{/tr}}
                </button>
                <button class="duplicate" type="button" onclick="this.form._duplicate.value = '1';this.form.submit();">
                  {{tr}}Duplicate{{/tr}}
                </button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>

      {{if $list_type->_id}}
        <table class="main tbl">
          <tr>
            <th colspan="3" class="title">Cat�gories</th>
          </tr>

          <tr>
            <th class="narrow">{{mb_title class=CDailyCheckItemCategory field=index}}</th>
            <th>{{mb_title class=CDailyCheckItemCategory field=title}}</th>
            <th>{{mb_title class=CDailyCheckItemCategory field=desc}}</th>
          </tr>

          {{foreach from=$list_type->_ref_categories item=_category}}
            <tr>
              <td>{{mb_value object=$_category field=index}}</td>
              <td>
                <a href="#1" onclick="CheckList.editItemCategory('{{$list_type->_id}}', '{{$_category->_id}}')" >
                  {{mb_value object=$_category field=title}}
                </a>
              </td>
              <td class="compact">{{mb_value object=$_category field=desc}}</td>
            </tr>
          {{foreachelse}}
            <tr>
              <td colspan="3" class="empty">{{tr}}CDailyCheckItemCategory.none{{/tr}}</td>
            </tr>
          {{/foreach}}
          <tr>
            <td colspan="3">
              <button class="new compact" onclick="CheckList.editItemCategory('{{$list_type->_id}}', 0)" >
                {{tr}}CDailyCheckItemCategory-title-create{{/tr}}
              </button>
            </td>
          </tr>
        </table>
      {{/if}}
    </td>
  </tr>
</table>