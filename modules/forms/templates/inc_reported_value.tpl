{{assign var=field_name value=$ex_field->name}}

{{if $ex_field->report_class}}
  {{if $ex_object->_id}}
    <img src="./images/icons/reported.png" title="Valeur report�e ({{tr}}{{$ex_field->report_class}}{{/tr}})" />
  {{else}}
    {{assign var=reported_from value=null}}
    {{if array_key_exists($field_name, $ex_object->_reported_fields)}}
      {{assign var=reported_from value=$ex_object->_reported_fields.$field_name}}
    {{/if}}
    
    {{if $reported_from}}
      {{if $reported_from instanceof CExObject}}
        <img class="reported-icon" src="./images/icons/reported.png" style="outline: 0 solid green; background: #7f7;"
             title="Valeur report�e depuis {{$reported_from->_ref_ex_class->name}}&#10;{{mb_value object=$reported_from field=datetime_create}}&#10;{{$reported_from->_ref_object}}"  />
      {{else}}
        <img class="reported-icon" src="./images/icons/reported.png" style="outline: 0 solid blue; background: #77f;"
             title="Valeur report�e depuis {{$reported_from->_view}}"  />
      {{/if}}
    {{else}}
      <img class="reported-icon opacity-50" src="./images/icons/reported.png" title="Valeur non report�e" />
    {{/if}}
  {{/if}}
{{/if}}