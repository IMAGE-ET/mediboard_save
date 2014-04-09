{{if $_spec->typeEnum == "select"}}
  <div style="{{$_style}} display: inline-block; padding: 1px 3px;" defaultstyle="1">
    {{if $show_label}}
      <table class="main layout">
        <tr>
          <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
          <td>
            {{if $ex_class->pixel_positionning}}
              {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
            {{/if}}
            {{$ex_field->prefix}}
            {{mb_field
              object=$ex_object
              field=$_field_name
              form=$form
              emptyLabel=" "
              readonly=$field_readonly
              tabindex=$ex_field->tab_index
            }}
            {{$ex_field->suffix}}
          </td>
        </tr>
      </table>
    {{else}}
      {{if $ex_class->pixel_positionning}}
        {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
      {{/if}}
      {{$ex_field->prefix}}
      {{mb_field
        object=$ex_object
        field=$_field_name
        form=$form
        emptyLabel=" "
        readonly=$field_readonly
        tabindex=$ex_field->tab_index}}
      {{$ex_field->suffix}}
    {{/if}}
  </div>
{{else}}
  {{assign var=_field_class value=""}}
  {{if !$show_label}}
    {{assign var=_field_class value="no-label"}}
  {{/if}}

  <fieldset style="{{$_style}}" defaultstyle="1" class="{{$_field_class}}">
    {{if $show_label}}
      <legend>
        {{mb_label object=$ex_object field=$_field_name}}
        {{if $ex_class->pixel_positionning}}
          {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
        {{/if}}
      </legend>
    {{/if}}
    <div class="wrapper {{if $_spec instanceof CEnumSpec && $_spec->columns > 1}} columns-{{$_spec->columns}} {{/if}}" >
      {{$ex_field->prefix}}
      {{mb_field
        object=$ex_object
        field=$_field_name
        form=$form
        readonly=$field_readonly
        tabindex=$ex_field->tab_index}}
      {{$ex_field->suffix}}
    </div>
  </fieldset>
{{/if}}
