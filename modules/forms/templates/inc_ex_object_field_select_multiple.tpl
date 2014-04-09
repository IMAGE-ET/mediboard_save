{{if $show_label}}
<div class="input-label">{{mb_label object=$ex_object field=$_field_name}}</div>
<div style="position: absolute; top: 1.27em; left: 0; right: 0; bottom: 0;">
  {{if $ex_class->pixel_positionning}}
    {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
  {{/if}}
  {{$ex_field->prefix}}
  {{mb_field
    object=$ex_object
    field=$_field_name
    form=$form
    emptyLabel=" "
    style=$_style
    defaultstyle=1
    readonly=$field_readonly
    tabindex=$ex_field->tab_index
  }}
  {{$ex_field->suffix}}
</div>
{{else}}
  {{if $ex_class->pixel_positionning}}
    {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
  {{/if}}
  {{$ex_field->prefix}}
  <div style="position: relative; display: inline-block; width: 100%; height: 100%;">
    {{mb_field
      object=$ex_object
      field=$_field_name
      form=$form
      emptyLabel=" "
      style=$_style
      defaultstyle=1
      readonly=$field_readonly
      tabindex=$ex_field->tab_index
    }}
  </div>
  {{$ex_field->suffix}}
{{/if}}
