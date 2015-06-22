{{mb_script module="system" script="object_selector"}}

<script>
changePage = function(start) {
  $V(getForm("filter-check-lists").start, start);
}

changeObject = function() {
  var form = getForm("filter-check-lists");
  $('filter-check-lists__type').hide();
  $('filter-check-lists_type').hide();

  if (form.object_guid.value == "COperation-") {
    $('filter-check-lists_type').show();
    $V(form._type, '');
  }
  else {
    $('filter-check-lists__type').show();
    $V(form.type, '');
  }
}

Main.add(function () {
  changeObject();
});
</script>
<table class="main layout">
  <tr>
    <td>
      <form name="filter-check-lists" action="?" method="get">
        <input type="hidden" name="m" value="salleOp" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="start" value="{{$start}}" onchange="this.form.submit()" />

        <table class="main form">
          <tr>
            <th>{{mb_label object=$check_list_filter field=_date_min}}</th>
            <td>{{mb_field object=$check_list_filter field=_date_min register=true form="filter-check-lists" onchange="this.form.start.value=0"}}</td>
            <th>{{mb_label object=$check_list_filter field=object_id}}</th>
            <td>
              <select name="object_guid" onchange="this.form.start.value=0;changeObject();">
                <option value="">{{tr}}All{{/tr}}</option>
                {{foreach from=$list_rooms item=list key=class}}
                  <optgroup label="{{if $class == "CBlocOperatoire"}}Salle de r�veil{{else}}{{tr}}{{$class}}{{/tr}}{{/if}}">
                    {{foreach from=$list item=room}}
                      <option value="{{$room->_guid}}" {{if $object_guid == $room->_guid}}selected="selected"{{/if}}>{{if $room->_id}}{{$room}}{{else}}Toutes{{/if}}</option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$check_list_filter field=_date_max}}</th>
            <td>{{mb_field object=$check_list_filter field=_date_max register=true form="filter-check-lists" onchange="this.form.start.value=0"}}</td>
            <th>{{mb_label object=$check_list_filter field=_type}}</th>
            <td >
              {{mb_field object=$check_list_filter field=_type emptyLabel="All"}}
              {{mb_field object=$check_list_filter field=type emptyLabel="All"}}
            </td>
          </tr>
          <tr>
            <td colspan="6" class="button">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>

      {{mb_include module=system template=inc_pagination total=$count_check_lists current=$start change_page='changePage' step=40}}

      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CDailyCheckList field=date}}</th>
          <th class="narrow">{{mb_title class=CDailyCheckList field=date_validate}}</th>
          <th>{{mb_title class=CDailyCheckList field=object_class}}</th>
          <th>{{mb_title class=CDailyCheckList field=object_id}}</th>
          <th>{{mb_title class=CDailyCheckList field=type}}</th>
          <th>{{mb_title class=CDailyCheckList field=comments}}</th>
          <th>{{mb_title class=CDailyCheckList field=list_type_id}}</th>
          <th>{{mb_title class=CDailyCheckList field=validator_id}}</th>
        </tr>
        {{foreach from=$list_check_lists item=curr_list}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;check_list_id={{$curr_list->_id}}">
              {{mb_value object=$curr_list field=date}}
            </a>
          </td>
          <td>{{mb_value object=$curr_list field=date_validate}}</td>
          <td>
            {{if $curr_list->list_type_id}}
              {{mb_value object=$curr_list->_ref_list_type field=type}}
            {{else}}
              {{mb_value object=$curr_list field=object_class}}
            {{/if}}
          </td>
          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_list->_ref_object->_guid}}')">
              {{$curr_list->_ref_object}}
            </span>
          </td>
          <td>{{mb_value object=$curr_list field=type}}</td>
          <td>{{mb_value object=$curr_list field=comments}}</td>
          <td>{{mb_value object=$curr_list field=list_type_id}}</td>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_list->_ref_validator}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="10" class="empty">{{tr}}CDailyCheckList.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    {{if $check_list->_id}}
    <td>
      <table class="main form">
        <tr>
          <th class="title" colspan="2">
            {{mb_include module=system template=inc_object_history object=$check_list}}
            {{$check_list}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=date}}</th>
          <td>{{mb_value object=$check_list field=date}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=object_id}}</th>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$check_list->_ref_object->_guid}}')">
              {{$check_list->_ref_object}}
            </span>
          </td>
        </tr>
        {{if $check_list->list_type_id}}
          <tr>
            <th>{{mb_label object=$check_list field=list_type_id}}</th>
            <td>{{mb_value object=$check_list field=list_type_id tooltip=true}}</td>
          </tr>
        {{else}}
          <tr>
            <th>{{mb_label object=$check_list field=type}}</th>
            <td>{{mb_value object=$check_list field=type}}</td>
          </tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$check_list field=validator_id}}</th>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$check_list->_ref_validator}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=date_validate}}</th>
          <td>{{mb_value object=$check_list field=date_validate}}</td>
        </tr>
        <tr>
          <td colspan="2" style="padding: 0;">
            <table class="main">
              {{assign var=category_id value=0}}
              {{foreach from=$check_list->_back.items item=_item}}
                {{assign var=curr_type value=$_item->_ref_item_type}}
                {{if $curr_type->category_id != $category_id}}
                  <tr>
                    <th colspan="3" class="text category" style="text-align: left;">
                      <strong>{{$curr_type->_ref_category->title}}</strong>
                      {{if $curr_type->_ref_category->desc}}
                        &ndash; {{$curr_type->_ref_category->desc}}
                      {{/if}}
                    </th>
                  </tr>
                {{/if}}
                <tr>
                  <td style="padding-left: 1em; width: 100%;" class="text" colspan="2">
                    {{mb_value object=$curr_type field=title}}
                    <small style="text-indent: 1em; color: #666;">{{mb_value object=$curr_type field=desc}}</small>
                  </td>
                  <td class="text" {{if $_item->checked == "no" && $curr_type->default_value == "yes" || $_item->checked == "yes" && $curr_type->default_value == "no"}}style="color: red; font-weight: bold;"{{/if}}>
                    {{$_item->getAnswer()}}
                  </td>
                </tr>
                {{assign var=category_id value=$curr_type->category_id}}
              {{foreachelse}}
                <tr>
                  <td colspan="3" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
                </tr>
              {{/foreach}}
              <tr>
                <td colspan="3">
                  <strong>Commentaires:</strong><br />
                  {{mb_value object=$check_list field=comments}}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    {{/if}}
  </tr>
</table>
