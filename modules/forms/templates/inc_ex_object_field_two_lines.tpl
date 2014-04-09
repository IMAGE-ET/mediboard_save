{{assign var=_field_class value="no-label"}}
{{if $ex_class->pixel_positionning}}
  {{assign var=_field_class value="noresize"}}
{{/if}}

{{if $show_label}}
<div class="input-label">
  {{mb_label object=$ex_object field=$_field_name}}
  {{if $ex_class->pixel_positionning}}
    {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
  {{/if}}
</div>
<div style="position: absolute; top: 1.2em; left: 0; right: 0; bottom: 0;">
  {{$ex_field->prefix}}
  {{mb_field
    object=$ex_object
    field=$_field_name
    form=$form
    emptyLabel=" "
    style=$_style
    defaultstyle=1
    class=$_field_class
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
  {{mb_field
    object=$ex_object
    field=$_field_name
    form=$form
    emptyLabel=" "
    style=$_style
    defaultstyle=1
    class=$_field_class
    readonly=$field_readonly
    tabindex=$ex_field->tab_index
  }}
  {{$ex_field->suffix}}
{{/if}}
