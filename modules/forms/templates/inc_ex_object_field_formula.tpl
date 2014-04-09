{{if $show_label}}
  <table class="main layout">
    <tr>
      <td class="narrow input-label">{{mb_label object=$ex_object field=$_field_name}}</td>
      <td class="narrow">
        {{if $ex_class->pixel_positionning}}
          {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
        {{/if}}
      </td>
      <td style="white-space: nowrap;">
        {{$ex_field->prefix}}
        {{mb_field
          object=$ex_object
          field=$_field_name
          readonly=true
          style="font-weight: bold; background-color: #aaff56; $_style"
          class="noresize"
          rows=5
          title=$ex_field->_formula
          defaultstyle=1
        }}
        {{$ex_field->suffix}}
      </td>
      <td class="narrow">
        <button type="button" class="cancel notext" style="margin-left: -1px;" onclick="$V($(this).previous(),'')">
          Vider
        </button>
      </td>
    </tr>
  </table>
{{else}}
  <div style="white-space: nowrap;">
    {{if $ex_class->pixel_positionning}}
      {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$ex_field}}
    {{/if}}
    {{$ex_field->prefix}}
    {{mb_field
      object=$ex_object
      field=$_field_name
      readonly=true
      style="font-weight: bold; background-color: #aaff56; $_style"
      class="noresize"
      rows=5
      title=$ex_field->_formula
      defaultstyle=1
    }}
    {{$ex_field->suffix}}
    <button type="button" class="cancel notext" style="margin-left: -1px;" onclick="$V($(this).previous(),'')">
      Vider
    </button>
  </div>
{{/if}}